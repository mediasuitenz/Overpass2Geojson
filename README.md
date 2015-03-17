Overpass 2 GeoJSON
==================

[![Media Suite](http://mediasuite.co.nz/ms-badge.png)](http://mediasuite.co.nz)

[![Build Status](https://travis-ci.org/mediasuitenz/Overpass2Geojson.svg)](https://travis-ci.org/mediasuitenz/Overpass2Geojson)

`composer require mediasuitenz/Overpass2Geojson`

PHP modules to convert Overpass API JSON output to GeoJSON format

**Note:** this currently only converts OSM ways and their nodes into a FeatureCollection of Features that have LineString geometries. If any input ways reference nodes that aren't also in the input, those nodes will be ignored. If there are less than 2 nodes for any way, that way will not produce a Feature as LineStrings must have more than 2 coordinates.

## Example

### Overpass query
```
[out:json][timeout:25];
// gather results
(
  // all with highway tag
  way["highway"]
  // within bounding box
  (-43.5594542,172.6998653,-43.5548322,172.708076);
  // recursively get all nodes for the resultant ways (contains the lat/lon whereas ways don't)
  node(w)->.x;
);
out;
```

### PHP
```php
// Example from above
$url = 'http://overpass.osm.rambler.ru/cgi/interpreter?data=[out:json][timeout:25];%20(%20way[%22highway%22]%20(-43.5594542,172.6998653,-43.5548322,172.708076);%20node(w)-%3E.x;%20);%20out;';

// cURL the API
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
$osmJsonData = curl_exec($ch);

// convert accepts JSON string or array e.g. from json_decode($osmJsonData, true);

$geojson = Overpass2Geojson::convert($osmJsonData); // Returns JSON encoded string
$geojson = Overpass2Geojson::convert($osmJsonData, false); // Returns array with GeoJSON structure
```
