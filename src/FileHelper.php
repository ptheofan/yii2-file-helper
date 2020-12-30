<?php /** @noinspection PhpIllegalPsrClassPathInspection */
namespace yii\helpers;

/**
 * Class FileHelper
 * @package common\helpers
 */
class FileHelper extends BaseFileHelper
{
    /**
     * Create a unique temp dir
     *
     * @param string|null $basePath
     * @param string|null $prefix
     * @return string
     */
    public static function tempDir(?string $basePath = null, ?string $prefix = null): string
    {
        if ($prefix === null) {
            $prefix = '';
        }

        $rVal = tempnam($basePath ?: sys_get_temp_dir(), $prefix);
        if ($rVal && file_exists($rVal)) {
            unlink($rVal);
        }

        if (!mkdir($rVal) && !is_dir($rVal)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $rVal));
        }

        return $rVal;
    }

    /**
     * Create a unique temp file based on another file (keep ext)
     * Will only generate the full-path/name, will not copy the contents
     *
     * @param string $file
     * @param string|null $prefix
     * @return string
     */
    public static function tempFileFrom(string $file, ?string $prefix = null): string
    {
        $info = pathinfo($file);
        return self::tempFile($prefix, $info['extension']);
    }

    /**
     * Create a temp. file
     *
     * @param string|null $prefix
     * @param string|null $ext
     * @return string
     */
    public static function tempFile(?string $prefix = null, ?string $ext = null): string
    {
        $rVal = tempnam(sys_get_temp_dir(), $prefix);
        if (!$rVal) {
            throw new \RuntimeException("Could not generate temporary file (prefix = {$prefix}, $ext = {$ext})");
        }

        if ($ext) {
            try {
                rename($rVal, "$rVal.{$ext}");
                $rVal .= ".{$ext}";
            } catch (\Exception $e) {
                throw new \RuntimeException("Generating tmp file - could not rename {$rVal} to {$rVal}.{$ext}");
            }
        }

        return $rVal;
    }

    /**
     * Create a temp dir
     *
     * @param string|null $filename
     * @return string
     */
    public static function sysTempDir(?string $filename = null): string
    {
        if ($filename) {
            return sprintf('%s/%s', rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR), ltrim($filename, DIRECTORY_SEPARATOR));
        }

        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
    }

    /**
     * Create a unique filename similar to $filename by adding suffix -n
     *
     * @param $filename - in the format /path/to/file.ext | /path/to/file-1.ext | /path/to/file-2.ext | ...
     * @return string
     */
    public static function uniqueFilename(string $filename): ?string
    {
        if (!file_exists($filename)) {
            return $filename;
        }

        $info = pathinfo($filename);
        $dirname = $info['dirname'];
        $filename = $info['filename'];
        $ext = $info['extension'];

        $i = 0;
        while(true)
        {
            $i++;
            $rVal = "{$dirname}/{$filename}-{$i}.{$ext}";
            if (!file_exists($rVal)) {
                return $rVal;
            }
        }
    }
}
