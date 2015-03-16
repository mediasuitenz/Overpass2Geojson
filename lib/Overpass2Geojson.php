<?php

class Overpass2Geojson
{

    public static function convert($input) {
        if (!is_string($input)) {
            return false;
        }
        $inputArray = json_decode($input, true);
        if (!isset($inputArray['elements']) || !is_array($inputArray['elements'])) {
            return false;
        }

        return '';
    }
}
