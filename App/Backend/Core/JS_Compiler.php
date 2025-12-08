<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

use JShrink\Minifier;
use Exception;

final class JS_Compiler
{
    private static $root = WEBROOT . "/App/Public/Javascript/";


    public static function initialize(): void
    {
        $bundles = unserialize(JS_BUNDLES);

        foreach ($bundles as $bundleName => $files) {
            self::processBundle($bundleName, $files);
        }
    }

    private static function processBundle(string $bundleName, array $files): void
    {
        $bundleName = self::$root . ensureJSExtension($bundleName);
        $bundleSourceMap = $bundleName . '.map';
        
        $lastCompileTime = file_exists($bundleName) ? filemtime($bundleName) : 0;
        $shouldCompile = false;
        $resolvedFiles = [];

        // Check has there been newer changes since last compilation
        foreach ($files as $file)
        {
            $file = self::$root . ensureJSExtension($file);

            if (!file_exists($file))
            {
                if (PRODUCTION_MODE)
                {
                    debuglog("Failed to load JS script: '{$filename}'", "warning");
                }
                else
                {
                    exit("JS_Compiler Error: Failed to load JS file '{$filename}'.");
                }

                continue;
            }

            $resolvedFiles[] = $file;

            if (filemtime($file) > $lastCompileTime)
            {
                $shouldCompile = true;
            }
        }

        // Early exit if nothing to compile
        if (!$shouldCompile || empty($resolvedFiles))
        {
            return;
        }

        // Build Bundle
        self::buildBundle($resolvedFiles, $bundleName, $bundleSourceMap);
    }

    private static function buildBundle(array $files, string $bundleName, string $bundleSourceMap): void
    {
        $bundleContent = "";
        $mapData = [
            "version" => 3,
            "file" => basename($bundleName),
            "sources" => [],
            "names" => [],
            "mappings" => ""
        ];

        foreach ($files as $filePath)
        {
            $content = file_get_contents($filePath);
            if ($content === false) continue;

            // Normalize path for sourcemap
            $mapData["sources"][] = str_replace(self::$root, "", $filePath);

            try
            {
                $minified = Minifier::minify($content, ['flaggedComments' => false]);
            }
            catch (Exception $e)
            {
                debuglog("Javascript minification error: {$e->getMessage()}", "warning");
            }

            $bundleContent .= $minified . "\n";
        }

        // Add Timestamp
        if (PRINT_COMPILE_DATE_JS)
        {
            $bundleContent .= "/* Generated: " . date(DATE_FORMAT . " " . TIME_FORMAT) . " */\n";
        }

        // Write Source Map Link
        $bundleContent .= "//# sourceMappingURL=" . basename($bundleSourceMap) . "\n";

        // Atomic Write
        file_put_contents($bundleName, $bundleContent);
        file_put_contents($bundleSourceMap, json_encode($mapData, JSON_UNESCAPED_SLASHES));
    }
}
