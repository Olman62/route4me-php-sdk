<?php
	namespace Route4me;
	
	$vdir=$_SERVER['DOCUMENT_ROOT'].'/route4me/examples/';

    require $vdir.'/../vendor/autoload.php';
	
	use Route4me\Route4me;
	use Route4me\Route;
	
	// Set the api key in the Route4me class
	Route4me::setApiKey('11111111111111111111111111111111');
	
	// Get random route from test routes
	//--------------------------------------------------------
	$route=new Route();
	
	$route_id=$route->getRandomRouteId(10, 20);
	
	if (is_null($route_id)) {
		echo "can't retrieve random route_id!.. Try again.";
		return;
	}
	
	$route=$route->getRoutes($route_id,null);
	
	$optimizationProblemId=$route->getOptimizationId();
	
	echo "route_id = $route_id<br>";
	echo "optimization_problem_id = $optimizationProblemId <br><br>";
	
	$optimizationProblemParams = array(
		"optimization_problem_id"  =>  $optimizationProblemId
	);
	
	$optimizationProblem = new OptimizationProblem();
	
	$optimizationProblem = $optimizationProblem->get($optimizationProblemParams);
	
	foreach ((array)$optimizationProblem as $probParts)
	{
		Route4me::simplePrint((array)$probParts);	
	}
	
?>