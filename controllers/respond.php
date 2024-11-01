<?php
/*
Controller name: Respond
Controller description: Comment/trackback submission methods
*/

class JSON_API_Respond_Controller {
  
  function submit_comment() {
    global $json_api;
    $val = $this->authenticate_api_key();
    nocache_headers();
    if (empty($_REQUEST['post_id'])) {
      $json_api->error("No post specified. Include 'post_id' var in your request.");
    } else if (empty($_REQUEST['name']) ||
               empty($_REQUEST['email']) ||
               empty($_REQUEST['content'])) {
      $json_api->error("Please include all required arguments (name, email, content).");
    } else if (!is_email($_REQUEST['email'])) {
      $json_api->error("Please enter a valid email address.");
    }
    $pending = new JSON_API_Comment();
    return $pending->handle_submission();
  }
  
private function authenticate_api_key() {
    global $json_api;
	global $wpdb;
    $table_name = $wpdb->prefix . "speeb";
    $api_key = str_replace("-","",$_REQUEST['api_key']);
    $api_key = trim($api_key);
    $sql        = "select * from ".$table_name." where api_key='$api_key'";
    $SQLQuery   = $wpdb->query($sql);
    
    if ($SQLQuery==0 || $SQLQuery==''){
    $json_api->error("Please send the proper API Key.");
    }else {
    	return 'TRUE';
    }
   
  }
  
  
}

?>
