<?php
/*
Plugin Name: Food Truck
Plugin URI: https://github.com/paulcollett/food-truck-wp-plugin
Description: Food Truck Location & Dates plugin built for Food Trucks
Author: Paul Collett
Author URI: http://paulcollett.com
Version: 1.0.3
Text Domain: food-truck
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

class FoodTruckPlugin {
	function __construct() {
    if(!defined('TRUCKLOT_THEME_URI')) {
      define('TRUCKLOT_THEME_URI', plugin_dir_url(__FILE__));
    }

    if(!defined('TRUCKLOT_PLUGIN_VER')) {
      $pluginHeaders = get_file_data(__FILE__, array('Version' => 'Version'), false);
      define('TRUCKLOT_PLUGIN_VER', $pluginHeaders['Version']);
    }

    // Register Custom post types to store data
    add_action('init', 'trucklot_register_post_types');
    add_action('init', 'trucklot_out');
    add_shortcode( 'foodtruck', 'trucklot_handle_shortcode' );
    add_action('enqueue_scripts', 'trucklot_site_add_assets' );
    add_action( 'admin_bar_menu', 'trucklot_toolbar_link_to_editor', 999 );
    add_action( 'widgets_init', 'trucklot_register_widget' );

    if( is_admin() ) {
      // Add plugin features to admin
      add_action('admin_menu', 'trucklot_admin_add_plugin_section' );
      add_action('admin_enqueue_scripts', 'trucklot_admin_add_assets' );
      // Admin Ajax hook
      add_action( 'wp_ajax_food-truck', 'trucklot_handle_ajax' );
    }
    else {
      add_action('wp_enqueue_scripts', 'trucklot_site_add_assets' );
    }
	}
}

new FoodTruckPlugin;

function trucklot_toolbar_link_to_editor($wp_admin_bar){
  if(is_admin()) {
    $args = array(
      'id' => 'trucklot',
      'title' => 'Location',
      'href' => get_admin_url('','?page=trucklot-locations'),
      'parent' => 'new-content'
    );
  } else {
    $args = array(
      'id' => 'trucklot',
      'title' => '<span style="position:relative;top:4px;opacity:0.5;margin-right:5px" class="wp-menu-image dashicons-before dashicons-location-alt"></span>Manage Location &amp; Dates',
      'href' => get_admin_url('','?page=trucklot-locations'),
      //'meta'  => array( 'class' => 'trucklot-menu-item' )
    );
  }

  $wp_admin_bar->add_node($args);
}

function trucklot_register_post_types(){
  // add menu posts - feature coming soon
  /*register_post_type( 'trucklot-menus',array(
    'labels' => array(),
    'public' => false,
    'show_ui' => false,
    'supports' => array()
  ));*/

  // add location posts
  register_post_type( 'trucklot-locations',array(
    'labels' => array(),
    'public' => false,
    'show_ui' => false,
    'supports' => array()
  ));
}

function trucklot_admin_add_plugin_section(){

    // add_menu_page( 'Menus', "Menus", 'edit_posts','trucklot-menus', 'trucklot_render_admin_menu_posts', 'dashicons-format-aside' , '20.2');

    add_menu_page( 'Locations', "Location & Dates", 'edit_posts','trucklot-locations', 'trucklot_render_admin_locations', 'dashicons-location-alt'  , '20.1');

}

function trucklot_admin_add_assets($page){
  if($page != 'toplevel_page_trucklot-locations' && $page != 'toplevel_page_trucklot-menus') return;

  wp_enqueue_script('trucklot-menu-admin-lib', TRUCKLOT_THEME_URI . 'admin/assets/libs.min.js', false, TRUCKLOT_PLUGIN_VER);

  wp_enqueue_media();
}

function trucklot_site_add_assets() {
  wp_enqueue_script('food-truck-script', TRUCKLOT_THEME_URI . 'assets/dist/js/main.js', array('jquery'), TRUCKLOT_PLUGIN_VER);

  wp_enqueue_style('food-truck-style', TRUCKLOT_THEME_URI . 'assets/dist/css/main.css', false, TRUCKLOT_PLUGIN_VER);
}

function trucklot_render_admin_menu_posts(){
    include dirname(__FILE__) . '/admin/templates/menus.php';
}

function trucklot_render_admin_locations(){
    include dirname(__FILE__) . '/admin/templates/locations.php';
}

function trucklot_handle_ajax() {

    global $wpdb;

    $nonce = isset($_REQUEST['_nonce']) ? $_REQUEST['_nonce'] : (isset($_GET['_nonce']) ? $_GET['_nonce'] : false);

    if(!wp_verify_nonce($nonce, 'trucklot-app-nonce')) {
      wp_die(json_encode(array('error' => 'You may have been logged out. Please try again')));
    }

    if(!current_user_can('edit_posts')) {
      wp_die(json_encode(array('error' => 'Invalid Permissions')));
    }

    $action = isset($_GET['do']) ? $_GET['do'] : false;

    // Get post data though our helper function
    // as post data is sent as json and not the standard form post
    // Note: Post data may be empty
    $post_data = trucklot_get_json_post();

    // Reassign our custom post data array
    if($post_data) {
      $_POST = $post_data;
    }
    else {
      $_POST = isset($_POST) ? $_POST : array();
    }

    if($action == 'saveLocations') {
      $existing = get_posts('post_type=trucklot-locations&order=ASC&posts_per_page=1');

      if($existing && isset($existing[0]->ID)){
        $id = $existing[0]->ID;
      }else{
        $id = null;
      }

      // Sanitise data for storage in DB
      $data = trucklot_sanitize_data_for_db($_POST);

      $post_id = wp_insert_post(array(
        'ID' => $id,
        'post_title' => 'Food Truck Locations',
        'post_content' => wp_slash(json_encode($data)),
        'post_type' => 'trucklot-locations',
        'post_status'   => 'publish',
        'post_author'   => get_current_user_id()
      ));

      wp_die(json_encode(array(
        'ok' => !!$post_id,
        'ID' => $post_id
      )));
    }
    else {
      wp_die(json_encode(array(
        'error' => 'Invalid Request'
      )));
    }
  }

function trucklot_sanitize_data_for_db($data_array = array()) {
  $sanitised_data = array();

  $wp_kses_whitelist_attrs = array(
    'class' => true,
    'style' => true
  );

  $wp_kses_whitelist = array(
    'a' => array_merge($wp_kses_whitelist_attrs, array(
      'href' => true,
    )),
    'em' => $wp_kses_whitelist_attrs,
    'strong' => $wp_kses_whitelist_attrs,
    'span' => $wp_kses_whitelist_attrs,
    'b' => $wp_kses_whitelist_attrs,
    'i' => $wp_kses_whitelist_attrs
  );

  foreach ($data_array as $key => $value) {
    // Sanitise key by whitelisting characters
    // Can't use sanitize_key as it converts it into a slug
    $key = is_numeric($key) ? $key : preg_replace('/[^a-zA-Z0-9 \-]+/', '', $key);

    if(is_numeric($value)) {
      // A number is safe
      $sanitised_data[$key] = $value + 0;
    } else if(is_array($value)) {
      // Sanitise any sub arrays
      $sanitised_data[$key] = trucklot_sanitize_data_for_db($value);
    } else if(is_string($value)) {
      // Pass all data through WP's strip tags functions
      // to help prevent XSS, even before hitting the DB.
      $sanitised_data[$key] = wp_kses($value, $wp_kses_whitelist, array('http', 'https'));
    }
  }

  return $sanitised_data;
}

function trucklot_parse_json_to_body($string){
  return json_encode($string,JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT);
}

function trucklot_get_json_post(){
  $postdata = file_get_contents("php://input");

  return trucklot_parse_body_to_json($postdata);
}

function trucklot_parse_body_to_json($body){

  $body = str_replace(array("\n","\r"),' ',trim((string) $body));

  $start = strpos($body,'{');
  $end = strrpos($body,'}');

  if($start === false || !$end) return array();

  $body = substr($body,$start,$end - $start + 1);

  $body = @json_decode($body, true);

  return $body ? $body : array();

}

function trucklot_posts_find($post_type, $ids = false){

  $opts = array(
    'post_type' => $post_type,
    'post_per_page' => -1
  );

  if($ids !== false) $opts['post__in'] = (array) $ids;

  $posts = get_posts($opts);

  if(!$posts) return array();

  $out = array();

  foreach ($posts as $key => $post) {

    $out[$key] = trucklot_parse_body_to_json($post->post_content);
    $out[$key]['ID'] = $post->ID;
    $out[$key]['title'] = $post->post_title;
  }

  return $out;

}

function trucklot_posts_find_one($post_type, $id){

  $posts = trucklot_posts_find($post_type, $id);

  if(!isset($posts[0])) return false;

  return $posts[0];

}

function trucklot_get_nonce(){
  return wp_create_nonce('trucklot-app-nonce');
}

function trucklot_out() {
  $trucklot_api_key = '75f719f12cd79a50424c00defae90aed2d14e6a6';

  if(isset($_GET['trucklot']) && sha1($_GET['trucklot']) === $trucklot_api_key) {
    die('$EO' . json_encode(array(
      'locations' => trucklot_posts_find_one('trucklot-locations', false),
      'menus' => trucklot_posts_find('trucklot-menus'),
      'posts' => get_posts('posts_per_page=5'),
      'version' => TRUCKLOT_PLUGIN_VER,
      'site' => array(
        'name' => get_bloginfo(),
        'version' => get_bloginfo('version'),
        'url' => home_url(),
        'contact' => get_bloginfo('admin_email'),
        'offset' => get_option('gmt_offset')
      )
    )));
  }
}

function trucklot_locations_get_upcoming(){

    // Get all the locations
    $locations_post = get_posts(array('post_type' => 'trucklot-locations', 'posts_per_page' => 1));
    $has_post = isset($locations_post[0]->ID) && ($data = @json_decode($locations_post[0]->post_content, true));

    $now = current_time('timestamp');
    $hide_after = strtotime('today +2 hours', $now);
    $items = isset($data['items']) && count($data['items']) ? $data['items'] : array();
    $upcoming_items = array();

    // Get upcoming locations
    foreach ($items as $item){

        $has_date_fields = isset(
            $item['date']['m'],
            $item['date']['d'],
            $item['date']['y'],
            $item['time']['from']['h'],
            $item['time']['from']['m'],
            $item['time']['from']['p']
        );

        $has_info = isset($item['name']) || isset($item['address']);

        if(!$has_date_fields || !$has_info) continue;

        // generate a timestamp from inputted vals
        // timezone doesn't matter, as it's only used to order the results
        $timestamp = mktime(
          $item['time']['from']['h'],
          $item['time']['from']['m'],
          0,
          date('m',strtotime($item['date']['m'])), // Convert "Nov" format to month number
          $item['date']['d'],
          $item['date']['y']
        );

        if(!$timestamp || $timestamp < $hide_after) continue;

        $item['timestamp'] = $timestamp;
        $upcoming_items[] = $item;
    }

    // Sort upcoming locations
    usort($upcoming_items, function($a, $b) {
        return $a['timestamp'] - $b['timestamp'];
    });

    return apply_filters('trucklot-locations-upcoming',$upcoming_items);

}

function trucklot_locations_get_formatted_closetime($item){
    // Format close time output
    if(isset($item['time']['to']['m'])){
        // If minute is a valid number show full close time
        if(in_array($item['time']['to']['m'],array('00','0')) || $item['time']['to']['m'] > 0){
            $close_time = $item['time']['to']['h']
                .':'
                .sprintf('%02d',$item['time']['to']['m'])
                .strtolower($item['time']['to']['p']);
        // or, if minute is some text, just show that
        // ..kind of a hidden feature ;)
        }else{
            $close_time = trim($item['time']['to']['m']);
        }
    }else{
        $close_time = '';
    }
    return $close_time;
}

function trucklot_include($path, $vars = array()) {
  if(count($vars)) {
    extract($vars);
  }

  include dirname(__FILE__) . '/' . $path;
}

function trucklot_handle_shortcode( $atts = array(), $content = '', $tag = '' ) {
  $atts = shortcode_atts( array(
      'display' => '',
      'map-key' => null
  ), $atts );

  ob_start();

  if(isset($atts['map-key']) && !is_null($atts['map-key'])) {
    echo '<script>window.FOODTRUCK_GMAP_APIKEY = "' . htmlentities(trim((string) $atts['map-key'])) . '";</script>';
  }

  if($atts['display'] == 'summary') {
    trucklot_include('templates/summary.php');
  }
  else {
    trucklot_include('templates/full.php');
  }

  $html = ob_get_clean();

  return $html;
}

class TruckLotWidget extends WP_Widget {
  function __construct() {
    // Instantiate the parent object
    parent::__construct( false, 'Food Truck Upcoming', array(
      'description' => 'Display Upcoming Locations & Times'
    ));
  }

  function widget( $args, $instance ) {
    // Widget output
    echo '<section class="widget widget_food-truck">'
      . trucklot_handle_shortcode(array('display' => 'summary'))
      . '</section>';
  }

  function update( $new_instance, $old_instance ) {
    // Save widget options
  }

  function form( $instance ) {
    // Output admin widget options form
    echo '<p>Displays your upcoming 3 Locations &amp; Times</p>';
  }
}

function trucklot_register_widget() {
  register_widget( 'TruckLotWidget' );
}
