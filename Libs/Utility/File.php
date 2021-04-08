<?php
namespace Taro\PageMaker\Utility;

// ファイル管理
class File
{
    /**
     * ルートパス
     *
     * @var string
     */
    public static $root = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
    /**
     * キャッシュ保存フォルダ
     *
     * @var string
     */
    public static $cachePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'
	. DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'views';
    /**
     * ファイルの拡張子
     *
     * @var string
     */
    public static $extension = 'php';


    // ファイルの読み込み
    public static function read(string $path)
    {
        if (self::exists($path)) {
            return file_get_contents($path);
        }

        return null;
    }

    // ファイルの書き出し
    public static function save($data, string $fileName = null)
    {
        if (empty($fileName)) {
            $fileName = date('Ymd') . uniqid() . '.php';
        }
        $filePath = self::buildOutputPath($fileName);

        file_put_contents($filePath, $data);

        return $filePath;
    }

    // ファイルチェック
    public static function exists(string $path)
    {
        if (file_exists($path)) {
            return true;
        }

        return false;
    }

    // パスの作成
    public static function buildPath(string $basePath, string  $path)
    {
        if (empty($basePath)) {
            $basePath = DIRECTORY_SEPARATOR;
        }
        return rtrim(realpath($basePath), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    // 雛形パスの作成
    public static function buildTemplatePath(string  $path)
    {
        $templatePath = self::$root  . DIRECTORY_SEPARATOR . 'templates';
        return self::buildPath($templatePath, self::appendExtension($path));
    }

    // 出力パスの作成
    public static function buildOutputPath(string  $path)
    {
        $outputPath = self::$root  . DIRECTORY_SEPARATOR . 'outputs';
        return self::buildPath($outputPath, self::appendExtension($path));
    }

    public static function appendExtension($path, $extension = null)
    {
        if (empty($extension)) {
            $extension = self::$extension;
        }
        if (strpos($path, $extension) === false) {
            $path .= '.' . $extension;
        }

        return $path;
    }

    // キャッシュの作成
    public static function saveCache($fileName, $content)
    {
		$path = self::buildPath(self::$cachePath, self::cacheName($fileName) );
		return file_put_contents($path, $content);
    }

    // キャッシュのロード
    public static function loadCache($fileName)
    {
		if(($path = self::checkCache($fileName)) !== false) {
			return file_get_contents($path);
		}

		return false;
    }

    // キャッシュのチェック
    private static function checkCache($fileName)
    {
		$path = self::buildPath(self::$cachePath, self::cacheName($fileName) );
		if(file_exists($path)) {
			return $path;
		}
		return false;
    }

    // キャッシュファイル名の作成
    private static function cacheName($fileName)
    {
		return hash( "sha256", $fileName);
    }
}
