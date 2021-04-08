<?php
namespace Taro\PageMaker\Core;

use ErrorException;
use Taro\PageMaker\Core\Directives;

trait ConvDirectivesTrait {


    // 変数の変換
    private function convVars($template)
    {
		// $pattern = "/\[\[\s*(.*?)\s*\]\]/";
        $pattern = "/".Directives::symbol('var.begin')."\s*(.*?)\s*".Directives::symbol('var.end')."/";

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
        // $begin = "/@if\s*\((.*?)\)/";
        $begin = "/".Directives::symbol('if.begin')."\s*".Directives::symbol('if.condbegin')."(.*?)".Directives::symbol('if.condend')."/";
        // $elseif = "/@elseif\s*\((.*?)\)/";
        $elseif = "/".Directives::symbol('elseif.begin')."\s*".Directives::symbol('elseif.condbegin')."(.*?)".Directives::symbol('elseif.condend')."/";
        // $else = "/@else\s+?/";
        $else = "/".Directives::symbol('if.else')."\s+?/";		
        // $end = "/@endif\s+?/";
        $end = "/".Directives::symbol('if.end')."\s+?/";		

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
        // $begin = "/@for\s*\((.*?)\)/";
        $begin = "/".Directives::symbol('for.begin')."\s*".Directives::symbol('for.condbegin')."(.*?)".Directives::symbol('for.condend')."/";
        // $end = "/@endfor\s+?/";		
        $end = "/".Directives::symbol('for.end')."\s+?/";

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
		// $begin = "/@foreach\s*\((.*?)\)/";
        $begin = "/".Directives::symbol('foreach.begin')."\s*".Directives::symbol('foreach.condbegin')."(.*?)".Directives::symbol('foreach.condend')."/";
        // $end = "/@endforeach\s+?/";
        $end = "/".Directives::symbol('foreach.end')."\s+?/";

        $beginMatches = $this->getMatchedBlocks($template, $begin);
        $endMatches = $this->getMatchedBlocks($template, $end);

        if (empty($beginMatches[0])) {
            return $template;
        }
        for ($i=0; $i < count($beginMatches[0]); $i++) {
            $stmt = '<?php foreach(' . trim($beginMatches[1][$i]) . '): ?>';

            $template = str_replace($beginMatches[0][$i], $stmt, $template);
            if (!isset($endMatches[0][$i])) {
                throw new ErrorException('foreachの終わりのendforeachが見つかりません。');
            }
            $template = str_replace($endMatches[0][$i], '<?php endforeach; ?>', $template);
        }

        return $template;
    }

    // whileの変換
    private function convWhiles($template)
    {
        // $begin = "/@while\s*\((.*?)\)/";
        $begin = "/".Directives::symbol('while.begin')."\s*".Directives::symbol('while.condbegin')."(.*?)".Directives::symbol('while.condend')."/";
        // $end = "/@endwhile\s+?/";		
        $end = "/".Directives::symbol('while.end')."\s+?/";

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
        // $pattern = "/\s*@includes\s*\((.*?)\)/";
        $pattern = "/".Directives::symbol('includes.begin')."\s*".Directives::symbol('includes.condbegin')."(.*?)".Directives::symbol('includes.condend')."/";

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
        // $pattern = "/\s*@extends\s*\((.*?)\)/";
        $pattern = "/".Directives::symbol('extends.begin')."\s*".Directives::symbol('extends.condbegin')."(.*?)".Directives::symbol('extends.condend')."/";


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