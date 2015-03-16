<?php

class Overpass2Geojson
{
    /**
     * Converts a JSON string or decoded array into a GeoJSON string
     * @param  mixed $input JSON string or array
     * @return string       GeoJSON string
     */
    public static function convert($input) {
        if (is_array($input)) {
            $inputArray = $input;
        } else if (is_string($input)) {
            $inputArray = json_decode($input, true);
        } else {
            return false;
        }

        $output = self::convertArray($inputArray);
        return $output !== false ? json_encode($output) : $output;
    }

    /**
     * Converts a JSON string or decoded array into a GeoJSON array
     * @param  mixed $input JSON string or array
     * @return array       Converted to GeoJSON structure
     */
    public static function convertArray($input) {
        if (is_string($input)) {
            $input = json_decode($input, true);
        }
        if (!is_array($input) ||
            !isset($input['elements']) ||
            !is_array($input['elements'])) {

            return false;
        }

        return array();
    }
}
