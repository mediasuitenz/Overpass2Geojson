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
            'features' => array(),
        );

        $nodes = self::collectNodes($inputArray['elements']);

        foreach ($inputArray['elements'] as $osmItem) {
            if (isset($osmItem['type']) && $osmItem['type'] === 'way') {
                $feature = array(
                    'geometry' => array(
                        'type' => 'LineString',
                        'coordinates' => array(),
                    ),
                );
                if (isset($osmItem['nodes'])) {
                    foreach ($osmItem['nodes'] as $nodeId) {
                        if (isset($nodes[$nodeId])) {
                            $feature['geometry']['coordinates'] []= $nodes[$nodeId];
                        }
                    }
                }
                $output['features'] []= $feature;
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
}
