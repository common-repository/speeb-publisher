<?php
/*
Controller name: Core
Controller description: Basic introspection methods
*/

class JSON_API_Core_Controller {
  
  public function info() {
    global $json_api;
    $php = '';
    if (!empty($json_api->query->controller)) {
      return $json_api->controller_info($json_api->query->controller);
    } else {
      $dir = json_api_dir();
      if (file_exists("$dir/json-api.php")) {
        $php = file_get_contents("$dir/json-api.php");
      } else {
        // Check one directory up, in case json-api.php was moved
        $dir = dirname($dir);
        if (file_exists("$dir/json-api.php")) {
          $php = file_get_contents("$dir/json-api.php");
        }
      }
      if (preg_match('/^\s*Version:\s*(.+)$/m', $php, $matches)) {
        $version = $matches[1];
      } else {
        $version = '(Unknown)';
      }
      $active_controllers = explode(',', get_option('json_api_controllers', 'core'));
      $controllers = array_intersect($json_api->get_controllers(), $active_controllers);
      return array(
        'json_api_version' => $version,
        'controllers' => array_values($controllers)
      );
    }
  }
  
  public function get_recent_posts() {
    global $json_api;
    $val = $this->authenticate_api_key();
    $posts = $json_api->introspector->get_posts();
    return $this->posts_result($posts);
  }
  
  public function get_post() {
    global $json_api, $post;
    $val = $this->authenticate_api_key();
    extract($json_api->query->get(array('id', 'slug', 'post_id', 'post_slug')));
    if ($id || $post_id) {
      if (!$id) {
        $id = $post_id;
      }
      $posts = $json_api->introspector->get_posts(array(
        'p' => $id
      ), true);
    } else if ($slug || $post_slug) {
      if (!$slug) {
        $slug = $post_slug;
      }
      $posts = $json_api->introspector->get_posts(array(
        'name' => $slug
      ), true);
    } else {
      $json_api->error("Include 'id' or 'slug' var in your request.");
    }
    if (count($posts) == 1) {
      $post = $posts[0];
      $previous = get_adjacent_post(false, '', true);
      $next = get_adjacent_post(false, '', false);
      $post = new JSON_API_Post($post);
      $response = array(
        'post' => $post
      );
      if ($previous) {
        $response['previous_url'] = get_permalink($previous->ID);
      }
      if ($next) {
        $response['next_url'] = get_permalink($next->ID);
      }
      return $response;
    } else {
      $json_api->error("Not found.");
    }
  }

  public function get_page() {
    global $json_api;
    $val = $this->authenticate_api_key();
    extract($json_api->query->get(array('id', 'slug', 'page_id', 'page_slug', 'children')));
    if ($id || $page_id) {
      if (!$id) {
        $id = $page_id;
      }
      $posts = $json_api->introspector->get_posts(array(
        'page_id' => $id
      ));
    } else if ($slug || $page_slug) {
      if (!$slug) {
        $slug = $page_slug;
      }
      $posts = $json_api->introspector->get_posts(array(
        'pagename' => $slug
      ));
    } else {
      $json_api->error("Include 'id' or 'slug' var in your request.");
    }
    
    // Workaround for https://core.trac.wordpress.org/ticket/12647
    if (empty($posts)) {
      $url = $_SERVER['REQUEST_URI'];
      $parsed_url = parse_url($url);
      $path = $parsed_url['path'];
      if (preg_match('#^http://[^/]+(/.+)$#', get_bloginfo('url'), $matches)) {
        $blog_root = $matches[1];
        $path = preg_replace("#^$blog_root#", '', $path);
      }
      if (substr($path, 0, 1) == '/') {
        $path = substr($path, 1);
      }
      $posts = $json_api->introspector->get_posts(array('pagename' => $path));
    }
    
    if (count($posts) == 1) {
      if (!empty($children)) {
        $json_api->introspector->attach_child_posts($posts[0]);
      }
      return array(
        'page' => $posts[0]
      );
    } else {
      $json_api->error("Not found.");
    }
  }
  
  public function get_date_posts() {
    global $json_api;
    $val = $this->authenticate_api_key();
    if ($json_api->query->date) {
      $date = preg_replace('/\D/', '', $json_api->query->date);
      if (!preg_match('/^\d{4}(\d{2})?(\d{2})?$/', $date)) {
        $json_api->error("Specify a date var in one of 'YYYY' or 'YYYY-MM' or 'YYYY-MM-DD' formats.");
      }
      $request = array('year' => substr($date, 0, 4));
      if (strlen($date) > 4) {
        $request['monthnum'] = (int) substr($date, 4, 2);
      }
      if (strlen($date) > 6) {
        $request['day'] = (int) substr($date, 6, 2);
      }
      $posts = $json_api->introspector->get_posts($request);
    } else {
      $json_api->error("Include 'date' var in your request.");
    }
    return $this->posts_result($posts);
  }
  
  public function get_category_posts() {
    global $json_api;
    $val = $this->authenticate_api_key();
    $category = $json_api->introspector->get_current_category();
    if (!$category) {
      $json_api->error("Not found.");
    }
    $posts = $json_api->introspector->get_posts(array(
      'cat' => $category->id
    ));
    return $this->posts_object_result($posts, $category);
  }
  
   public function ping() {
      
	global $json_api;
	global $wpdb;
    $table_name = $wpdb->prefix . "speeb";
    $api_key = str_replace("-","",$_REQUEST['api_key']);
    $api_key = trim($api_key);
    $sql        = "select * from ".$table_name." where api_key='$api_key'";
    $SQLQuery   = $wpdb->query($sql);
    
    
	if ($SQLQuery==0 || $SQLQuery==''){
    echo '{"status":"error","error":"failed"}';
	exit();
    }else {
    echo '{"status":"success","success":"ok"}';
    exit();
	}

	 
   
  }
  
  
  public function get_tag_posts() {
    global $json_api;
    $val = $this->authenticate_api_key();
    $tag = $json_api->introspector->get_current_tag();
    if (!$tag) {
      $json_api->error("Not found.");
    }
    $posts = $json_api->introspector->get_posts(array(
      'tag' => $tag->slug
    ));
    return $this->posts_object_result($posts, $tag);
  }
  
  public function get_author_posts() {
    global $json_api;
    $val = $this->authenticate_api_key();
    $author = $json_api->introspector->get_current_author();
    if (!$author) {
      $json_api->error("Not found.");
    }
    $posts = $json_api->introspector->get_posts(array(
      'author' => $author->id
    ));
    return $this->posts_object_result($posts, $author);
  }
  
  public function get_search_results() {
    global $json_api;
    $val = $this->authenticate_api_key();
    if ($json_api->query->search) {
      $posts = $json_api->introspector->get_posts(array(
        's' => $json_api->query->search
      ));
    } else {
      $json_api->error("Include 'search' var in your request.");
    }
    return $this->posts_result($posts);
  }
  
  public function get_date_index() {
    global $json_api;
    $val = $this->authenticate_api_key();
    $permalinks = $json_api->introspector->get_date_archive_permalinks();
    $tree = $json_api->introspector->get_date_archive_tree($permalinks);
    return array(
      'permalinks' => $permalinks,
      'tree' => $tree
    );
  }
  
  public function get_category_index() {
    global $json_api;
    $val = $this->authenticate_api_key();
    $categories = $json_api->introspector->get_categories();
    return array(
      'count' => count($categories),
      'categories' => $categories
    );
  }
  
  public function get_tag_index() {
    global $json_api;
    $val = $this->authenticate_api_key();
    $tags = $json_api->introspector->get_tags();
    return array(
      'count' => count($tags),
      'tags' => $tags
    );
  }
  
  public function get_author_index() {
    global $json_api;
    $val = $this->authenticate_api_key();
    $authors = $json_api->introspector->get_authors();
    return array(
      'count' => count($authors),
      'authors' => array_values($authors)
    );
  }
  
  public function get_page_index() {
    global $json_api;
    $val = $this->authenticate_api_key();
    $pages = array();
    // Thanks to blinder for the fix!
    $numberposts = empty($json_api->query->count) ? -1 : $json_api->query->count;
    $wp_posts = get_posts(array(
      'post_type' => 'page',
      'post_parent' => 0,
      'order' => 'ASC',
      'orderby' => 'menu_order',
      'numberposts' => $numberposts
    ));
    foreach ($wp_posts as $wp_post) {
      $pages[] = new JSON_API_Post($wp_post);
    }
    foreach ($pages as $page) {
      $json_api->introspector->attach_child_posts($page);
    }
    return array(
      'pages' => $pages
    );
  }
  
   public function get_nonce() {
    global $json_api;
    
    extract($json_api->query->get(array('controller', 'method')));
    if ($controller && $method) {
      $controller = strtolower($controller);
      if (!in_array($controller, $json_api->get_controllers())) {
        $json_api->error("Unknown controller '$controller'.");
      }
      require_once $json_api->controller_path($controller);
      if (!method_exists($json_api->controller_class($controller), $method)) {
        $json_api->error("Unknown method '$method'.");
      }
            
      $nonce_id = $json_api->get_nonce_id($controller, $method);	    
       if (!is_user_logged_in() ) {
	  	$this->authenticate();
	  	$params      = $_SERVER['QUERY_STRING'];
	  	$url 		 = "?".$params;
	    header("Location:". $url);
	    exit();
	    }      	    
	    
      return array(
        'controller' => $controller,
        'method' => $method,
        'nonce' => wp_create_nonce($nonce_id)
      );
    } else {
      $json_api->error("Include 'controller' and 'method' vars in your request.");
    }    
    
  }
  
    private function authenticate() {
    global $json_api;
  	global $wpdb;
    $table_name = $wpdb->prefix . "speeb";
    $api_key = str_replace("-","",$_REQUEST['api_key']);
    $sql        = "select * from ".$table_name." where api_key='$api_key'";
    $SQLQuery   = $wpdb->query($sql);
    
    if ($SQLQuery>=1){
    $row        = $wpdb->get_row($sql);
    $json_api->query->author        = $row->username;
    $json_api->query->user_password = base64_decode($row->password);
    }else{
    $json_api->error("Invalid api key.Please send proper api key");
    exit();
    }
       
    if ($json_api->query->author && $json_api->query->user_password) {
      $user = wp_signon(array('user_login' => $json_api->query->author, 'user_password' => $json_api->query->user_password));
      if (get_class($user) == 'WP_Error') {
        $json_api->error($user->errors);
      } else {
        if (!user_can($user->ID,'edit_posts')) {
          $json_api->error("You need to login with a user capable of creating posts.");
        }
      }
    } else {
      if (!current_user_can('edit_posts')) {
        $json_api->error("You need to login with a user capable of creating posts.");
      }
    }
  }
  
  
  
  protected function get_object_posts($object, $id_var, $slug_var) {
    global $json_api;
    $val = $this->authenticate_api_key();
    $object_id = "{$type}_id";
    $object_slug = "{$type}_slug";
    extract($json_api->query->get(array('id', 'slug', $object_id, $object_slug)));
    if ($id || $$object_id) {
      if (!$id) {
        $id = $$object_id;
      }
      $posts = $json_api->introspector->get_posts(array(
        $id_var => $id
      ));
    } else if ($slug || $$object_slug) {
      if (!$slug) {
        $slug = $$object_slug;
      }
      $posts = $json_api->introspector->get_posts(array(
        $slug_var => $slug
      ));
    } else {
      $json_api->error("No $type specified. Include 'id' or 'slug' var in your request.");
    }
    return $posts;
  }
  
  protected function posts_result($posts) {
    global $wp_query;
    $val = $this->authenticate_api_key();
    return array(
      'count' => count($posts),
      'count_total' => (int) $wp_query->found_posts,
      'pages' => $wp_query->max_num_pages,
      'posts' => $posts
    );
  }
  
  protected function posts_object_result($posts, $object) {
    global $wp_query;
    $val = $this->authenticate_api_key();
    // Convert something like "JSON_API_Category" into "category"
    $object_key = strtolower(substr(get_class($object), 9));
    return array(
      'count' => count($posts),
      'pages' => (int) $wp_query->max_num_pages,
      $object_key => $object,
      'posts' => $posts
    );
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
