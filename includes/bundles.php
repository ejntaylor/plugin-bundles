<?php






	
function mm_get_plugins($plugins)
{
    $args = array(
            'path' => ABSPATH.'wp-content/plugins/',
            'preserve_zip' => false
    );

    foreach($plugins as $plugin)
    {

echo '<br />';	    
$pb_plugin_check = ABSPATH.'wp-content/plugins/'. $plugin['install'];
    if (file_exists($pb_plugin_check))
{    	
	
echo 'plugin already installed';
echo '<br />';
echo $pb_plugin_check;
echo '<br />';


} else {

           mm_plugin_download($plugin['path'], $args['path'].$plugin['name'].'.zip');
           mm_plugin_unpack($args, $args['path'].$plugin['name'].'.zip');
           	
}


            mm_plugin_activate($plugin['install']);
    }
}
function mm_plugin_download($url, $path) 
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);

    if(file_put_contents($path, $data))
            return true;
    else
            return false;
}
function mm_plugin_unpack($args, $target)
{
    if($zip = zip_open($target))
    {
            while($entry = zip_read($zip))
            {
                    $is_file = substr(zip_entry_name($entry), -1) == '/' ? false : true;
                    $file_path = $args['path'].zip_entry_name($entry);
                    if($is_file)
                    {
                            if(zip_entry_open($zip,$entry,"r")) 
                            {
                                    $fstream = zip_entry_read($entry, zip_entry_filesize($entry));
                                    file_put_contents($file_path, $fstream );
                                    chmod($file_path, 0777);
                                    //echo "save: ".$file_path."<br />";
                            }
                            zip_entry_close($entry);
                    }
                    else
                    {
                            if(zip_entry_name($entry))
                            {
                                    mkdir($file_path);
                                    chmod($file_path, 0777);
                                    //echo "create: ".$file_path."<br />";
                            }
                    }
            }
            zip_close($zip);
    }
    if($args['preserve_zip'] === false)
    {
            unlink($target);
    }
}
function mm_plugin_activate($installer)
{
    $current = get_option('active_plugins');
    $plugin = plugin_basename(trim($installer));

    if(!in_array($plugin, $current))
    {
            $current[] = $plugin;
            sort($current);
            do_action('activate_plugin', trim($plugin));
            update_option('active_plugins', $current);
            do_action('activate_'.trim($plugin));
            do_action('activated_plugin', trim($plugin));
            return true;
    }
    else
            return false;
}




// Variables

/*
$pb_plugins_json = '{
  "bundle_info": {
    "bundle_name": "Starter Bundle",
    "bundle_description": "This is a test bundle"
  },
  "bundle_plugins": [
    {
      "name": "jetpack",
      "path": "http://downloads.wordpress.org/plugin/jetpack.1.3.zip",
	  "install": "jetpack/jetpack.php"
    },
    {
      "name": "cookies-for-comments",
      "path": "http://downloads.wordpress.org/plugin/cookies-for-comments.0.5.5.zip",
      "install": "cookies-for-comments/cookies-for-comments.php"
    },
    {
      "name": "tumblr-importer",
      "path": "http://downloads.wordpress.org/plugin/tumblr-importer.0.5.zip",
      "install": "tumblr-importer/tumblr-importer.php"
    }
  ]
}';
*/


function pb_plugins_json() {
	$pb_root = 'http://raison.co/bundles_json/';
	$pb_file = $_GET['bp_bundle_file'];	
	$pb_path = $pb_root.$pb_file.'.json';
		

	// http://alvinalexander.com/php/php-curl-examples-curl_setopt-json-rest-web-service
	
	$data = array("id" => "$id", "symbol" => "$symbol", "companyName" => "$companyName");
	$data_string = json_encode($data);
	
	$ch = curl_init($pb_path);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json',
	    'Content-Length: ' . strlen($data_string))
	);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	
	//execute post
	$pb_result = curl_exec($ch);
	
	//close connection
	curl_close($ch);
	
	$pb_result_decode = json_decode($pb_result, true);
	//var_dump($pb_result_decode);
	return $pb_result_decode;

}




echo $data;



// Page Logic

$bp_action = isset($_GET['action']) ? $_GET['action'] : '';


    if($bp_action == 'deactivate'){
        bp_deactivate();
    }
    
    elseif($bp_action == 'activate'){
		bp_activate();
    }




function bp_activate() {
	
	echo '<h2>Activate</h2>';
//	$pb_plugins_decode = json_decode($pb_plugins_json, true);
/*
	$pb_plugins_decode = json_decode(pb_plugins_json(), true);
	$pb_plugins = $pb_plugins_decode[bundle_plugins];
*/

//var_dump( pb_plugins_json());


$pbt = pb_plugins_json();
$pb_plugins = $pbt[bundle_plugins];
//var_dump($pb_plugins);

	if ($pb_plugins == null) {
		echo 'Nothing Found Yo';
		return;
	}
	
	else {
	
		mm_get_plugins($pbt[bundle_plugins]);
	}
}


function bp_deactivate() {
	echo '<h2>Deactivate</h2>';
}		
		
?>





<form>
	<input class="" type="text" name="bp_bundle_file" value="default" checked="checked">Text
	<input type="hidden" name="action" value="activate"><br>
	<input type="hidden" name="page" value="bundles">
	<input type="submit" value="Install" />

</form>