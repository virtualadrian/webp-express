<?php

namespace WebPExpress;

include_once __DIR__ . '/../classes/Paths.php';
use \WebPExpress\Paths;

include_once __DIR__ . '/../classes/Messenger.php';
use \WebPExpress\Messenger;

include_once __DIR__ . '/../classes/TestRun.php';
use \WebPExpress\TestRun;

/* helper. Remove dir recursively. No warnings - fails silently
   Set $removeTheDirItself to false if you want to empty the dir
*/
function webpexpress_migrate2_rrmdir($dir, $removeTheDirItself = true) {
    if (@is_dir($dir)) {
        $objects = @scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                $file = $dir . "/" . $object;
                if (@is_dir($file)) {
                    webpexpress_migrate2_rrmdir($file);
                } else {
                    @unlink($file);
                }
            }
        }
        if ($removeTheDirItself) {
            @rmdir($dir);
        }
    }
}

$testResult = TestRun::getConverterStatus();
if ($testResult) {
    $workingConverters = $testResult['workingConverters'];
    if (in_array('imagick', $workingConverters)) {
       webpexpress_migrate2_rrmdir(Paths::getCacheDirAbs(), false);
       Messenger::addMessage(
           'info',
           'WebP Express has emptied the image cache. In previous versions, the imagick converter ' .
              'was generating images in poor quality. This has been fixed. As your system meets the ' .
              'requirements of the imagick converter, it might be that you have been using that. So ' .
              'to be absolutely sure you do not have inferior conversions in the cache dir, it has been emptied.'
       );
    }
    if (in_array('gmagick', $workingConverters)) {
        Messenger::addMessage(
            'info',
            'Good news! WebP Express is now able to use the gmagick extension for conversion - ' .
               'and your server meets the requirements!'
        );
    }
}
update_option('webp-express-migration-version', '2');
