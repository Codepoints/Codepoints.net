<?php

namespace Codepoints;

use Analog\Analog;
use Gettext\Loader\MoLoader;
use Gettext\Translations;

class CachingMoLoader {

    public function loadFile(string $file) : Translations {
        $file_php = $file . '.php';
        if (! is_file($file_php)) {
            $loader = new MoLoader();
            $translations = $loader->loadFile($file);
            // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
            if (! @file_put_contents($file_php,
                '<?php return unserialize(\'' . serialize($translations) . '\');'."\n")) {
                Analog::log('cannot create file '.$file_php);
            }
        } else {
            $translations = require($file_php);
        }
        return $translations;
    }

}
