<?php
class Overpass2GeojsonTest extends PHPUnit_Framework_TestCase
{

    public function testInputOutput() {
        $input = '';
        $output = Overpass2Geojson::convert($input);
        $this->assertTrue(is_string($output), 'Did not output a string');
    }
}
