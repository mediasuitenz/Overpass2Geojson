<?php
class Overpass2GeojsonTest extends PHPUnit_Framework_TestCase
{

    public function testConvertInputValidation() {

        $input = null;
        $output = Overpass2Geojson::convert($input);
        $this->assertSame(false, $output, 'Should return false when given null');

        $input = 42;
        $output = Overpass2Geojson::convert($input);
        $this->assertSame(false, $output, 'Should return false when given something not a string');

        $input = '';
        $output = Overpass2Geojson::convert($input);
        $this->assertSame(false, $output, 'Should return false when given an empty string');

        $input = '{}';
        $output = Overpass2Geojson::convert($input);
        $this->assertSame(false, $output, 'Should return false when given json without elements');

        $input = array();
        $output = Overpass2Geojson::convert($input);
        $this->assertSame(false, $output, 'Should return false when given an array without elements');

        $input = '{ "elements": [] }';
        $output = Overpass2Geojson::convert($input);
        $this->assertTrue(is_string($output), 'Should return a string when given valid json');

        $input = array('elements' => array());
        $output = Overpass2Geojson::convert($input);
        $this->assertTrue(is_string($output), 'Should return a string when given a valid array');
    }

    public function testConvertArrayInputValidation() {

        $input = null;
        $output = Overpass2Geojson::convertArray($input);
        $this->assertSame(false, $output, 'Should return false when given null');

        $input = 42;
        $output = Overpass2Geojson::convertArray($input);
        $this->assertSame(false, $output, 'Should return false when given something not a string');

        $input = '';
        $output = Overpass2Geojson::convertArray($input);
        $this->assertSame(false, $output, 'Should return false when given an empty string');

        $input = '{}';
        $output = Overpass2Geojson::convertArray($input);
        $this->assertSame(false, $output, 'Should return false when given json without elements');

        $input = array();
        $output = Overpass2Geojson::convertArray($input);
        $this->assertSame(false, $output, 'Should return false when given an array without elements');

        $input = '{ "elements": [] }';
        $output = Overpass2Geojson::convertArray($input);
        $this->assertTrue(is_array($output), 'Should return an array when given valid json');

        $input = array('elements' => array());
        $output = Overpass2Geojson::convertArray($input);
        $this->assertTrue(is_array($output), 'Should return an array when given a valid array');
    }
}
