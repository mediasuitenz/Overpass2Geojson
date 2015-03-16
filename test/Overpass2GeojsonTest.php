<?php
class Overpass2GeojsonTest extends PHPUnit_Framework_TestCase
{

    public function testConvertInputValidation() {

        $input = null;
        $output = Overpass2Geojson::convert($input, true);
        $this->assertSame(false, $output, 'Should return false when given null');

        $input = 42;
        $output = Overpass2Geojson::convert($input, true);
        $this->assertSame(false, $output, 'Should return false when given something not a string');

        $input = '';
        $output = Overpass2Geojson::convert($input, true);
        $this->assertSame(false, $output, 'Should return false when given an empty string');

        $input = '{}';
        $output = Overpass2Geojson::convert($input, true);
        $this->assertSame(false, $output, 'Should return false when given json without elements');

        $input = array();
        $output = Overpass2Geojson::convert($input, true);
        $this->assertSame(false, $output, 'Should return false when given an array without elements');

        $input = '{ "elements": [] }';
        $output = Overpass2Geojson::convert($input, true);
        $this->assertTrue(is_string($output), 'Should return a string when given valid json');

        $input = array('elements' => array());
        $output = Overpass2Geojson::convert($input, true);
        $this->assertTrue(is_string($output), 'Should return a string when given a valid array');
    }

    public function testConvertArrayInputValidation() {

        $input = null;
        $output = Overpass2Geojson::convert($input, false);
        $this->assertSame(false, $output, 'Should return false when given null');

        $input = 42;
        $output = Overpass2Geojson::convert($input, false);
        $this->assertSame(false, $output, 'Should return false when given something not a string');

        $input = '';
        $output = Overpass2Geojson::convert($input, false);
        $this->assertSame(false, $output, 'Should return false when given an empty string');

        $input = '{}';
        $output = Overpass2Geojson::convert($input, false);
        $this->assertSame(false, $output, 'Should return false when given json without elements');

        $input = array();
        $output = Overpass2Geojson::convert($input, false);
        $this->assertSame(false, $output, 'Should return false when given an array without elements');

        $input = '{ "elements": [] }';
        $output = Overpass2Geojson::convert($input, false);
        $this->assertTrue(is_array($output), 'Should return an array when given valid json');

        $input = array('elements' => array());
        $output = Overpass2Geojson::convert($input, false);
        $this->assertTrue(is_array($output), 'Should return an array when given a valid array');
    }

    public function testOutputIsGeojsonArray() {

        $input = array('elements' => array());
        $output = Overpass2Geojson::convert($input, false);

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
        $this->assertSame(array(-13.13415, 123.4567), $output['12314513113'], 'Should have original coordinates');

        $this->assertTrue(isset($output['13542524325']), 'Should be indexed by node id');
        $this->assertSame(array(32.26244, 151.1341), $output['13542524325'], 'Should have original coordinates');
    }

    public function testSmallDataset() {

        $input = file_get_contents(__DIR__ . '/data/small.json');
        $output = Overpass2Geojson::convert($input, false);

        $this->assertSame(2, count($output['features']), 'Should return 2 features');

        $feature1 = $output['features'][0];
        $this->assertTrue(isset($feature1['geometry']), 'A feature should have geometry');
        $this->assertTrue(is_array($feature1['geometry']), 'A feature should have a geometry array');

        $this->assertTrue(isset($feature1['geometry']['type']), 'Geometry should have a type');
        $this->assertSame('LineString', $feature1['geometry']['type'], 'Geometry should have type LineString');

        $this->assertTrue(isset($feature1['geometry']['coordinates']), 'Geometry should have coordinates');
        $this->assertTrue(is_array($feature1['geometry']['coordinates']), 'Geometry should have a coordinates array');
    }
}
