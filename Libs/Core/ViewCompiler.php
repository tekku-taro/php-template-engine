<?php
namespace Taro\PageMaker\Core;

use ErrorException;
use Taro\PageMaker\Core\ICompiler;
use Taro\PageMaker\Utility\File;

// コンパイラ
class ViewCompiler implements ICompiler
{
    // 作成されたファイルデータ
    public $compiled = ['parent'=>null, 'content'=>null];

    // 継承の上限
    private $maxLevel = 5;
    private $minLevel = -5;

    private $extendLevel = 0;
    private $includeLevel = 0;

    // 雛形からファイルデータを作成
    public function run($templatePath, array $data)
    {
        $template = $this->loadTemplate($templatePath);
        ["content"=>$content,"parent" => $parent] = $this->compile($template);
        return $content;
    }


    /**
     * Undocumented function
     *
     * @param string $template
     * @return array ["content"=>$content,"parent" => $parent]
     */
    private function compile($template)
    {
        // 雛形データの書き換え
        $compiled = $this->convert($template);
        // 継承元があれば、$data[content]に保存
        if (isset($compiled['parent'])) {
            $content = $compiled['content'];
            // 継承元ファイルの処理
            $this->extendLevel += 1;
            $compiled = $this->compileParentFile($compiled['parent'], $content);
            $this->extendLevel -= 1;
        }
        // コンパイルデータを返す
        return $compiled;
    }

    // 雛形データの書き換え
    private function convert($template)
    {
        $template = $this->convIncludes($template);

        $template = $this->convFors($template);
        
        $template = $this->convForeachs($template);

        $template = $this->convWhiles($template);
        
        $template = $this->convIfs($template);
        
        $template = $this->convVars($template);
        
        ['content'=>$template, 'parent'=>$parent] = $this->getParent($template);

        return ['content'=>$template, 'parent'=>$parent];
    }

    // 継承元ファイルの処理
    private function compileParentFile($parent, $content)
    {
        if ($this->extendLevel > $this->maxLevel) {
            throw new ErrorException('継承は5回までが上限です。');
        }

        // 継承元ファイルのコンパイル
        $template = $this->loadTemplate($parent);
        ["content"=>$parentContent,"parent" => $parent] = $this->compile($template);

        return ["content"=> $this->placeContent($parentContent, $content),"parent" => $parent];
    }

    private function placeContent($template, $content)
    {
        return preg_replace("/@content\s+?/", $content, $template);
    }

    // ファイル読込
    private function loadTemplate($templatePath)
    {
        $fullPath = File::buildTemplatePath($templatePath);
        if (File::exists($fullPath)) {
            $data = file_get_contents($fullPath);
            if ($data) {
                return $data;
            }
        } else {
            throw new ErrorException('ファイル: '.$templatePath.' が見つかりません。');
        }
    }

    // 変数の変換
    private function convVars($template)
    {
        $pattern = "/\[\[\s*(.*?)\s*\]\]/";

        $beginMatches = $this->getMatchedBlocks($template, $pattern);

        if (empty($beginMatches[0])) {
            return $template;
        }
        for ($i=0; $i < count($beginMatches[0]); $i++) {
            $printVar = '<?= ' . trim($beginMatches[1][$i]) . ' ?>';

            $template = str_replace($beginMatches[0][$i], $printVar, $template);
        }

        return $template;
    }

    // ifの変換
    private function convIfs($template)
    {
        $begin = "/@if\s*\((.*?)\)/";
        $elseif = "/@elseif\s*\((.*?)\)/";

        $else = "/@else\s+?/";
        $end = "/@endif\s+?/";

        $beginMatches = $this->getMatchedBlocks($template, $begin);
        $elseifMatches = $this->getMatchedBlocks($template, $elseif);
        $elseMatches = $this->getMatchedBlocks($template, $else);
        $endMatches = $this->getMatchedBlocks($template, $end);

        if (empty($beginMatches[0])) {
            return $template;
        }
        for ($i=0; $i < count($beginMatches[0]); $i++) {
            $stmtIf = '<?php if(' . trim($beginMatches[1][$i]) . '): ?>';
            $stmtElseif = '<?php elseif(' . trim($elseifMatches[1][$i]) . '): ?>';

            $template = str_replace($beginMatches[0][$i], $stmtIf, $template);
            $template = str_replace($elseifMatches[0][$i], $stmtElseif, $template);
            $template = str_replace($elseMatches[0][$i], '<?php else: ?>', $template);
            $template = str_replace($endMatches[0][$i], '<?php endif; ?>', $template);
        }

        return $template;
    }

    // forの変換
    private function convFors($template)
    {
        $begin = "/@for\s*\((.*?)\)/";
        $end = "/@endfor\s+?/";

        $beginMatches = $this->getMatchedBlocks($template, $begin);
        $endMatches = $this->getMatchedBlocks($template, $end);

        if (empty($beginMatches[0])) {
            return $template;
        }
        for ($i=0; $i < count($beginMatches[0]); $i++) {
            $stmt = '<?php for(' . trim($beginMatches[1][$i]) . '): ?>';

            $template = str_replace($beginMatches[0][$i], $stmt, $template);
            $template = str_replace($endMatches[0][$i], '<?php endfor; ?>', $template);
        }

        return $template;
    }

    // foreachの変換
    private function convForeachs($template)
    {
        $begin = "/@foreach\s*\((.*?)\)/";
        $end = "/@endforeach\s+?/";

        $beginMatches = $this->getMatchedBlocks($template, $begin);
        $endMatches = $this->getMatchedBlocks($template, $end);

        if (empty($beginMatches[0])) {
            return $template;
        }
        for ($i=0; $i < count($beginMatches[0]); $i++) {
            $stmt = '<?php foreach(' . trim($beginMatches[1][$i]) . '): ?>';

            $template = str_replace($beginMatches[0][$i], $stmt, $template);
            if (!isset($beginMatches[0][$i])) {
                throw new ErrorException('foreachの終わりのendforeachが見つかりません。');
            }
            $template = str_replace($endMatches[0][$i], '<?php endforeach; ?>', $template);
        }

        return $template;
    }

    // whileの変換
    private function convWhiles($template)
    {
        $begin = "/@while\s*\((.*?)\)/";
        $end = "/@endwhile\s+?/";

        $beginMatches = $this->getMatchedBlocks($template, $begin);
        $endMatches = $this->getMatchedBlocks($template, $end);

        if (empty($beginMatches[0])) {
            return $template;
        }
        for ($i=0; $i < count($beginMatches[0]); $i++) {
            $stmt = '<?php while(' . trim($beginMatches[1][$i]) . '): ?>';

            $template = str_replace($beginMatches[0][$i], $stmt, $template);
            $template = str_replace($endMatches[0][$i], '<?php endwhile; ?>', $template);
        }

        return $template;
    }

    // includesの変換
    private function convIncludes($template)
    {
        $pattern = "/\s*@includes\s*\((.*?)\)/";

        $matches = $this->getMatchedBlocks($template, $pattern);
        
        if (empty($matches[0])) {
            return $template;
        }
        for ($i=0; $i < count($matches[0]); $i++) {
            // $filepath = $this->getFilepath($matches[1][$i]);
            $filePath = trim($matches[1][$i]);
            
            // ファイルの読み込み
            // 雛形データ書き換え
            $includedData = $this->loadTemplate($filePath);
            $this->includeLevel -= 1;
            if ($this->includeLevel < $this->minLevel) {
                throw new ErrorException('Includeは5レベルまでが上限です。');
            }
            ["content"=>$content,"parent" => $parent] = $this->compile($includedData);
            if (!empty($parent)) {
                throw new ErrorException('includeされたファイル内でextendsはできません。');
            }

            
            $this->includeLevel += 1;

            // $incBlock = "include '". $filepath ."';";
            $template = str_replace($matches[0][$i], $content, $template);
        }

        return $template;
    }



    // 継承元ファイルのパスと更新後のテンプレートデータを返す
    private function getParent($template)
    {
        $pattern = "/\s*@extends\s*\((.*?)\)/";

        $match = $this->getMatchedBlock($template, $pattern);
        
        if (empty($match)) {
            return ['content' => $template, 'parent'=>null];
        }

        $template = str_replace($match[0], '', $template);

        // parentを返す
        return ['content' => $template, 'parent'=>trim($match[1])];
    }


    /**
     * 対象データを$patternでパターンマッチングした結果全てを返す
     *
     * @param string $pattern
     * @return array $matches
     */
    protected function getMatchedBlocks($template, $pattern)
    {
        preg_match_all($pattern, $template, $matches);

        return $matches;
    }

    /**
     * 対象データを$patternでパターンマッチングした最初の結果を返す
     *
     * @param string $pattern
     * @return array $matches
     */
    protected function getMatchedBlock($template, $pattern)
    {
        if (preg_match($pattern, $template, $matche)) {
            return $matche;
        }
        return null;
    }
}
