
<style type="text/css">

.speeb_wrapper{
	width:100%;
	height:240px;
	}

#speeb_outer{
	width:90%;
	height:305px;
	margin:1% 0 0 2%;
	padding:6px;
	background:#e5e5e5;
	border:1px #0094c2 solid;
	border-radius:10px;
	-moz-border-radius:10px;
	-webkit-border-radius:10px;
	}
	
.speeb_api_txt{
	font-family:Arial, Helvetica, sans-serif;
	font-size:14px;
	color:#666;
	font-weight:bold;
	float:left;
	width:25%;
	margin:20px 2px 0 10px;
	}
	
.speeb_login_txt{
	font-family:Arial, Helvetica, sans-serif;
	font-size:14px;
	color:#0094c2;
	font-weight:bold;
	float:left;
	width:94%;
	margin:22px 5px 0 10px;
	}	
	
.speeb_text_box{
	width:40%;
	height:20px;
	padding:2px;
	margin:18px 30% 0 0;
	float:right;
	border-radius:6px;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border:1px #0094c2 solid;
	background: rgb(234,234,234); /* Old browsers */
/* IE9 SVG, needs conditional override of 'filter' to 'none' */
background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2VhZWFlYSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjMxJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgPC9saW5lYXJHcmFkaWVudD4KICA8cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIiBmaWxsPSJ1cmwoI2dyYWQtdWNnZy1nZW5lcmF0ZWQpIiAvPgo8L3N2Zz4=);
background: -moz-linear-gradient(top,  rgba(234,234,234,1) 0%, rgba(255,255,255,1) 31%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(234,234,234,1)), color-stop(31%,rgba(255,255,255,1))); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  rgba(234,234,234,1) 0%,rgba(255,255,255,1) 31%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  rgba(234,234,234,1) 0%,rgba(255,255,255,1) 31%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  rgba(234,234,234,1) 0%,rgba(255,255,255,1) 31%); /* IE10+ */
background: linear-gradient(to bottom,  rgba(234,234,234,1) 0%,rgba(255,255,255,1) 31%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#eaeaea', endColorstr='#ffffff',GradientType=0 ); /* IE6-8 */

	}
	
	.button-speeb-authentication{
	float:right;
	width:75px;
	height:30px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	color:#fff;
	font-weight:bold;
	cursor:pointer;
	outline:none;
	background:#333333;
	border:none;
	margin:1% 62% 0 0;}
			


.speeb_heading{
font-family:Arial, Helvetica, sans-serif;
font-size:18px;
color:#000000;
}

.speeb_heading2{
font-family:Arial, Helvetica, sans-serif;
font-size:13px;
color:#000000;
font-weight:bold;
padding-left:10px;
}

.inside_text{
font-family:Arial, Helvetica, sans-serif;
font-size:12px;
color:#000000;}

/*body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}*/
</style>

<?php
	global $wpdb;
    $table_name = $wpdb->prefix . "speeb";
    $sql        = "select * from ".$table_name;
    $SQLQuery   = $wpdb->query($sql);
    $row        = $wpdb->get_row($sql);   

?>
<form name="json_authentication" method="post" action="#">
<div class="speeb_wrapper">
<div id="speeb_outer">
<span class="speeb_api_txt">API Key:</span>
<input type="text" name="api_key" id="api_key" class="speeb_text_box" value="<?php echo trim($row->api_key);?>" />
<span class="speeb_login_txt">Save the credentials below to authorize Speeb to post sponsor content and external links on your site:</span>
<span class="speeb_api_txt">Wordpress Username:</span><input type="text" name="username" id="textfield" class="speeb_text_box"
 value="<?php echo $row->username; ?>" /><br>
<span class="speeb_api_txt">Wordpress Password:</span><input type="password" name="password" id="textfield" class="speeb_text_box" value="<?php echo trim(base64_decode($row->password));?>" />
<input type="submit" class="button-speeb-authentication" value="Save" name="edit_authentication_json" style="background:#333333; font-family:Arial, Helvetica, sans-serif; color:#FFFFFF" />
<table width="100%" border="0" align="left" cellpadding="4" cellspacing="4">

  <?php
  $host = 'http://www.speeb.com';
  $up = wp_remote_post( $host, $args ); 
  ?>
 
 
  <tr>
    <td width="290" align="left" valign="middle">&nbsp;</td>
    <td width="300" align="left" valign="middle"><table width="100%" border="0" align="center" cellpadding="1" cellspacing="1">
      <tr>
        <td height="30" align="center" valign="middle" class="inside_text"><b>&nbsp;</b></td>
      </tr>

	  <tr>
        <td align="center" valign="middle" bgcolor="#66CC00" class="speeb_heading2"> 
		Speeb Reachable :<?php if($up['response']['code']==200) { ?> Ok <?php } else { ?> Error <?php } ?></td>
      </tr>
     	  
    </table></td>
    <td width="250" align="left" valign="middle">&nbsp;</td>
  </tr>
</table>
</div>
</div>
</form>