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

}
