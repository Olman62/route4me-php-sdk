<?php
namespace Route4Me;

$root = realpath(dirname(__FILE__) . '/../../');
require $root . '/vendor/autoload.php';

use Route4Me\Route4Me;
use Route4Me\Geocoding;

// Example refers to getting all geocodings with specified zipcode.

// Set the api key in the Route4me class
Route4Me::setApiKey('11111111111111111111111111111111');

$gcParameters=(array)Geocoding::fromArray(array(
    "zipcode" => '00601'
));

$geocoding = new Geocoding();

$response = $geocoding->getZipCode($gcParameters);

Route4Me::simplePrint($response);
