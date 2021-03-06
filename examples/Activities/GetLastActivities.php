<?php

namespace Route4Me;

$root = realpath(dirname(__FILE__).'/../../');
require $root.'/vendor/autoload.php';

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_BAIL, 1);

// Example refers to the process of getting all activities recorded after specified time.

// Set the api key in the Route4Me class
Route4Me::setApiKey('11111111111111111111111111111111');

$activityParameters = ActivityParameters::fromArray([
    'limit' => 10,
    'offset' => 0,
    'start' => strtotime('-1 week'),
]);

$activities = new ActivityParameters();
$actResults = $activities->getActivities($activityParameters);
$results = $activities->getValue($actResults, 'results');

foreach ($results as $result) {
    Route4Me::simplePrint($result);
    echo '<br>';
}
