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
        $inputArray = self::validateInput($input);
        return $inputArray !== false ? self::doConversion($inputArray, $encode) : false;
    }

    private static function validateInput($input) {
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
        return $inputArray;
    }

    private static function doConversion($input, $encode) {
        $output = array(
            'type' => 'FeatureCollection',
            'features' => array(),
        );

        $nodes = self::collectNodes($input['elements']);

        foreach ($input['elements'] as $osmItem) {
            if (isset($osmItem['type']) && $osmItem['type'] === 'way') {
                $feature = self::createFeature($osmItem, $nodes);
                if ($feature) {
                    $output['features'] []= $feature;
                }
            }
        }

        return $encode ? json_encode($output) : $output;
    }

    /**
     * Creates an array of node coordinates indexed by node id
     * @param  array $elements  OSM items
     * @return array            node coordinates e.g. [id => [lon, lat], ...]
     */
    public static function collectNodes($elements) {
        $nodes = array();
        if (!is_array($elements)) {
            return $nodes;
        }
        foreach ($elements as $osmItem) {
            if (isset($osmItem['type']) && $osmItem['type'] === 'node') {
                if (isset($osmItem['id']) && isset($osmItem['lat']) && isset($osmItem['lon'])) {
                    $nodes[$osmItem['id']] = array($osmItem['lon'], $osmItem['lat']);
                }
            }
        }
        return $nodes;
    }

    /**
     * Creates a Feature array with geometry from matching nodes
     * @param  array $way  OSM way
     * @param  array $nodes    OSM node coordinates indexed by id
     * @return mixed           false if invalid feature otherwise
     *                         array GeoJSON Feature with LineString geometry
     */
    public static function createFeature($way, $nodes) {
        $coords = array();
        if (isset($way['nodes'])) {
            foreach ($way['nodes'] as $nodeId) {
                if (isset($nodes[$nodeId])) {
                    $coords []= $nodes[$nodeId];
                }
            }
        }
        if (count($coords) >= 2) {
            return array(
                'type' => 'Feature',
                'geometry' => array(
                    'type' => 'LineString',
                    'coordinates' => $coords,
                ),
                'properties' => array(),
            );
        }
        return false;
    }
}
