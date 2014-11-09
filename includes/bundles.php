<?php


	
function mm_get_plugins($plugins) {
	
    $args = array(
            'path' => ABSPATH.'wp-content/plugins/',
            'preserve_zip' => false
    );

echo '<div class="updated">';

    foreach($plugins as $plugin)
    {
	    

echo '<br />';	    
$pb_plugin_check = ABSPATH.'wp-content/plugins/'. $plugin['plugin_install'];
    if (file_exists($pb_plugin_check))
{    	
	
echo $plugin['plugin_install'] . ' already installed';


} else {
			echo $plugin['plugin_install'] . ' installed';

           mm_plugin_download($plugin['plugin_path'], $args['plugin_path'].$plugin['plugin_name'].'.zip');
           mm_plugin_unpack($args, $args['plugin_path'].$plugin['plugin_name'].'.zip');
           	
}


            mm_plugin_activate($plugin['plugin_install']);
    }
    
    echo '</div>';
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
                    $file_path = $args['plugin_path'].zip_entry_name($entry);
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






function pb_plugins_json() {
	$pb_root = 'http://raison.co/bundles_json/';
	$pb_file = isset($_GET['bp_bundle_file']) ? $_GET['bp_bundle_file'] : 'bundles';
	$pb_path = $pb_root.$pb_file.'.json';
	//echo $pb_path;	

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


	$pbjson = pb_plugins_json();
	$pb_bundle_number = isset($_GET['bundle_number']) ? $_GET['bundle_number'] : '';
	if ($pb_bundle_number == '') {
		echo 'no bundle found';
		return;
		}
	echo 'Activating and Installing' . $pb_bundle_number;
	$pb_plugins = $pbjson[$pb_bundle_number][bundle_plugins];

	var_dump($pb_plugins);

						
			mm_get_plugins($pb_plugins);
		
	
}


function bp_deactivate() {
	echo '<h2>Deactivate</h2>';
}		
		
		
		
// Loop Logic

function bp_loop_logic() {
	
	$pbjson = pb_plugins_json();
	$pb_plugins = $pbjson;
	//var_dump($pb_plugins);

	if ($pb_plugins == null) {
		echo 'Nothing Found Yo';
		return;
	}
	
	else {
		
		
		foreach ($pb_plugins as $pb_plugin_item_number => $pb_plugin_item_details ) {

			bp_loop_item($pb_plugin_item_details, $pb_plugin_item_number);
						
			//$pb_bundle_go = $pbjson[bundle_plugins]
			//mm_get_plugins($pb_bundle_go);
		}
		
}

}		
		
		
		
// Loop Output

function bp_loop_item($pb_data, $pb_number) { 
	
//var_dump($pb_data); 

	
?>


<div id="bundle-section">
	<ul id="bundle-list">
		<li class="bundle-item">
			<div class="bundle-item-l">
				<div class="bundle-title-top">
					<h3><?php echo $pb_data[bundle_info][bundle_name]; ?></h3>
					<div class="plugin-info"><?php echo count($pb_data[bundle_plugins]); ?> Plugins by <span><a href="<?php echo $pb_data[bundle_info][bundle_author_link]; ?>"><?php echo $pb_data[bundle_info][bundle_author]; ?></a></span></div>
				</div>
				<div class="bundle-description"><?php echo $pb_data[bundle_info][bundle_description]; ?></div>
								
				<div class="activate">
					<form>
						<input type="hidden" name="bundle_number" value="<?php echo $pb_number; ?>">
						<input type="hidden" name="action" value="activate">
						<input type="hidden" name="page" value="bundles">
						<input type="submit" value="Download and install" />
					
					</form>
				</div>
				
			</div>
			<div class="bundle-item-r">
				ICONS
			</div>
		</li>
	</ul>
</div>


<?php }	
		
		
		
// page loaded

bp_loop_logic();

		
?>




<!--


// Use for Custom Fetch

<form>
	<input class="" type="text" name="bp_bundle_file" value="bundles" checked="checked">Text
	<input type="hidden" name="action" value="activate"><br>
	<input type="hidden" name="page" value="bundles">
	<input type="submit" value="Install" />

</form>
-->

