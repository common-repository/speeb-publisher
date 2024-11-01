<?php

class JSON_API {
  
  function __construct() {
   error_reporting(0);
    $this->query = new JSON_API_Query();
    $this->introspector = new JSON_API_Introspector();
    $this->response = new JSON_API_Response();
    add_action('template_redirect', array(&$this, 'template_redirect'));
    add_action('admin_menu', array(&$this, 'admin_menu'));
    add_action('update_option_json_api_base', array(&$this, 'flush_rewrite_rules'));
    add_action('pre_update_option_json_api_controllers', array(&$this, 'update_controllers'));
  }
  
  
 
  
  function template_redirect() {
    // Check to see if there's an appropriate API controller + method    
    $controller = strtolower($this->query->get_controller());
    $available_controllers = $this->get_controllers();
    $enabled_controllers = explode(',', get_option('json_api_controllers', 'posts'));
    

	
	
	$active_controllers = array_intersect($available_controllers, $enabled_controllers);
    

    if ($controller) {
      
      if (!in_array($controller, $active_controllers)) {
        $this->error("Unknown controller '$controller'.");
      }
      
      $controller_path = $this->controller_path($controller);
      if (file_exists($controller_path)) {
        require_once $controller_path;
      }
      $controller_class = $this->controller_class($controller);
      
      if (!class_exists($controller_class)) {
        $this->error("Unknown controller '$controller_class'.");
      }
      
      $this->controller = new $controller_class();
      $method = $this->query->get_method($controller);
      
      if ($method) {
        
        $this->response->setup();
        
        // Run action hooks for method
        do_action("json_api-{$controller}-$method");
        
        // Error out if nothing is found
        if ($method == '404') {
          $this->error('Not found');
        }
        
        // Run the method
        $result = $this->controller->$method();
        
        $this->response->respond($result);
        
        // Handle the result
        // Done!
        exit;
      }
    }
  }
  
  function admin_menu() {
    add_options_page('Speeb-Publisher Settings', 'Speeb-Publisher', 'manage_options', 'json-api', array(&$this, 'admin_options'));
  }
  
   function ping($url,$port=80,$timeout=30)
	{
      
   
	  
	 
		if(substr($link,0,4)!="http"){ 
			$link = "http://".$link;
			}
		
			$timestart = microtime();
		
			$churl = @fopen($link,'r');
		
			$timeend = microtime();
			$diff = number_format(((substr($timeend,0,9)) + (substr($timeend,-10)) - (substr($timestart,0,9)) - (substr($timestart,-10))),4);
			$diff = $diff*100;
		
			if (!$churl) {
				$message="FALSE";
			}else{
				$message="TRUE";
			}
		
			fclose($churl); 
			return  $message;	
			
			
			
	}
  
  
  
  
  function admin_options() {
    if (!current_user_can('manage_options'))  {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    
    $available_controllers = $this->get_controllers();
    $active_controllers = explode(',', get_option('json_api_controllers', 'core'));
    
    if (count($active_controllers) == 1 && empty($active_controllers[0])) {
      $active_controllers = array();
    }
    
    if (!empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], "update-options")) {
      if ((!empty($_REQUEST['action']) || !empty($_REQUEST['action2'])) &&
          (!empty($_REQUEST['controller']) || !empty($_REQUEST['controllers']))) {
        if (!empty($_REQUEST['action'])) {
          $action = $_REQUEST['action'];
        } else {
          $action = $_REQUEST['action2'];
        }
        
        if (!empty($_REQUEST['controllers'])) {
          $controllers = $_REQUEST['controllers'];
        } else {
          $controllers = array($_REQUEST['controller']);
        }
        
        foreach ($controllers as $controller) {
          if (in_array($controller, $available_controllers)) {
            if ($action == 'activate' && !in_array($controller, $active_controllers)) {
              $active_controllers[] = $controller;
            } else if ($action == 'deactivate') {
              $index = array_search($controller, $active_controllers);
              if ($index !== false) {
                unset($active_controllers[$index]);
              }
            }
          }
        }
        $this->save_option('json_api_controllers', implode(',', $active_controllers));
      }
      if (isset($_REQUEST['json_api_base'])) {
        $this->save_option('json_api_base', $_REQUEST['json_api_base']);
      }
    }
	
	
	 
    global $wpdb;
    global $current_user;
	$table_name = $wpdb->prefix . "speeb";
    get_currentuserinfo();
    
   
   
   
    if (isset($_REQUEST['authentication_json'])){

    	$api_key    = trim(mysql_real_escape_string($_POST['api_key']));
	    $username   = trim(mysql_real_escape_string($_POST['username']));
	    $password   = trim(mysql_real_escape_string($_POST['password']));
		
		$res        = $this->checkTheUserAuthentication($username,$password);
		
		if($res=='FALSE'){
		$this->showMessage("The username or password you entered is incorrect");

		}else{
		
		
    	
	    if( !empty($api_key) && !empty($username) && !empty($password))
	    {
        $password   = base64_encode($password);
	    //$password = base64_decode($password); 
	    $api_key = str_replace("-","",$api_key);
    	
			if($wpdb->query("INSERT INTO $table_name (api_key, username,password) VALUES('$api_key','$username','$password')")){
			$this->showMessage("API key and credentials stored successfully. <br/> That's it! You're now ready to post with Speeb.");
			}else{
			 $this->showMessage("Something went wrong.Please try again.");
			}
	    
		}else{
	     $this->showMessage("Please enter all the mandatory fields.");
	    }
		
	}	
   	     
    } elseif (isset($_REQUEST['edit_authentication_json'])){

    	$api_key    = trim(mysql_real_escape_string($_POST['api_key']));
	    $username   = trim(mysql_real_escape_string($_POST['username']));
	    $password   = trim(mysql_real_escape_string($_POST['password']));
    	
		
		$res        = $this->checkTheUserAuthentication($username,$password);
		
		if($res=='FALSE'){
		$this->showMessage("The username or password you entered is incorrect");
		} else{
		
		
	    if( !empty($api_key) && !empty($username) && !empty($password))
	    {
        $password   = base64_encode($password);
	    //$password = base64_decode($password); 
	    $api_key = str_replace("-","",$api_key);
    	 $wpdb->options=$table_name;
	  

        $data_array = array('username' => $username, 'password' => $password, 'api_key' => $api_key);
        $where = array('id' => 1);
        
	
			if($wpdb->update( $table_name, $data_array, $where )){
			$this->showMessage("API key and credentials updated successfully. <br/> That's it! You're now ready to post with Speeb.");
			}else{
			 $this->showMessage("API key and credentials updated successfully. <br/> That's it! You're now ready to post with Speeb.");
			}

		}else{
	     $this->showMessage("Please enter all the mandatory fields.");
	    }  	 
		
	  }	    
    }
	
	$sql        = "select * from ".$table_name;
    $SQLQuery   = $wpdb->query($sql);
    
    ?>
<div class="wrap">
  <div id="icon-options-general" class="icon32"><br /></div>
  <h2>Speeb-Publisher Settings</h2>
  <?php if ($SQLQuery==0 || $SQLQuery==''){  include_once "authentication.php"; }
        else{   include_once "edit_authentication.php";   }
   ?>
  
  
</div>
<?php
  }
  
  function print_controller_actions($name = 'action') {
    ?>
    <div class="tablenav">
      <div class="alignleft actions">
        <select name="<?php echo $name; ?>">
          <option selected="selected" value="-1">Bulk Actions</option>
          <option value="activate">Activate</option>
          <option value="deactivate">Deactivate</option>
        </select>
        <input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Apply">
      </div>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
    <?php
  }
  
  function get_method_url($controller, $method, $options = '') {
    $url = get_bloginfo('url');
    $base = get_option('json_api_base', 'api');
    $permalink_structure = get_option('permalink_structure', '');
    if (!empty($options) && is_array($options)) {
      $args = array();
      foreach ($options as $key => $value) {
        $args[] = urlencode($key) . '=' . urlencode($value);
      }
      $args = implode('&', $args);
    } else {
      $args = $options;
    }
    if ($controller != 'core') {
      $method = "$controller/$method";
    }
    if (!empty($base) && !empty($permalink_structure)) {
      if (!empty($args)) {
        $args = "?$args";
      }
      return "$url/$base/$method/$args";
    } else {
      return "$url?json=$method&$args";
    }
  }
  
  function save_option($id, $value) {
    $option_exists = (get_option($id, null) !== null);
    if ($option_exists) {
      update_option($id, $value);
    } else {
      add_option($id, $value);
    }
  }
  
  function get_controllers() {
    $controllers = array();
    $dir = json_api_dir();
    $dh = opendir("$dir/controllers");
    while ($file = readdir($dh)) {
      if (preg_match('/(.+)\.php$/', $file, $matches)) {
        $controllers[] = $matches[1];
      }
    }
    $controllers = apply_filters('json_api_controllers', $controllers);
    return array_map('strtolower', $controllers);
  }
  
  function controller_is_active($controller) {
    if (defined('JSON_API_CONTROLLERS')) {
      $default = JSON_API_CONTROLLERS;
    } else {
      $default = 'core';
    }
    $active_controllers = explode(',', get_option('json_api_controllers', $default));
    return (in_array($controller, $active_controllers));
  }
  
  function update_controllers($controllers) {
    if (is_array($controllers)) {
      return implode(',', $controllers);
    } else {
      return $controllers;
    }
  }
  
  function controller_info($controller) {
    $path = $this->controller_path($controller);
    $class = $this->controller_class($controller);
    $response = array(
      'name' => $controller,
      'description' => '(No description available)',
      'methods' => array()
    );
    if (file_exists($path)) {
      $source = file_get_contents($path);
      if (preg_match('/^\s*Controller name:(.+)$/im', $source, $matches)) {
        $response['name'] = trim($matches[1]);
      }
      if (preg_match('/^\s*Controller description:(.+)$/im', $source, $matches)) {
        $response['description'] = trim($matches[1]);
      }
      if (preg_match('/^\s*Controller URI:(.+)$/im', $source, $matches)) {
        $response['docs'] = trim($matches[1]);
      }
      if (!class_exists($class)) {
        require_once($path);
      }
      $response['methods'] = get_class_methods($class);
      return $response;
    } else if (is_admin()) {
      return "Cannot find controller class '$class' (filtered path: $path).";
    } else {
      $this->error("Unknown controller '$controller'.");
    }
    return $response;
  }
  
  function controller_class($controller) {
    return "json_api_{$controller}_controller";
  }
  
  function controller_path($controller) {
    $dir = json_api_dir();
    $controller_class = $this->controller_class($controller);
    return apply_filters("{$controller_class}_path", "$dir/controllers/$controller.php");
  }
  
  function get_nonce_id($controller, $method) {
    $controller = strtolower($controller);
    $method = strtolower($method);
    return "json_api-$controller-$method";
  }
  
  function flush_rewrite_rules() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
  }
  
  function error($message = 'Unknown error', $status = 'error') {
    $this->response->respond(array(
      'error' => $message
    ), $status);
  }
  
  function include_value($key) {
    return $this->response->is_value_included($key);
  }
  
  
    function showMessage($message, $errormsg = false)
  {
	if ($errormsg) {
		echo '<div id="message" class="error">';
	}
	else {
		echo '<div id="message" class="updated fade">';
	}

	echo "<p><strong>$message</strong></p></div>";
	add_action('admin_notices', 'showAdminMessages'); 
  }
  
  
  function checkTheUserAuthentication($username,$password) {

		$user = wp_authenticate($username, $password);
		if ( is_wp_error($user)) {
		  return 'FALSE';
		}else{
		   return 'TRUE';
		   }
	}
  
  
}

?>
