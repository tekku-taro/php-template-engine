<?php
namespace Taro\PageMaker\Core;

use ErrorException;
use Taro\PageMaker\Core\ICompiler;
use Taro\PageMaker\Utility\File;

// コンパイラ
class ViewCompiler implements ICompiler
{
	use ConvDirectivesTrait;

    // 作成されたファイルデータ
    public $compiled = ['parent'=>null, 'content'=>null];

    // 継承の上限
    public $maxLevel = 5;
    public $minLevel = -5;

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
        
        $template = $this->convVarsSanitized($template);
		
        $template = $this->convVars($template);
        
        ['content'=>$template, 'parent'=>$parent] = $this->getParent($template);

        return ['content'=>$template, 'parent'=>$parent];
    }

    // 継承元ファイルの処理
    private function compileParentFile($parent, $content)
    {
        if ($this->extendLevel > $this->maxLevel) {
            throw new ErrorException('継承は'.$this->maxLevel.'回までが上限です。');
        }

        // 継承元ファイルのコンパイル
        $template = $this->loadTemplate($parent);

        // 雛形データの書き換え
        $compiled = $this->convert($template);

		// 継承先のコンテンツを埋め込む
		$compiled['content'] = $this->placeContent($compiled['content'], $content);
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

    private function placeContent($template, $content)
    {
		// return preg_replace("/@content\s+?/", $content, $template);
        return preg_replace("/".Directives::symbol('content')."\s*/", $content, $template);
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

}
