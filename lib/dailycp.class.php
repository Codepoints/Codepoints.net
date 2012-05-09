<?php


/**
 * get a daily Codepoint
 */
class DailyCP {

    /**
     * fetch the date's codepoint from the JSON db
     */
    protected static function _get($date) {
        $data = json_decode(file_get_contents(
                    dirname(__FILE__).'/ucotd.json'), True);
        if (array_key_exists($date, $data)) {
            if ($data[$date][0] !== '0000') {
                return $data[$date];
            }
        }
        return Null;
    }

    /**
     * get the codepoint and additional data for a date
     */
    public static function get($date, $db) {
        $data = self::_get($date);
        if ($data) {
            $data = array(
                Codepoint::getCP($data[0], $db),
                $data[1], $data[2]
            );
        }
        return $data;
    }

}


//__END__
