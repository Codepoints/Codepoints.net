<?php


/**
 * the path to the cache folder
 */
define('CACHE_FOLDER', realpath(dirname(__FILE__).'/../cache'));


/**
 * a simple caching class to store and receive cached data
 */
class Cache {

    /**
     * the maximum allowed cache size
     */
    public $allowedCacheSize = 100000000; // 100 MB
    //public $allowedCacheSize = 100; // 100 B

    /**
     * write content to the cache
     *
     * If there are relevant GET parameters beside "lang", those must
     * be reflected in the $path parameter!
     */
    public function write($path, $content) {
        if (! $this->_checkSize()) {
            flog(sprintf('Cache is full [%s]', $path));
            return False;
        }
        if (! is_dir(CACHE_FOLDER)) {
            mkdir(CACHE_FOLDER, 0777, True);
        }
        $gz = gzopen($this->_getPath($path), 'w9');
        if ($gz === False) {
            return False;
        }
        gzwrite($gz, $content);
        gzclose($gz);
        return True;
    }

    /**
     * fetch content from the cache
     *
     * returns False, if nothing found or index.php is more recent
     */
    public function fetch($path, $zipped=False) {
        $rpath = $this->_getPath($path);
        if (! is_file($rpath)) {
            return False;
        }
        if (filemtime($rpath) < filemtime(dirname(__FILE__).'/../index.php')) {
            // clear cache, if index.php has changed
            unlink($rpath);
            return False;
        }
        if ($zipped) {
            $r = file_get_contents($rpath);
        } else {
            $r = join('', gzfile($rpath));
        }
        return $r;
    }

    /**
     * compose the path to the cached file
     */
    protected function _getPath($path) {
        $path = $this->_normalize($path);
        $rpath = CACHE_FOLDER."/_cache_".
                    preg_replace('/[^a-zA-Z0-9%&*+,\-.=?_~]+/', "_", $path);
        $test = dirname($rpath);
        if (substr($test, 0, strlen(CACHE_FOLDER)) !== CACHE_FOLDER) {
            // security net: don't allow writing/reading outside the cache folder
            throw new Exception('Cannot use this path for caching');
        }
        return $rpath;
    }

    /**
     * check that the cache folder remains in its limits
     *
     * This function needs `du` and `awk` in their place
     */
    protected function _checkSize() {
        if (! is_dir(CACHE_FOLDER)) {
            return True;
        }
        $size = exec('du -b '.escapeshellarg(CACHE_FOLDER).' | awk "{print \$1}"');
        if ((int)$size > $this->allowedCacheSize) {
            return False;
        }
        return True;
    }

    /**
     * normalize the path, especially to cater GET params
     *
     * @see http://en.wikipedia.org/wiki/URL_normalization
     */
    protected function _normalize($path) {
        // get the file path
        $rpath = current(explode('?', $path, 2));
        // split off the get params
        $params = substr($path, strlen($rpath));
        // we throw away fragment identifiers
        $params = current(explode('#', $params, 2));
        if (strlen($params)) {
            // if there are GET parameters
            $aParams = parse_str($params);
            if (array_key_exists('lang', $aParams)) {
                unset($aParams['lang']); // we handle this param separately
            }
            ksort($aParams);
            $params = '?'.http_build_query($aParams);
            if ($params === '?') {
                // can happen, if ?lang was the only GET param
                $params = '';
            }
        }

        // use Apache-style path extension for language
        $lang = L10n::get('messages')->getLanguage();

        // re-assemble path (the '?' is already part of $params
        $path = $rpath . '.' . $lang . $params;

        // normalize URL encoding
        $path = preg_replace_callback('/%[a-f0-9]{2}/', function($m) {
            return strtoupper($m[0]);
        }, $path);
        foreach (array(
            '%41' => 'A', '%42' => 'B', '%43' => 'C', '%44' => 'D',
            '%45' => 'E', '%46' => 'F', '%47' => 'G', '%48' => 'H',
            '%49' => 'I', '%4A' => 'J', '%4B' => 'K', '%4C' => 'L',
            '%4D' => 'M', '%4E' => 'N', '%4F' => 'O', '%50' => 'P',
            '%51' => 'Q', '%52' => 'R', '%53' => 'S', '%54' => 'T',
            '%55' => 'U', '%56' => 'V', '%57' => 'W', '%58' => 'X',
            '%59' => 'Y', '%5A' => 'Z',
            '%61' => 'a', '%62' => 'b', '%63' => 'c', '%64' => 'd',
            '%65' => 'e', '%66' => 'f', '%67' => 'g', '%68' => 'h',
            '%69' => 'i', '%6A' => 'j', '%6B' => 'k', '%6C' => 'l',
            '%6D' => 'm', '%6E' => 'n', '%6F' => 'o', '%70' => 'p',
            '%71' => 'q', '%72' => 'r', '%73' => 's', '%74' => 't',
            '%75' => 'u', '%76' => 'v', '%77' => 'w', '%78' => 'x',
            '%79' => 'y', '%7A' => 'z',
            '%30' => '0', '%31' => '1', '%32' => '2', '%33' => '3',
            '%34' => '4', '%35' => '5', '%36' => '6', '%37' => '7',
            '%38' => '8', '%39' => '9',
            '%2D' => '-', '%2E' => '.', '%5F' => '_', '%7E' => '~',
        ) as $orig => $repl) {
            $path = str_replace($orig, $repl, $path);
        }

        return $path;
    }

}


//__END__
