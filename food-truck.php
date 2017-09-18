<?php
/*
Plugin Name: Food Truck
Plugin URI: http://studiobrace.com/food-truck-wp-plugin/
Description: A Food Truck Location & Date Viewer, & A Menus built for food trucks
Author: Paul Collett
Author URI: http://paulcollett.com
Version: 1.0
Text Domain: food-truck
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

class FoodTruckPlugin {
	function __construct() {
		if(isset($_GET['food-truck'])) {
			var_dump($_GET['food-truck']);
			die;
		}
	}
}

new FoodTruckPlugin;
