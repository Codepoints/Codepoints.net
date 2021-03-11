<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\Runner;
use Codepoints\Api\Exception as ApiException;


class Property extends Runner {

    private int $colormod = 0;

    private const FIELDS = [
        'cp', 'age', 'gc', 'ccc', 'bc', 'Bidi_M', 'Bidi_C', 'dt', 'CE',
        'Comp_Ex', 'NFC_QC', 'NFD_QC', 'NFKC_QC', 'NFKD_QC', 'XO_NFC',
        'XO_NFD', 'XO_NFKC', 'XO_NFKD', 'nt', 'nv', 'jt', 'jg', 'Join_C',
        'lb', 'ea', 'Upper', 'Lower', 'OUpper', 'OLower', 'CI', 'Cased',
        'CWCF', 'CWCM', 'CWL', 'CWKCF', 'CWT', 'CWU', 'hst', 'JSN', 'IDS',
        'OIDS', 'XIDS', 'IDC', 'OIDC', 'XIDC', 'Pat_Syn', 'Pat_WS', 'Dash',
        'Hyphen', 'QMark', 'Term', 'STerm', 'Dia', 'Ext', 'SD', 'Alpha',
        'OAlpha', 'Math', 'OMath', 'Hex', 'AHex', 'DI', 'ODI', 'LOE',
        'WSpace', 'Gr_Base', 'Gr_Ext', 'OGr_Ext', 'Gr_Link', 'GCB', 'WB',
        'SB', 'Ideo', 'UIdeo', 'IDSB', 'IDST', 'Radical', 'Dep', 'VS',
        'NChar', 'kTotalStrokes', 'blk', 'scx', 'sc', 'confusables', 'block',
    ];

    /**
     * render a PNG where each pixel represents a code point
     */
    public function handle(string $data) : string {
        $width = 256;
        $height = 256 * 3; // three planes high

        if ($data === 'block') {
            $data = 'blk';
        }
        if (! in_array($data, self::FIELDS)) {
            header('Content-Type: application/json;charset=UTF-8');
            throw new ApiException(json_encode([
                'title' => $data ? __('Unknown property') : __('Please specify a property to display'),
                'detail' => __('show a PNG image where every codepoint is represented by one pixel. The pixel color determines the value.'),
                'property_url' => 'https://codepoints.net/api/v1/property/{property}',
                'properties' => self::FIELDS,
            ]), ApiException::BAD_REQUEST);
        }

        $gd = imagecreatetruecolor($width, $height);
        imagecolortransparent($gd, imagecolorallocate($gd, 0, 0, 0));

        switch ($data) {
            case 'confusables':
                $query = 'SELECT cp, COUNT(\'other\') AS Q FROM codepoint_confusables
                    WHERE cp < 196608
                    GROUP BY cp';
                break;
            case 'sc':
                $query = 'SELECT cp, sc AS Q FROM codepoint_script
                    WHERE cp < 196608
                    AND `primary` = 1';
                break;
            case 'scx':
                $query = 'SELECT cp, GROUP_CONCAT(sc SEPARATOR \' \') AS Q FROM codepoint_script
                    WHERE cp < 196608
                    AND `primary` = 0
                GROUP BY cp';
                break;
            default:
                $query = 'SELECT cp, '.$data.' AS Q FROM codepoint_props
                    WHERE cp < 196608';
                            // 196608 == 0x30000 (everything up to the third plane)
                break;
        }
        $result = $this->env['db']->getAll($query);

        $coloroptions = [
            'frequency1' => 1.666,
            'frequency2' => 2.666,
            'frequency3' => 3.666,
            'phase1' => 0,
            'phase2' => 2,
            'phase3' => 4,
        ];
        if ($data === 'age') {
            $coloroptions = [
                'frequency1' => .2,
                'frequency2' => .2,
                'frequency3' => .2,
                'phase1' => 1.6,
                'phase2' => -0.6,
                'phase3' => 4.0,
            ];
        }
        $colors = [];

        foreach ($result as $cp) {
            $_x = $cp['cp'] % $width;
            $_y = (int)floor($cp['cp'] / $width);
            if ($data === 'cp') {
                // codepoints are only checked for existance. There's no use coloring
                // each CP in an individual color
                $cp['Q'] = '1';
            }
            if (! array_key_exists($cp['Q'], $colors)) {
                $rgb = call_user_func_array([$this, 'getNextColor'], $coloroptions);
                array_unshift($rgb, $gd);
                $colors[$cp['Q']] = call_user_func_array('imagecolorallocate', $rgb);
            }
            imagesetpixel($gd, $_x, $_y, $colors[$cp['Q']]);
        }

        header('Content-Type: image/png');

        ob_start();
        imagepng($gd);
        imagedestroy($gd);
        return ob_get_clean();
    }

    /**
     * a continuous color generator for usage in the API
     *
     * @see http://krazydad.com/tutorials/makecolors.php for the maths
     */
    private function getNextColor($frequency1, $frequency2, $frequency3,
        $phase1, $phase2, $phase3) : Array {
        $center = 128;
        $width = 127;

        $red = min(255, max(0, round( sin($frequency1*$this->colormod + $phase1) * $width + $center)));
        $grn = min(255, max(0, round( sin($frequency2*$this->colormod + $phase2) * $width + $center)));
        $blu = min(255, max(0, round( sin($frequency3*$this->colormod + $phase3) * $width + $center)));
        $this->colormod += 1;
        return [$red, $grn, $blu];
    }

}
