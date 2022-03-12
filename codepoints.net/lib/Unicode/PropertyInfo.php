<?php

namespace Codepoints\Unicode;


/**
 * get Unicode labels, abbreviations and other informative static data
 */
class PropertyInfo {
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
            return array_map(function ($version, $year) {
                return sprintf('%s (%s)', $version, $year);
            }, array_keys($age_to_year), array_values($age_to_year));
        } elseif (property_exists($this, $name)) {
            return $this->__get($name) ?? [];
        } else {
            return [];
        }
    }

}
