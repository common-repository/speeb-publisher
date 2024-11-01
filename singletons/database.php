<?php
/**
 * Description: Creates database tables used by DiveBook
 * Author: Per Ola Saether
 */

//Database table versions
global $divebook_db_table_dive_version;
$divebook_db_table_dive_version = "1.0";

//Create database tables needed by the DiveBook widget
function divebook_db_create () {
    divebook_create_table_dive();
}

//Create dive table
function divebook_create_table_dive(){
    //Get the table name with the WP database prefix
    global $wpdb;
    $table_name = $wpdb->prefix . "speeb";
    global $divebook_db_table_dive_version;
    $installed_ver = get_option( "divebook_db_table_dive_version" );
     //Check if the table already exists and if the table is up to date, if not create it
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name
            ||  $installed_ver != $divebook_db_table_dive_version ) {
       $sql = "CREATE TABLE " . $table_name . " (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              api_key varchar(200) NOT NULL,
              username varchar(200) NOT NULL,
              password varchar(200) NOT NULL,
              UNIQUE KEY id (id)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        update_option( "divebook_db_table_dive_version", $divebook_db_table_dive_version );
}
    //Add database table versions to options
    add_option("divebook_db_table_dive_version", $divebook_db_table_dive_version);
}

// Activation process

	global $wpdb;
	$table_name = $wpdb->prefix . "options";  
	$sql        = "select * from ".$table_name." where option_name='json_api_controllers' and autoload='yes'";
	$SQLQuery   = $wpdb->query($sql);
	if ($SQLQuery==0 || $SQLQuery=='' ){
	//echo "tseteest";
	  $cont = "core,posts,respond";
	  $wpdb->query("INSERT INTO $table_name (option_name, option_value) VALUES('json_api_controllers','$cont')");
	 }else{
	 //echo "testst2111";
	    $cont = "core,posts,respond";
	 	$data_array = array('option_value' => $cont);
        $where = array('option_name' => 'json_api_controllers');
        $wpdb->update( $table_name, $data_array, $where );		
	 }

//exit();


?>
