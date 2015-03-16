<?php
class Overpass2GeojsonTest extends PHPUnit_Framework_TestCase
{

    public function testInputValidation() {

        $input = null;
        $output = Overpass2Geojson::convert($input);
        $this->assertSame(false, $output, 'Should return false when given null');

        $input = 42;
        $output = Overpass2Geojson::convert($input);
        $this->assertSame(false, $output, 'Should return false when given something not a string');
    }
}
