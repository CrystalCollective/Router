<?php

/**
 * User: Pankaj Vaghela
 * Date: 02-05-2019
 * Time: 12:10
 */
namespace KrystlPHP;

use KrystlPHP\Route as Route;

class Router{
	/**
	 * @var string $rt
	 * Requested Route
	 */
	public static $rt;
	
	/**
	 * @var Route[] $routes
	 */
	public static $routes = [];
	
	/**
	 * @var array $routes_by_name
	 * Route by name to easily create url
	 */
	public static $routes_by_name = [];
	
	/**
	 * @var int $routeCount
	 * Count of total routes defined
	 */
	public static $routeCount = 0;
	
	/**
	 * @var array $defaultRoutes
	 * Collection of default route for each type
	 */
	public static $defaultRoutes = array();
	
	private static $url_data_keys = array();
	private static $url_data_vals = array();
	
	/**
	 * @param string $request_route
	 */
	public static function setRoute($request_route){
		static::$rt = $request_route;
	}
	
	/**
	 * @param string $method
	 * @param $url
	 * @param $callback
	 * @param string $name
	 * @return mixed
	 */
	public static function addRoute($method, $url, $callback, $name = ''){
		static::$routeCount++;
		static::$routes[$method][$url] = new Route($method, $url, $callback, $name);
		$ref =& static::$routes[$method][$url];
		static::addRoutesByName($name, $method, $url);
		return $ref;
	}
	
	/**
	 * @param $url
	 * @param $callback
	 * @return Route
	 */
	public static function get($url, $callback){
		return static::addRoute("GET", $url, $callback);
	}
	
	/**
	 * @param $url
	 * @param $callback
	 * @return Route
	 */
	public static function post($url, $callback){
		return static::addRoute("POST", $url, $callback);
	}
	
	/**
	 * set Default Route for Any method
	 * @param $method
	 * @param $callback
	 */
	public static function setDefaultRoute($method, $callback){
		static::$defaultRoutes[$method] = ['callback' => $callback];
	}
	
	/**
	 * Resolve the request
	 * @param string $reqMethod Requested Method
	 * @param string $rt Requested URL
	 * @return mixed
	 * @throws RouterException
	 */
	public static function resolve($reqMethod, $rt = ''){
		if($rt == ''){
			$rt = static::$rt;
		}

//		$reqUrl = ($rt[0] == "/" ? "" : "/") . $rt;
//		$reqMet = $_SERVER['REQUEST_METHOD'];
		
		/**
		 * @var $route Route
		 */
		foreach(static::$routes[$reqMethod] as $route){
			// convert urls like '/users/:uid/posts/:pid' to regular expression
			$pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9_\-]+/', '([a-zA-Z0-9\@.\-_]+)', preg_quote($route->url)) . "$@D";
			
			// check if the current request matches the expression
			if(preg_match($pattern, $rt, $matches)){
				// remove the first match
				array_shift($matches);
				
				preg_match_all("/:\w+/", $route->url, $keys);
				$keys = $keys[0];
				foreach($keys as $x => $key){
					$keys[$x] = substr($key, 1);
				}
				
				static::$url_data_keys = $keys;
				static::$url_data_vals = $matches;
				
				// call the callback with the matched positions as params
				return call_user_func_array($route->callback, []);
			}
		}
		
		foreach(static::$defaultRoutes as $method => $route){
			if($reqMethod == $method){
				return call_user_func_array($route['callback'], array());
			}
		}
		
		throw new RouterException("Route Not Defined for request");
	}
	
	
	/**
	 * @param $name
	 * @param $method
	 * @param $url
	 */
	public static function addRoutesByName($name, $method, $url){
		if($name != ''){
			static::$routes_by_name[$name] =& static::$routes[$method][$url];;
		}
	}
}

