<?php
namespace Route4Me;

use Route4Me\Address;
use Route4Me\Common;
use Route4Me\RouteParameters;
use Route4Me\OptimizationProblemParams;
use Route4Me\Route;
use Route4Me\Route4Me;
use Route4Me\Enum\Endpoint;

class OptimizationProblem extends Common
{
    public $optimization_problem_id;
    public $user_errors = array();
    public $state;
    public $optimization_errors = array();
    public $parameters;
    public $sent_to_background;
    public $created_timestamp;
    public $scheduled_for;
    public $optimization_completed_timestamp;
    public $addresses = array();
    public $routes = array();
    public $links = array();

    function __construct()
    {
        $this->parameters = new RouteParameters;
    }

    public static function fromArray(array $params)
    {
        $problem = new OptimizationProblem;
        $problem->optimization_problem_id = Common::getValue($params, 'optimization_problem_id');
        $problem->user_errors = Common::getValue($params, 'user_errors', array());
        $problem->state = Common::getValue($params, 'state', array());
        $problem->sent_to_background = Common::getValue($params, 'sent_to_background', array());
        $problem->links = Common::getValue($params, 'links', array());

        if (isset($params['parameters'])) {
            $problem->parameters = RouteParameters::fromArray($params['parameters']);
        }

        if (isset($params['addresses'])) {
            $addresses = array();
            
            foreach ($params['addresses'] as $address) {
                $addresses[] = Address::fromArray($address);
            }
            
            $problem->addresses = $addresses;
        }

        if (isset($params['routes'])) {
            $routes = array();
            
            foreach ($params['routes'] as $route) {
                $routes[] = Route::fromArray($route);
            }
            
            $problem->routes = $routes;
        }

        return $problem;
    }

    public static function optimize(OptimizationProblemParams $params)
    {
        $optimize = Route4Me::makeRequst(array(
            'url'    => Endpoint::OPTIMIZATION_PROBLEM,
            'method' => 'POST',
            'query'  => array(
                'redirect'               => isset($params->redirect) ? $params->redirect : null,
                'directions'             => isset($params->directions) ? $params->directions : null, 
                'format'                 => isset($params->format) ? $params->format : null,
                'route_path_output'      => isset($params->route_path_output) ? $params->route_path_output : null,
                'optimized_callback_url' => isset($params->optimized_callback_url) ? $params->optimized_callback_url : null
            ),
            'body'   => array(
                'addresses'  => $params->getAddressesArray(),
                'parameters' => $params->getParametersArray()
            )
        ));

        return OptimizationProblem::fromArray($optimize);
    }

    public static function get($params)
    {
        $optimize = Route4Me::makeRequst(array(
            'url'    => Endpoint::OPTIMIZATION_PROBLEM,
            'method' => 'GET',
            'query'  => array(
                'state'  => isset($params['state']) ? $params['state'] : null,
                'limit'  => isset($params['limit']) ? $params['limit'] : null,
                'offset' => isset($params['offset']) ? $params['offset'] : null,
                'optimization_problem_id' => isset($params['optimization_problem_id']) 
                    ? $params['optimization_problem_id'] : null,
                'wait_for_final_state' => isset($params['wait_for_final_state']) 
                    ? $params['wait_for_final_state'] : null,
            )
        ));

        if (isset($optimize['optimizations'])) {
            $problems = array();
            
            foreach ($optimize['optimizations'] as $problem) {
                $problems[] = OptimizationProblem::fromArray($problem);
            }
            
            return $problems;
        } else {
            return OptimizationProblem::fromArray($optimize);
        }
    }

    public static function reoptimize($params)
    {
        $param = new OptimizationProblemParams;
        $param->optimization_problem_id = isset($params['optimization_problem_id']) ? $params['optimization_problem_id'] : null;
        $param->reoptimize = 1;

        return self::update((array)$param);
    }

    public static function update($params)
    {
        $optimize = Route4Me::makeRequst(array(
            'url'    => Endpoint::OPTIMIZATION_PROBLEM,
            'method' => 'PUT',
            'query'  => array(
                'optimization_problem_id' => isset($params['optimization_problem_id'])
                                               ? $params['optimization_problem_id'] : null,
                'addresses'  => isset($params['addresses']) ? $params['addresses'] : null,
                'reoptimize' => isset($params['reoptimize']) ? $params['reoptimize'] : null,
            )
        ));
        
        return $optimize;
    }

    public function getOptimizationId()
    {
        return $this->optimization_problem_id;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
    
    public function getRandomOptimizationId($offset, $limit)
    {
        $query['limit'] = !is_null($limit) ? $limit : 30;
        $query['offset'] = !is_null($offset) ? $offset : 0;
            
        $json = Route4Me::makeRequst(array(
            'url'    => Endpoint::OPTIMIZATION_PROBLEM,
            'method' => 'GET',
            'query'  => $query
        ));
        
        $optimizations = array();
            foreach ($json as $optimization) {
                if (gettype($optimization)!="array") {
                   continue; 
                }
                
                foreach ($optimization as $otp1) {
                    $optimizations[] = $otp1;
                }
            }
            
            $num = rand(0,sizeof($optimizations)-1);
            
            $rOptimization = $optimizations[$num];
            
            return $rOptimization['optimization_problem_id'];
    }
    
    public function getAddresses($opt_id)
    {
        if ($opt_id==null) {
            return null;
        }
        
        $params = array("optimization_problem_id" => $opt_id );
        
        $optimization = (array)$this->get($params);
        
        $addresses = $optimization["addresses"];
        
        return $addresses;
    }
    
    public function getRandomAddressFromOptimization($opt_id)
    {
        $addresses = (array)$this->getAddresses($opt_id);
        
        if ($addresses==null) {
            echo "There are no addresses in this optimization!.. Try again.";
            return null;
        }
        
        $num = rand(0, sizeof($addresses)-1);
        
        $rAddress = $addresses[$num];
        
        return $rAddress;
    }
    
    public function removeAddress($params)
    {
        $response = Route4Me::makeRequst(array(
            'url'    => Endpoint::ADDRESS_V4,
            'method' => 'DELETE',
            'query'  => array(
                'optimization_problem_id' => isset($params['optimization_problem_id'])
                                               ? $params['optimization_problem_id'] : null,
                'route_destination_id'    => isset($params['route_destination_id'])
                                               ? $params['route_destination_id'] : null,
            )
        ));
        
        return $response;
    }
    
    public function removeOptimization($params)
    {
        $response = Route4Me::makeRequst(array(
            'url'    => Endpoint::OPTIMIZATION_PROBLEM,
            'method' => 'DELETE',
            'query'  => array(
                'redirect' => isset($params['redirect']) ? $params['redirect'] : null,
            ),
            'body'   => array(
                'optimization_problem_ids' => isset($params['optimization_problem_ids'])
                                                ? $params['optimization_problem_ids'] : null,
            )
        ));
        
        return $response;
    }
    
    public function getHybridOptimization($params)
    {
        $optimize = Route4Me::makeRequst(array(
            'url'    => Endpoint::HYBRID_DATE_OPTIMIZATION,
            'method' => 'GET',
            'query'  => array(
                'target_date_string'      => isset($params['target_date_string'])
                                               ? $params['target_date_string'] : null,
                'timezone_offset_minutes' => isset($params['timezone_offset_minutes'])
                                               ? $params['timezone_offset_minutes'] : null
            )
        ));

        return $optimize;
    }
    
    Public function addDepotsToHybrid($params)
    {
        $depots = Route4Me::makeRequst(array( 
            'url'    => Endpoint::CHANGE_HYBRID_OPTIMIZATION_DEPOT,
            'method' => 'POST',
            'query'  => array(
                'optimization_problem_id' => isset($params['optimization_problem_id'])
                                               ? $params['optimization_problem_id'] : null,
                ),
            'body'   => array(
                'optimization_problem_id' => isset($params['optimization_problem_id'])
                                               ? $params['optimization_problem_id'] : null,
                'delete_old_depots'       => isset($params['delete_old_depots']) ? $params['delete_old_depots'] : null,
                'new_depots'              => isset($params['new_depots']) ? $params['new_depots'] : null,
            )
        ));
        
        return $depots;
    }
}
