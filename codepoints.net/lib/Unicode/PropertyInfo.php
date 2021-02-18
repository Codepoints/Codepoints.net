<?php

namespace Codepoints\Unicode;


/**
 * get Unicode labels, abbreviations and other informative static data
 */
class PropertyInfo {
    private Array $age_to_year;
    private Array $booleans;
    private Array $gc_shortcuts;
    private Array $legend = [];
    private Array $properties;
    private Array $region_to_block;
    private Array $script;
    private Array $script_age;

    public function __construct() {
        $this->legend['NFC_QC'] = [
            'M' => __('Maybe'),
            'N' => __('No'),
            'Y' => __('Yes'),
        ];
        $this->legend['NFD_QC'] = [
            'N' => __('No'),
            'Y' => __('Yes'),
        ];
        $this->legend['NFKC_QC'] = [
            'M' => __('Maybe'),
            'N' => __('No'),
            'Y' => __('Yes'),
        ];
        $this->legend['NFKD_QC'] = [
            'N' => __('No'),
            'Y' => __('Yes'),
        ];
        $this->age_to_year = include(__DIR__.'/PropertyInfo/age_to_year.php');
        $this->booleans = include(__DIR__.'/PropertyInfo/booleans.php');
        $this->gc_shortcuts = include(__DIR__.'/PropertyInfo/gc_shortcuts.php');
        $this->legend['bc'] = include(__DIR__.'/PropertyInfo/legend_bc.php');
        $this->legend['bpt'] = include(__DIR__.'/PropertyInfo/legend_bpt.php');
        $this->legend['ccc'] = include(__DIR__.'/PropertyInfo/legend_ccc.php');
        $this->legend['dt'] = include(__DIR__.'/PropertyInfo/legend_dt.php');
        $this->legend['ea'] = include(__DIR__.'/PropertyInfo/legend_ea.php');
        $this->legend['GCB'] = include(__DIR__.'/PropertyInfo/legend_GCB.php');
        $this->legend['gc'] = include(__DIR__.'/PropertyInfo/legend_gc.php');
        $this->legend['hst'] = include(__DIR__.'/PropertyInfo/legend_hst.php');
        $this->legend['jt'] = include(__DIR__.'/PropertyInfo/legend_jt.php');
        $this->legend['lb'] = include(__DIR__.'/PropertyInfo/legend_lb.php');
        $this->legend['nt'] = include(__DIR__.'/PropertyInfo/legend_nt.php');
        $this->legend['SB'] = include(__DIR__.'/PropertyInfo/legend_SB.php');
        $this->legend['WB'] = include(__DIR__.'/PropertyInfo/legend_WB.php');
        $this->properties = include(__DIR__.'/PropertyInfo/properties.php');
        $this->region_to_block = include(__DIR__.'/PropertyInfo/region_to_block.php');
        $this->script = include(__DIR__.'/PropertyInfo/script.php');
        $this->script_age = include(__DIR__.'/PropertyInfo/script_age.php');
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            return null;
        }
    }

}
