<?php
namespace Route4Me;

use Route4Me\Common;
use Route4Me\Enum\Endpoint;

class Order extends Common
{
    public $address_1;
    public $address_2;
    public $cached_lat;
    public $cached_lng;
    public $curbside_lat;
    public $curbside_lng;
    public $address_alias;
    public $address_city;
    public $EXT_FIELD_first_name;
    public $EXT_FIELD_last_name;
    public $EXT_FIELD_email;
    public $EXT_FIELD_phone;
    public $EXT_FIELD_custom_data;
    
    public $color;
    public $order_icon;
    public $local_time_window_start;
    public $local_time_window_end;
    public $local_time_window_start_2;
    public $local_time_window_end_2;
    public $service_time;
    
    public $day_scheduled_for_YYMMDD;
    
    public $route_id;
    public $redirect;
    public $optimization_problem_id;
    public $order_id;
    public $order_ids;
    
    public $day_added_YYMMDD;
    public $scheduled_for_YYMMDD;
    public $fields;
    public $offset;
    public $limit;
    public $query;
    
    public $created_timestamp;
    public $order_status_id;
    public $member_id;
    public $address_state_id;
    public $address_country_id;
    public $address_zip;
    public $in_route_count;
    public $last_visited_timestamp;
    public $last_routed_timestamp;
    public $local_timezone_string;
    public $is_validated;
    public $is_pending;
    public $is_accepted;
    public $is_started;
    public $is_completed;
    public $custom_user_fields;
    
    public static function fromArray(array $params) {
        $order = new Order();
        foreach ($params as $key => $value) {
            if (property_exists($order, $key)) {
                $order->{$key} = $value;
            }
        }
        
        return $order;
    }
    
    /**
     * @param Order $params
     */
    public static function addOrder($params)
    {
        $body = array();
        
        $allAddOrderParameters = array('address_1', 'address_2', 'member_id', 'cached_lat', 'cached_lng', 'curbside_lat', 
        'curbside_lng', 'color', 'order_icon', 'day_scheduled_for_YYMMDD', 'address_alias', 'address_city', 'address_state_id', 
        'address_country_id', 'address_zip', 'local_time_window_start', 'local_time_window_end', 'local_time_window_start_2', 
        'local_time_window_end_2', 'service_time', 'local_timezone_string', 'EXT_FIELD_first_name', 'EXT_FIELD_last_name', 
        'EXT_FIELD_email', 'EXT_FIELD_phone', 'EXT_FIELD_custom_data', 'is_validated', 'is_pending', 'is_accepted', 'is_started', 
        'is_completed', 'custom_user_fields');
        
        foreach ($allAddOrderParameters as $addOrderParameter) {
            if (isset($params->{$addOrderParameter})) $body[$addOrderParameter] = $params->{$addOrderParameter};
        }
       
        $response = Route4Me::makeRequst(array(
            'url'    => Endpoint::ORDER_V4,
            'method' => 'POST',
            'body'   => $body
        ));

        return $response;
    }

    public static function addOrder2Route($params, $body)
    {
        $response = Route4Me::makeRequst(array(
            'url'    => Endpoint::ROUTE_V4,
            'method' => 'PUT',
            'query'  => array(
                'route_id' => isset($params->route_id) ? $params->route_id : null,
                'redirect' => isset($params->redirect) ? $params->redirect : null
            ),
            'body' => (array)$body
        ));

        return $response;
    }
    
    public static function addOrder2Optimization($params, $body)
    {
        $response = Route4Me::makeRequst(array(
            'url'    => Endpoint::OPTIMIZATION_PROBLEM,
            'method' => 'PUT',
            'query'  => array(
                'optimization_problem_id' =>  isset($params['optimization_problem_id']) ? $params['optimization_problem_id'] : null,
                'redirect'                => isset($params['redirect']) ? $params['redirect'] : null,
                'device_type'             => isset($params['device_type']) ? $params['device_type'] : null
            ),
            'body'  => (array)$body
        ));

        return $response;
    }
    
    public static function getOrder($params)
    {
        $query = array();
        $allGetParameters = array('order_id', 'fields', 'day_added_YYMMDD', 'scheduled_for_YYMMDD', 'query', 'offset', 'limit' );
        
        foreach ($allGetParameters as $getParameter) {
            if (isset($params->{$getParameter})) $query[$getParameter] = $params->{$getParameter};
        }

        $response = Route4Me::makeRequst(array(
            'url'    => Endpoint::ORDER_V4,
            'method' => 'GET',
            'query'  => $query
        ));

        return $response;
    }
    
    public static function getOrders($params)
    {
        $response = Route4Me::makeRequst(array(
            'url'    => Endpoint::ORDER_V4,
            'method' => 'GET',
            'query'  => array(
                'offset' => isset($params->offset) ? $params->offset : null,
                'limit'  => isset($params->limit) ? $params->limit : null
            )
        ));

        return $response;
    }
    
    public function getRandomOrderId($offset, $limit)
    {
        $randomOrder = $this->getRandomOrder($offset, $limit);
        
        if (is_null($randomOrder)) {
            return null;
        }
        
        if (!isset($randomOrder)) {
            return null;
        }
        
        return $randomOrder['order_id'];
    }
    
    public function getRandomOrder($offset, $limit)
    {
        $params = array('offset' => $offset, 'limit' => $limit);
        
        $orders = self::getOrders($params);
        
        if (is_null($orders)) {
            return null;
        }
        
        if (!isset($orders['results'])) {
            return null;
        }
        
        $randomIndex = rand(0, sizeof($orders['results']) - 1);
        
        $order = $orders['results'][$randomIndex];
        
        return $order;
    }
    
    public static function removeOrder($params)
    {
        $response = Route4Me::makeRequst(array(
            'url'    => Endpoint::ORDER_V4,
            'method' => 'DELETE',
            'body'   => array(
                'order_ids' =>  isset($params->order_ids) ? $params->order_ids : null
            )
        ));

        return $response;
    }
    
    public static function updateOrder($body)
    {
        $response = Route4Me::makeRequst(array(
            'url'    => Endpoint::ORDER_V4,
            'method' => 'PUT',
            'body'   => (array)$body
        ));

        return $response;
    }
    
    public static function searchOrder($params)
    {
        $query = array();
        $allSearchParameters = array('fields', 'day_added_YYMMDD', 'scheduled_for_YYMMDD', 'query', 'offset', 'limit' );
        
        foreach ($allSearchParameters as $searchParameter) {
            if (isset($params->{$searchParameter})) $query[$searchParameter] = $params->{$searchParameter};
        }
        
        $response = Route4Me::makeRequst(array(
            'url'    => Endpoint::ORDER_V4,
            'method' => 'GET',
            'query'  => $query
        ));

        return $response;
    }
    
    public static function validateLatitude($lat)
    {
        if (!is_numeric($lat)) {
            return false;
        }
        
        if ($lat>90 || $lat<-90) {
            return false;
        }
        
        return true;
    }
    
    public static function validateLongitude($lng)
    {
        if (!is_numeric($lng)) {
            return false;
        }
        
        if ($lng>180 || $lng<-180) {
            return false;
        }
        
        return true;
    }
    
    public function addOrdersFromCsvFile($csvFileHandle, $ordersFieldsMapping)
    {
        $max_line_length = 512;
        $delemietr = ',';
        
        $results = array();
        $results['fail'] = array();
        $results['success'] = array();
        
        $columns = fgetcsv($csvFileHandle, $max_line_length, $delemietr);
        
        $allOrderFields = array("curbside_lat","curbside_lng","color","day_scheduled_for_YYMMDD",
                "address_alias","address_1","address_2","local_time_window_start","local_time_window_end","local_time_window_start_2",
                "local_time_window_end_2","service_time","EXT_FIELD_first_name","EXT_FIELD_last_name","EXT_FIELD_email","EXT_FIELD_phone",
                "EXT_FIELD_custom_data","order_icon");
        
        if (!empty($columns)) {
             array_push($results['fail'],'Empty CSV table');
             return ($results);
        }
                 
        $iRow=1;
        
        while (($rows = fgetcsv($csvFileHandle, $max_line_length, $delemietr))!==false) {
            if ($rows[$ordersFieldsMapping['cached_lat']] && $rows[$ordersFieldsMapping['cached_lng']] && $rows[$ordersFieldsMapping['address_1']] && array(null)!==$rows) {
                
                $cached_lat = 0.000;
                
                if (!$this->validateLatitude($rows[$ordersFieldsMapping['cached_lat']])) {
                    array_push($results['fail'], "$iRow --> Wrong cached_lat"); 
                    $iRow++;
                    continue;
                } else {
                    $cached_lat = doubleval($rows[$ordersFieldsMapping['cached_lat']]);
                }
                
                $cached_lng = 0.000;
                
                if (!$this->validateLongitude($rows[$ordersFieldsMapping['cached_lng']])) {
                    array_push($results['fail'], "$iRow --> Wrong cached_lng"); 
                    $iRow++;
                    continue;
                } else {
                    $cached_lng = doubleval($rows[$ordersFieldsMapping['cached_lng']]);
                }
                
                if (isset($ordersFieldsMapping['curbside_lat'])) {
                    if (!$this->validateLatitude($rows[$ordersFieldsMapping['curbside_lat']])) {
                        array_push($results['fail'], "$iRow --> Wrong curbside_lat"); 
                        $iRow++;
                        continue;
                    }
                }
                
                if (isset($ordersFieldsMapping['curbside_lng'])) {
                    if (!$this->validateLongitude($rows[$ordersFieldsMapping['curbside_lng']])) {
                        array_push($results['fail'], "$iRow --> Wrong curbside_lng"); 
                        $iRow++;
                        continue;
                    }
                }
                
                $address = $rows[$ordersFieldsMapping['address_1']];
                
                if (isset($ordersFieldsMapping['order_city'])) {
                    $address.=', '.$rows[$ordersFieldsMapping['order_city']];
                }
                
                if (isset($ordersFieldsMapping['order_state_id'])) {
                    $address.=', '.$rows[$ordersFieldsMapping['order_state_id']];
                }
                
                if (isset($ordersFieldsMapping['order_zip_code'])) {
                    $address.=', '.$rows[$ordersFieldsMapping['order_zip_code']];
                }
                
                if (isset($ordersFieldsMapping['order_country_id'])) {
                    $address.=', '.$rows[$ordersFieldsMapping['order_country_id']];
                }
                
                echo "$iRow --> ".$ordersFieldsMapping['day_scheduled_for_YYMMDD'].", ".$rows[$ordersFieldsMapping['day_scheduled_for_YYMMDD']]."<br>";
                
                $parametersArray = array();
                
                $parametersArray["cached_lat"] = $cached_lat;
                $parametersArray["cached_lng"] = $cached_lng;
                
                
                foreach ($allOrderFields as $orderField) {
                    if (isset($ordersFieldsMapping[$orderField])) {
                        $parametersArray[$orderField] = $rows[$ordersFieldsMapping[$orderField]];
                    }
                }
                
                $orderParameters = Order::fromArray($parametersArray);

                $order = new Order();
                
                $orderResults = $order->addOrder($orderParameters);
                
                array_push($results['success'], "The order with order_id = ".strval($orderResults["order_id"])." added successfuly.");
            }
            else {
                array_push($results['fail'], "$iRow --> one of the parameters cached_lat, cached_lng, address_1 is not set"); 
            }
            
            $iRow++;
        }
    }
}
