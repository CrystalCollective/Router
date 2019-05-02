<?php
/**
 * User: Pankaj Vaghela
 * Date: 02-05-2019
 * Time: 12:17
 */

namespace KrystlPHP;

use Closure;

class Route{
	/**
	 * @var string $url
	 * Schema of URL
	 */
	public $url;
	
	/**
	 * @var string $method
	 * Method of Request eg GET, POST
	 */
	public $method;
	
	/**
	 * @var Closure $callback
	 * Function to be called for route
	 */
	public $callback;
	
	/**
	 * @var string $name
	 * Name of the Route
	 */
	public $name;
	
	/**
	 * Route constructor.
	 * @param $method
	 * @param $url
	 * @param $callback
	 * @param string $name
	 */
	public function __construct($method, $url, $callback, $name = ""){
		$this->url = $url;
		$this->method = $method;
		$this->callback = $callback;
		$this->name = $name;
	}
	
	/**
	 * @param $name
	 * @return $this
	 */
	public function name($name){
		$this->name = $name;
		Router::addRoutesByName($this->name, $this->method, $this->url);
		return $this;
	}
}