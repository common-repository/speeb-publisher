<?php
/*
Plugin Name: Speeb Publisher
Plugin URI: http://www.speeb.com
Description: The Speeb Publisher Worpress Plugin allows you to publish sponsored posts from Speeb.com on your website and to monetize your blog. To use this plugin, register at http://speeb.com and get your personal Speeb API code.
Version: 1.0.3.3
Author: Speeb
Author URI: http://www.speeb.com
License: GPLv2
*/
//wp_enqueue_script('theirscript', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
//ob_clean();
$dir = json_api_dir();
@include_once "$dir/singletons/api.php";
@include_once "$dir/singletons/query.php";
@include_once "$dir/singletons/introspector.php";
@include_once "$dir/singletons/response.php";
@include_once "$dir/models/post.php";
@include_once "$dir/models/comment.php";
@include_once "$dir/models/category.php";
@include_once "$dir/models/tag.php";
@include_once "$dir/models/author.php";
@include_once "$dir/models/attachment.php";


function json_warning() {
			echo "
			<div id=\"json-api-warning\" class=\"updated fade\">
			<p><strong>".__('Speeb-Publisher is almost ready.')."</strong> ".sprintf(__('You must <a href="%1$s">enter your speeb.com API key</a> for it to work.'), "options-general.php?page=json-api")."</p></div>
			";
		}
		
		
	function json_login_warning() {
			echo "<div id=\"json-api-warning\" class=\"updated fade\"><p>".sprintf(__('Your blog login credentials have been changed. So Please update the Speeb-Publisher login credentials as well. <a href="%1$s">Please click here to enter the Speeb-Publisher setting&#39;s page.'), "options-general.php?page=json-api")."</p></div>";
		}	
		
		
		
/* function checkUserAuthentication($username,$password) {

		$user = wp_authenticate($username, $password);
		if ( is_wp_error($user)) {
		  return 'FALSE';
		}else{
		   return 'TRUE';
		   }
	}*/
		

		
function json_api_init() {
  global $json_api;
  if (phpversion() < 5) {
    add_action('admin_notices', 'json_api_php_version_warning');
    return;
  }
  if (!class_exists('JSON_API')) {
    add_action('admin_notices', 'json_api_class_warning');
    return;
  }
 
  add_filter('rewrite_rules_array', 'json_api_rewrites');
  $json_api = new JSON_API();
}

function json_api_php_version_warning() {
  echo "<div id=\"json-api-warning\" class=\"updated fade\"><p>Sorry, Speeb-Publisher requires PHP version 5.0 or greater.</p></div>";
}

function json_api_class_warning() {
  echo "<div id=\"json-api-warning\" class=\"updated fade\"><p>Oops, Speeb-Publisher class not found. If you've defined a JSON_API_DIR constant, double check that the path is correct.</p></div>";
}

function json_api_activation() {
  // Add the rewrite rule on activation
  global $wp_rewrite;
  //include_once dirname( __FILE__ ).'controller/posts.php';
  //register_activation_hook( __FILE__, array( 'JSON_API_Posts_Controller', 'create_post' ) );
  //include_once dirname( __FILE__ ).'controller/respond.php';
  //register_activation_hook( __FILE__, array( 'JSON_API_Respond_Controller', 'submit_comment' ) );
 
  add_filter('rewrite_rules_array', 'json_api_rewrites');
  $wp_rewrite->flush_rules();
}

function json_api_deactivation() {
  // Remove the rewrite rule on deactivation
  global $wp_rewrite;
  global $wpdb;
  $wp_rewrite->flush_rules();
  $table_name = $wpdb->prefix . "speeb";
  $sql = "DROP TABLE " . $table_name;
  $wpdb->query($sql);
    
}

function json_api_rewrites($wp_rules) {
  $base = get_option('json_api_base', 'api');
  if (empty($base)) {
    return $wp_rules;
  }
  $json_api_rules = array(
    "$base\$" => 'index.php?json=info',
    "$base/(.+)\$" => 'index.php?json=$matches[1]'
  );
  return array_merge($json_api_rules, $wp_rules);
}

function json_api_dir() {
  if (defined('JSON_API_DIR') && file_exists(JSON_API_DIR)) {
    return JSON_API_DIR;
  } else {
    return dirname(__FILE__);
  }
}


// Add initialization and activation hooks
add_action('init', 'json_api_init');

include("$dir/singletons/database.php");  
//Activation hook so the DB is created when plugin is activated
register_activation_hook(__FILE__,'divebook_db_create');

	global $wpdb;
	$table_name = $wpdb->prefix . "speeb";  
	$sql        = "select * from ".$table_name;
	$SQLQuery   = $wpdb->query($sql);
	if ($SQLQuery==0 || $SQLQuery=='' ){
		if(!isset($_POST['authentication_json'])){
		add_action('admin_notices', 'json_warning');
	  }
	}  else  {
			$row           = $wpdb->get_row($sql);
    		$author        = $row->username;
    		$user_password = base64_decode($row->password);
			/*$val           = checkUserAuthentication($author,$user_password);		
			
			if($val=='FALSE'){
			add_action('admin_notices', 'json_login_warning');
			}*/		
	}

register_activation_hook("$dir/json-api.php", 'json_api_activation');
register_deactivation_hook("$dir/json-api.php", 'json_api_deactivation');
  
	

?>
