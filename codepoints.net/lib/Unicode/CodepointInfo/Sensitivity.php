<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Unicode\Codepoint;


enum SENSITIVITY_LEVEL: int {
    case NORMAL = 0;
    case RAISED = 1;
    case HIGH = 2;
    case MAX = 3;
}


/**
 * provide a level of sensitivity as a measure of how to present and handle
 * this codepoint
 *
 * We do not extend CodepointInfo, because we do not set $env in the
 * constructor.
 */
class Sensitivity {

    private array $sensitivity_map = [
        4053 => SENSITIVITY_LEVEL::MAX,
        4054 => SENSITIVITY_LEVEL::MAX,
        4055 => SENSITIVITY_LEVEL::MAX,
        4056 => SENSITIVITY_LEVEL::MAX,
        5827 => SENSITIVITY_LEVEL::HIGH,
        5833 => SENSITIVITY_LEVEL::HIGH,
        5835 => SENSITIVITY_LEVEL::HIGH,
        5839 => SENSITIVITY_LEVEL::HIGH,
        5855 => SENSITIVITY_LEVEL::HIGH,
        5859 => SENSITIVITY_LEVEL::HIGH,
        21325 => SENSITIVITY_LEVEL::MAX,
        21328 => SENSITIVITY_LEVEL::MAX,
        72816 => SENSITIVITY_LEVEL::HIGH,
        128076 => SENSITIVITY_LEVEL::RAISED,
        128328 => SENSITIVITY_LEVEL::RAISED,
    ];

    /**
     * register this info provider
     */
    public function __construct() {
        Codepoint::addInfoProvider('sensitivity', $this);
    }

    /**
     * return the sensitivity level for a code point
     */
    public function __invoke(Codepoint $codepoint) : SENSITIVITY_LEVEL {
        $id = $codepoint->id;
        if (array_key_exists($id, $this->sensitivity_map)) {
            return $this->sensitivity_map[$id];
        }
        return SENSITIVITY_LEVEL::NORMAL;
    }

}
