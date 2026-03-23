<?php

namespace Codepoints\Unicode;


/**
 * get Unicode labels, abbreviations and other informative static data
 *
 * @property-read Array $age_to_year
 * @property-read Array $booleans
 * @property-read Array $gc_shortcuts
 * @property-read Array $properties
 * @property-read Array $region_to_block
 * @property-read Array $script
 * @property-read Array $script_age
 * @property-read Array $legend_bc
 * @property-read Array $legend_bpt
 * @property-read Array $legend_ccc
 * @property-read Array $legend_dt
 * @property-read Array $legend_ea
 * @property-read Array $legend_GCB
 * @property-read Array $legend_gc
 * @property-read Array $legend_hst
 * @property-read Array $legend_jt
 * @property-read Array $legend_lb
 * @property-read Array $legend_nt
 * @property-read Array $legend_SB
 * @property-read Array $legend_WB
 * @property-read Array $legend_NFC_QC
 * @property-read Array $legend_NFD_QC
 * @property-read Array $legend_NFKC_QC
 * @property-read Array $legend_NFKD_QC
 */
final class PropertyInfo {
    private ?Array $age_to_year = null;
    private ?Array $booleans = null;
    private ?Array $gc_shortcuts = null;
    private ?Array $properties = null;
    private ?Array $region_to_block = null;
    private ?Array $script = null;
    private ?Array $script_age = null;
    private ?Array $legend_bc = null;
    private ?Array $legend_bpt = null;
    private ?Array $legend_ccc = null;
    private ?Array $legend_dt = null;
    private ?Array $legend_ea = null;
    private ?Array $legend_GCB = null;
    private ?Array $legend_gc = null;
    private ?Array $legend_hst = null;
    private ?Array $legend_jt = null;
    private ?Array $legend_lb = null;
    private ?Array $legend_nt = null;
    private ?Array $legend_SB = null;
    private ?Array $legend_WB = null;
    private ?Array $legend_NFC_QC = null;
    private ?Array $legend_NFD_QC = null;
    private ?Array $legend_NFKC_QC = null;
    private ?Array $legend_NFKD_QC = null;

    /**
     * we load most properties lazily, because this class is otherwise the
     * major performance stumbling block in the whole app.
     */
    public function __construct() {
        $this->legend_NFC_QC = [
            'M' => __('Maybe'),
            'N' => __('No'),
            'Y' => __('Yes'),
        ];
        $this->legend_NFD_QC = [
            'N' => __('No'),
            'Y' => __('Yes'),
        ];
        $this->legend_NFKC_QC = [
            'M' => __('Maybe'),
            'N' => __('No'),
            'Y' => __('Yes'),
        ];
        $this->legend_NFKD_QC = [
            'N' => __('No'),
            'Y' => __('Yes'),
        ];
    }

    /**
     * get the requested info
     *
     * If the info is not yet loaded, fetch it from ./PropertyInfo/.
     *
     * @psalm-suppress UnresolvableInclude
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (property_exists($this, $name)) {
            if ($this->$name === null) {
                $this->$name = include(__DIR__.'/PropertyInfo/'.$name.'.php');
            }
            return $this->$name;
        } else {
            return null;
        }
    }

    /**
     * get the legend for a single property value
     *
     * @param string $prop
     * @param string $key
     * @return string
     */
    public function getLegend(string $prop, string $key) {
        $legend = $this->getLegends($prop);
        if (array_key_exists($key, $legend)) {
            if (is_array($legend[$key])) {
                return $legend[$key][0];
            }
            return $legend[$key];
        }
        return $key;
    }

    /**
     * get the legend array for a certain unicode property, if it exists
     *
     * The properties age and sc are special-cased. This method cannot be used
     * with the blk property.
     *
     * @param string $prop
     * @return array
     */
    public function getLegends(string $prop) {
        $name = $prop === 'sc'? 'script' : 'legend_'.$prop;
        if ($name === 'legend_age') {
            $age_to_year = $this->__get('age_to_year');
            $ret = [];
            foreach ($age_to_year as $version => $year) {
                $ret[$version] = sprintf('%s (%s)', $version, $year);
            }
            return $ret;
        } elseif (property_exists($this, $name)) {
            return $this->__get($name) ?? [];
        } else {
            return [];
        }
    }

}
