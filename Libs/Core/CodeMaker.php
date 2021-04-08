<?php
namespace Taro\PageMaker\Core;

use ErrorException;
use Taro\PageMaker\Core\ICompiler;
use Taro\PageMaker\Utility\File;

// コンパイラ
class CodeMaker implements ICompiler
{
	use ConvDirectivesTrait;

    // 作成されたファイルデータ
    public $compiled = ['parent'=>null, 'content'=>null];

    // 継承の上限
    private $maxLevel = 5;
    private $minLevel = -5;

    private $extendLevel = 0;
    private $includeLevel = 0;

    // 埋め込む変数データ
    private $data;


    // 雛形からファイルデータを作成
    public function run($templatePath, array $data)
    {
        $this->data = $data;
        $template = $this->loadTemplate($templatePath);
        ["content"=>$content,"parent" => $parent] = $this->compile($template);

        $evalContent = $this->executeContent($content);

        return $this->enablePHPCode($evalContent);
    }
 
 
    /**
     * 雛形データをコンパイルして返す
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
        $template = $this->disablePHPCode($template);

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
		// return preg_replace("/@content\s+?/", $content, $template);
        return preg_replace("/".Directives::symbol('content')."\s+?/", $content, $template);
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
 

    /**
     * 対象データ内のphpタグをプレースホルダーで置き換える
     *
     * @return string $template
     */
    protected function disablePHPCode($template)
    {
        $template = str_replace("<?php", "TRATSEDOC", $template);
        $template = str_replace("?>", "DNEEDOC", $template);
        return $template;
    }

    /**
     * 対象データ内のプレースホルダーをphpタグに戻す
     *
     * @return string $template
     */
    protected function enablePHPCode($template)
    {
        $template = str_replace("TRATSEDOC", "<?php", $template);
        $template = str_replace("DNEEDOC", "?>", $template);
        return $template;
    }

    /**
     * $contentのphpコードを実行して、結果を返す
     *
     * @param string $content
     * @return string
     */
    protected function executeContent($content)
    {
        if (!empty($this->data)) {
            extract($this->data);
        }
        ob_start();
        eval('?>' . $content);
        return ob_get_clean();
    }

}
