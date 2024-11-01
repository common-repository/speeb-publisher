<?php
/*
Controller name: Posts
Controller description: Data manipulation methods for posts
*/

class JSON_API_Posts_Controller {

  public function create_post() {
    global $json_api;
   $val = $this->authenticate_api_key();
    
    if($val=='TRUE'){ 
    if (!$json_api->query->nonce) {
      $json_api->error("You must include a 'nonce' value to create posts. Use the `get_nonce` Core API method.");
    }
    $nonce_id = $json_api->get_nonce_id('posts', 'create_post');
    if (!wp_verify_nonce($json_api->query->nonce, $nonce_id)) {
      $json_api->error("Your 'nonce' value was incorrect. Use the 'get_nonce' API method.");
    }
    nocache_headers();
    $post = new JSON_API_Post();
	
	/*$speeb_id = $_REQUEST['speeb_id'];
	$tracking = "<script type='text/javascript'>
				$(document).ready(function() {
 				_gaq.push(['_trackEvent', 'Speeb', 'Page_view', 'speeb_id', ".$speeb_id.", false]);
				});
			   </script>";
	
	$_REQUEST['content'] =$_REQUEST['content'].$tracking;*/
	
//	print_r($_REQUEST);
	//exit();
	
	/*
	if(isset($_FILES['attachment'])){
	  $filename 	= $_FILES['attachment'];
	  $content  	= $_REQUEST['content'];
	  $path     	= '';
	  $path     	= date("Y/m");
	  $path     	= 'wp-content/uploads/'.$path."/".$filename['name'];	  
	  $img      	= "<img src=".$path.">";
	  $img_content  = '<span style="float:left;margin-right:10px;max-height:180px;max-width:238px;overflow:hidden;">'.$img.'</span>';	  
	  $content  	= $img_content.$content;
	  $_REQUEST['content'] = $content; 	  
    }*/
	

    $id = $post->create($_REQUEST);
    wp_logout();
    if (empty($id)) {
      $json_api->error("Could not create post.");
    }
    return array(
      'post' => $post
    );
    
    }
    
  }
  

  
  /**
   * Attempts to authenticate  using api key
   *
   * @return void
   * @author Narasinha Joshi Charmpilas
   */
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
