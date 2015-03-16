<?php

class Overpass2Geojson
{
    /**
     * Converts a JSON string or decoded array into a GeoJSON string or array
     * @param  mixed   $input  JSON string or array
     * @param  boolean $encode whether to encode output as string
     * @return mixed           false if failed, otherwise GeoJSON string or array
     */
    public static function convert($input, $encode=true) {
        if (is_array($input)) {
            $inputArray = $input;
        } else if (is_string($input)) {
            $inputArray = json_decode($input, true);
        } else {
            return false;
        }
        if (!is_array($inputArray) ||
            !isset($inputArray['elements']) ||
            !is_array($inputArray['elements'])) {

            return false;
        }

        $output = array(
            'type' => 'FeatureCollection',
        );

        return $encode ? json_encode($output) : $output;
    }
}
