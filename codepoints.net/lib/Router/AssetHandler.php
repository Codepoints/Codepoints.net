<?php

namespace Codepoints\Router;

use Analog\Analog;

/**
 * handle asset URLs
 */
class AssetHandler {

    /**
     * @var array<string, array>
     */
    private array $manifest = [];

    public function __construct() {
        $manifest_path = dirname(dirname(__DIR__)).'/static/manifest.json';
        if (! is_file($manifest_path)) {
            Analog::warning('AssetHandler: no manifest found');
            return;
        }
        $this->manifest = json_decode(file_get_contents($manifest_path), true, 512, JSON_THROW_ON_ERROR);
    }

    public function getUrl(string $path): string {
        if (! DEBUG) {
            if (array_key_exists($path, $this->manifest)) {
                return '/static/' . $this->manifest[$path]['file'];
            }
            Analog::warning(sprintf('AssetHandler: asset not found: %s', $path));
        }
        return '/static/' . $path;
    }

}
