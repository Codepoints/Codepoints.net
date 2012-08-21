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
        $rpath = CACHE_FOLDER."/_cache_".
                    preg_replace('/[^a-zA-Z0-9$%&()*+,\-.=?_~]+/', "_", $path);
        $lang = L10n::get('messages')->getLanguage();
        // use Apache-style path extension for language
        $rpath .= '.'.$lang;
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

}


//__END__
