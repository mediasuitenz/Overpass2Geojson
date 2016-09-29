<?php
class Overpass2GeojsonTest extends PHPUnit_Framework_TestCase {

    public function testConvertInputValidation() {

        $input = null;
        $output = Overpass2Geojson::convertWays($input, true);
        $this->assertSame(false, $output, 'Should return false when given null');

        $input = 42;
        $output = Overpass2Geojson::convertWays($input, true);
        $this->assertSame(false, $output, 'Should return false when given something not a string');

        $input = '';
        $output = Overpass2Geojson::convertWays($input, true);
        $this->assertSame(false, $output, 'Should return false when given an empty string');

        $input = '{}';
        $output = Overpass2Geojson::convertWays($input, true);
        $this->assertSame(false, $output, 'Should return false when given json without elements');

        $input = array();
        $output = Overpass2Geojson::convertWays($input, true);
        $this->assertSame(false, $output, 'Should return false when given an array without elements');

        $input = '{ "elements": [] }';
        $output = Overpass2Geojson::convertWays($input, true);
        $this->assertTrue(is_string($output), 'Should return a string when given valid json');

        $input = array('elements' => array());
        $output = Overpass2Geojson::convertWays($input, true);
        $this->assertTrue(is_string($output), 'Should return a string when given a valid array');
    }

    public function testConvertArrayInputValidation() {

        $input = null;
        $output = Overpass2Geojson::convertWays($input, false);
        $this->assertSame(false, $output, 'Should return false when given null');

        $input = 42;
        $output = Overpass2Geojson::convertWays($input, false);
        $this->assertSame(false, $output, 'Should return false when given something not a string');

        $input = '';
        $output = Overpass2Geojson::convertWays($input, false);
        $this->assertSame(false, $output, 'Should return false when given an empty string');

        $input = '{}';
        $output = Overpass2Geojson::convertWays($input, false);
        $this->assertSame(false, $output, 'Should return false when given json without elements');

        $input = array();
        $output = Overpass2Geojson::convertWays($input, false);
        $this->assertSame(false, $output, 'Should return false when given an array without elements');

        $input = '{ "elements": [] }';
        $output = Overpass2Geojson::convertWays($input, false);
        $this->assertTrue(is_array($output), 'Should return an array when given valid json');

        $input = array('elements' => array());
        $output = Overpass2Geojson::convertWays($input, false);
        $this->assertTrue(is_array($output), 'Should return an array when given a valid array');
    }

    public function testOutputIsGeojsonArray() {

        $input = array('elements' => array());
        $output = Overpass2Geojson::convertWays($input, false);

        $this->assertTrue(isset($output['type']), 'Should return geojson with a type');
        $this->assertEquals('FeatureCollection', $output['type'], 'Should return geojson with type FeatureCollection');

        $this->assertTrue(isset($output['features']), 'Should return geojson with features');
        $this->assertTrue(is_array($output['features']), 'Should return geojson with features array');
    }

    public function testCollectNodes() {
        $input = null;
        $output = Overpass2Geojson::collectNodes($input);
        $this->assertTrue(is_array($output), 'Should return an array');

        $input = array(
            array('type' => 'node'),
            array('type' => 'node', 'id' => 12314513113, 'lat' => 123.4567, 'lon' => -13.13415),
            array('type' => 'way'),
            array('type' => 'node', 'id' => 13542524325, 'lat' => 151.1341, 'lon' => 32.26244),
        );
        $output = Overpass2Geojson::collectNodes($input);
        $this->assertTrue(is_array($output), 'Should return an array');
        $this->assertSame(2, count($output), 'Should have an entry for each valid node');

        $this->assertTrue(isset($output['12314513113']), 'Should be indexed by node id');
        $this->assertSame($input[1], $output['12314513113'], 'Should have original coordinates');

        $this->assertTrue(isset($output['13542524325']), 'Should be indexed by node id');
        $this->assertSame($input[3], $output['13542524325'], 'Should have original coordinates');
    }

    public function testSmallDataset() {

        $input = file_get_contents(__DIR__ . '/data/small.json');
        $output = Overpass2Geojson::convertWays($input, false);

        $this->assertSame(2, count($output['features']), 'Should return 2 features');

        $feature1 = $output['features'][0];
        $feature2 = $output['features'][1];
        $this->assertTrue(isset($feature1['type']), 'A feature should have type');
        $this->assertSame('Feature', $feature1['type'], 'A feature should have type Feature');

        $this->assertTrue(isset($feature1['geometry']), 'A feature should have geometry');
        $this->assertTrue(is_array($feature1['geometry']), 'A feature should have a geometry array');

        $this->assertTrue(isset($feature1['geometry']['type']), 'Geometry should have a type');
        $this->assertSame('LineString', $feature1['geometry']['type'], 'Geometry should have type LineString');

        $this->assertTrue(isset($feature1['properties']), 'A feature should have a properties array');
        $tags = array(
            "access" => "yes",
            "ccc" => "cyclingpath",
            "foot" => "yes",
            "highway" => "residential",
            "maxspeed" => "50",
            "name" => "Worcester Street");
        $this->assertSame($feature1['properties'], $tags, 'A feature\'s property array should contain all of its tags');

        $this->assertTrue(isset($feature1['geometry']['coordinates']), 'Geometry should have coordinates');
        $this->assertTrue(is_array($feature1['geometry']['coordinates']), 'Geometry should have a coordinates array');

        $this->assertSame(2, count($feature1['geometry']['coordinates']), 'Feature should have same number of coordinates as original way');
        $this->assertSame(5, count($feature2['geometry']['coordinates']), 'Feature should have same number of coordinates as original way');

        $coords1 = $feature1['geometry']['coordinates'];
        $this->assertTrue(is_array($coords1[0]), 'Each coordinate should be an array');
        $this->assertTrue(is_array($coords1[1]), 'Each coordinate should be an array');

        $this->assertSame(array(172.6420391, -43.5309816), $coords1[0], 'Coordinate should match the original node coordinate');
        $this->assertSame(array(172.6396892, -43.5309652), $coords1[1], 'Coordinate should match the original node coordinate');
    }

    public function testIncompleteData() {
        $input = file_get_contents(__DIR__ . '/data/missingNodes.json');
        $output = Overpass2Geojson::convertWays($input, false);

        $this->assertSame(1, count($output['features']), 'Should only create a feature for ways with more than 1 node');
        $coords = $output['features'][0]['geometry']['coordinates'];
        $this->assertSame(3, count($coords), 'Feature should have as many coordinates as exist in data');
        $this->assertSame(array(172.6427486, -43.5309800), $coords[0], 'Coordinate should match the original node coordinate');
    }

    public function testConvertNodes() {

        $input = file_get_contents(__DIR__ . '/data/small.json');
        $output = Overpass2Geojson::convertNodes($input, false);

        $this->assertSame(6, count($output['features']), 'Should return 6 features');

        $feature1 = $output['features'][0];
        $feature2 = $output['features'][1];
        $this->assertTrue(isset($feature1['type']), 'A feature should have type');
        $this->assertSame('Feature', $feature1['type'], 'A feature should have type Feature');

        $this->assertTrue(isset($feature1['geometry']), 'A feature should have geometry');
        $this->assertTrue(is_array($feature1['geometry']), 'A feature should have a geometry array');

        $this->assertTrue(isset($feature1['geometry']['type']), 'Geometry should have a type');
        $this->assertSame('Point', $feature1['geometry']['type'], 'Geometry should have type Point');

        $this->assertTrue(isset($feature1['geometry']['coordinates']), 'Geometry should have coordinates');
        $this->assertTrue(is_array($feature1['geometry']['coordinates']), 'Geometry should have a coordinates array');

        $this->assertSame(2, count($feature1['geometry']['coordinates']), 'First Point should have 2 coordinates');
        $this->assertSame(2, count($feature2['geometry']['coordinates']), 'Second Point should have 2 coordinates');

        $coords1 = $feature1['geometry']['coordinates'];
        $this->assertTrue(is_numeric($coords1[0]), 'Each coordinate should be a number');
        $this->assertTrue(is_numeric($coords1[1]), 'Each coordinate should be a number');

        $this->assertSame(172.6420391, $coords1[0], 'Coordinate should match the original node coordinate');
        $this->assertSame(-43.5309816, $coords1[1], 'Coordinate should match the original node coordinate');

        $this->assertTrue(isset($feature1['properties']), 'Feature should have properties');
        $this->assertTrue(isset($feature2['properties']), 'Feature should have properties');
        $this->assertTrue(is_array($feature1['properties']), 'Feature should have a properties array');
        $this->assertTrue(is_array($feature2['properties']), 'Feature should have a properties array');

        $this->assertSame(0, count($feature1['properties']), 'First feature should have no property values');
        $this->assertSame(3, count($feature2['properties']), 'Second feature should have 3 properties matching original node');
    }
}
