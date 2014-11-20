<?php



/**
 * bp_api_query function.
 *
 * Returns the API object from the plugin slug
 * ref: http://stackoverflow.com/questions/10353859/is-it-possible-to-programmatically-install-plugins-from-wordpress-theme
 *
 * @access public
 * @param mixed $slug
 * @return object
 */

function bp_api_query($slug) {

	/** Prepare our query */
	$call_api = bp_wpapi( 'plugin_information', array( 'slug' => $slug ) );

	/** Check for Errors & Display the results */
	if ( is_wp_error( $call_api ) ) {

		echo '<pre>' . print_r( $call_api->get_error_message(), true ) . '</pre>';

	} else {

		// echo '<pre>' . print_r( $call_api, true ) . '</pre>';

		if ( ! empty( $call_api->downloaded ) ) {

			$pb_plugin_name =  print_r( $call_api->name, true );
			$pb_plugin_download_link =  print_r( $call_api->download_link, true );
		}

	}



	return $call_api;


}




/**
 * mm_get_plugins function.
 *
 * downloads and activates a plugin.
 *
 * @access public
 * @param mixed $plugins
 * @return string the confirmation of whether plugin is activated
 */


function mm_get_plugins($plugins) {

	$args = array(
		'plugin_path' => ABSPATH.'wp-content/plugins/',
		'preserve_zip' => false
	);

	echo '<div class="updated">';


	foreach($plugins as $plugin)
	{


		// get the api object from supplied plugin slug
		$bp_api_query = bp_api_query($plugin[plugin_slug]);


		echo '<br />';
		$pb_plugin_check = ABSPATH.'wp-content/plugins/'. $plugin['plugin_slug'];
		if (file_exists($pb_plugin_check)) {

			echo 'Already Installed. Just Activated: ' . print_r( $bp_api_query->name, true );

		} else {

			$pb_plugin_download_link =  print_r( $bp_api_query->download_link, true );
			echo 'Installed and Activated: ' . print_r( $bp_api_query->name, true );
			$pb_plugin_download_link = preg_replace("/^https:/i", "http:", $pb_plugin_download_link);
			mm_plugin_download($pb_plugin_download_link, $args['plugin_path'].$plugin['plugin_slug'].'.zip');
			mm_plugin_unpack($args, $args['plugin_path'].$plugin['plugin_slug'].'.zip');

		}


		mm_plugin_activate($plugin['plugin_slug']);
	}

	echo '</div>';
}




/**
 * mm_plugin_download function.
 *
 * downloads the plugin from the WP API
 *
 * @access public
 * @param mixed $url
 * @param mixed $path
 * @return void
 */

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
	$pb_plugin_info = get_plugins('/' . $installer);
	reset($pb_plugin_info);
	$pb_plugin_file = key($pb_plugin_info);

	$pb_full = $installer . '/'. $pb_plugin_file;
	$plugin = trim($pb_full);


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






/**
 * fetches the json from the raison website
 *
 * @access public
 * @return object json as object
 */


function pb_plugins_json() {
	$timeStart = microtime(true);


	$pb_root = 'http://raison.co/bundles_json/';
	$timeAfterGet = microtime(true);

	$pb_file = isset($_GET['bp_bundle_file']) ? $_GET['bp_bundle_file'] : 'bundles';
	$pb_path = $pb_root.$pb_file.'.json';
	//echo $pb_path;

	// ref: http://alvinalexander.com/php/php-curl-examples-curl_setopt-json-rest-web-service

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


	$timeEnd = microtime(true);

	/*
	 echo "Time taken to get JSON: " . number_format($timeAfterGet - $timeStart, 4) . " seconds<br />";
	 echo "Time taken to go through JSON: " . number_format($timeEnd - $timeAfterGet, 4) . " seconds<br />";
*/


	return $pb_result_decode;

}




/**
 * bp_loop_logic function.
 *
 * the logic to run through the plugin loop
 *
 * @access public
 * @return void
 */


function bp_loop_logic() {


	$pb_json = pb_plugins_json();
	//var_dump($pb_plugins);

	if ($pb_json == null) {
		echo 'Nothing Found Yo';
		return;
	}

	else {


		foreach ($pb_json as $pb_plugin_item_number => $pb_plugin_item_details ) {

			bp_loop_item($pb_plugin_item_details, $pb_plugin_item_number);

			//$pb_bundle_go = $pbjson[bundle_plugins]
			//mm_get_plugins($pb_bundle_go);
		}

	}

}




/**
 * bp_wpapi function.
 *
 * Access information about plugin from the API
 *
 * @access public
 * @param mixed $action
 * @param mixed $args (default: null)
 * @return string
 */


function bp_wpapi($action, $args = null) {

	if ( is_array($args) )
		$args = (object)$args;

	if ( !isset($args->per_page) )
		$args->per_page = 24;

	// Allows a plugin to override the WordPress.org API entirely.
	// Use the filter 'plugins_api_result' to merely add results.
	// Please ensure that a object is returned from the following filters.
	$args = apply_filters('plugins_api_args', $args, $action);
	$res = apply_filters('plugins_api', false, $action, $args);

	if ( false === $res ) {
		$url = 'http://api.wordpress.org/plugins/info/1.0/';
		if ( wp_http_supports( array( 'ssl' ) ) )
			$url = set_url_scheme( $url, 'https' );

		$request = wp_remote_post( $url, array(
				'timeout' => 15,
				'body' => array(
					'action' => $action,
					'request' => serialize( $args )
				)
			) );

		if ( is_wp_error($request) ) {
			$res = new WP_Error('plugins_api_failed', __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="http://wordpress.org/support/">support forums</a>.' ), $request->get_error_message() );
		} else {
			$res = maybe_unserialize( wp_remote_retrieve_body( $request ) );
			if ( ! is_object( $res ) && ! is_array( $res ) )
				$res = new WP_Error('plugins_api_failed', __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="http://wordpress.org/support/">support forums</a>.' ), wp_remote_retrieve_body( $request ) );
		}
	} elseif ( !is_wp_error($res) ) {
		$res->external = true;
	}

	return apply_filters('plugins_api_result', $res, $action, $args);
}




/**
 * bndls_get_images function.
 *
 * Gets the logo images from the WordPress server
 *
 * @access public
 * @param mixed $pb_plugin_item
 * @return string
 */
function bndls_get_images($pb_plugin_item) {

	$logo = '128x128.png';
	$url = 'http://ps.w.org/' . $pb_plugin_item[plugin_slug] . '/assets/icon-' . $logo;
	$exists = (@$fp = fopen($url, "r")) !== FALSE; if ($fp) fclose($fp);
	if ($exists==false) { $logo = 'no';  }
	$image = '';

	// provided logo url
	$bgImage = '';
	if(!empty($image)){
		$bgImage = 'style="background-image: url(' . $image . ');"';
	} else {
		if($logo == '128x128.jpg' || $logo == '128x128.png' || $logo == '256x256.jpg' || $logo == '256x256.png'){
			$bgImage = 'style="background-image:  url(http://ps.w.org/' . $pb_plugin_item[plugin_slug] . '/assets/icon-' . $logo . ');"';
		} else if($logo == 'svg'){
				$bgImage = 'style="background-image:  url(http://ps.w.org/' . $pb_plugin_item[plugin_slug] . '/assets/icon.svg)"';
			} else if($logo == 'no'){
				$bgImage = 'style="background-image:  url(' . plugins_url('/assets/img/wp-pic-sprite.png', dirname(__FILE__) ) . ');"';
			} else {
			$bgImage = 'style="background-image:  none, url(http://ps.w.org/' . $pb_plugin_item[plugin_slug] . '/assets/icon.svg), url(' . plugins_url('/assets/img/wp-pic-sprite.png', dirname(__FILE__) ) . ');"';
		}

	}

	return $bgImage;
}



/**
 * PB Loop Output function.
 *
 * Loops through the bundles and the plugins on the backend
 *
 * @access public
 * @param mixed $pb_data
 * @param mixed $pb_number
 * @return void
 */

function bp_loop_item($pb_data, $pb_number) {


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
						<input type="hidden" name="bundle_name" value="<?php echo $pb_data[bundle_info][bundle_name]; ?>">
						<input type="hidden" name="bundle_number" value="<?php echo $pb_number; ?>">
						<input type="hidden" name="action" value="activate">
						<input type="hidden" name="page" value="bundles">
						<input type="submit" value="Download and install" />

					</form>
				</div>

			</div>
			<div class="bundle-item-r">


			<?php

	$pb_plugin_items = $pb_data[bundle_plugins];
	foreach ($pb_plugin_items as $pb_plugin_item) {

		// check if images are enabled in settings and use function if true
		global $bndlsPlugin;
		$bndls_images = $bndlsPlugin->get_setting('bndls_images');
		if ($bndls_images == "yes" ) {
			$bgImage = bndls_get_images($pb_plugin_item);
		} else {
			$bgImage = 'style="background-image:  url(' . plugins_url('/assets/img/wp-pic-sprite.png', dirname(__FILE__) ) . ');"';

		}

		// output the plugin list item

		//var_dump($pb_plugin_item );
		$bp_api_name = bp_api_query($pb_plugin_item[plugin_slug]);

		echo '<div class="bundle-plugin-item"><a href="'.$bp_api_name->homepage .'" target="blank"><div '. $bgImage . ' class="bundle-icon"></div><div class="bundle-item-right"><div class="bundle-name">' . $bp_api_name->name .'</div><div class="bundle-rating">Rating: ' . $bp_api_name->rating  . '</div></div></a></div>';



	} // close foreach plugin



?>
			</div>
		</li>
	</ul>
</div>


<?php }






// Page Logic

$bp_action = isset($_GET['action']) ? $_GET['action'] : '';


if($bp_action == 'deactivate'){
	bp_deactivate();
}

elseif($bp_action == 'activate'){
	bp_activate();
}




function bp_activate() {

	echo '<h3>Activating and Installing: ' .  $_GET['bundle_name'] . '</h3>';



	$pbjson = pb_plugins_json();
	$pb_bundle_number = isset($_GET['bundle_number']) ? $_GET['bundle_number'] : '';
	if ($pb_bundle_number == '') {
		echo 'no bundle found';
		return;
	}
	$pb_plugins = $pbjson[$pb_bundle_number][bundle_plugins];

	//  var_dump($pb_plugins);


	mm_get_plugins($pb_plugins);


}


function bp_deactivate() {
	echo '<h2>Deactivate</h2>';
}






// page loaded

bp_loop_logic();










?>