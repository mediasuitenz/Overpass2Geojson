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

    public function testSmallDataset() {

        $input = file_get_contents(__DIR__ . '/data/small.json');
        $output = Overpass2Geojson::convert($input, false);

        $this->assertSame(2, count($output['features']), 'Should return 2 features');
    }
}
