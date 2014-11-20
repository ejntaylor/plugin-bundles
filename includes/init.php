<?php
	
	// Add Menu Items
function bndls_options_panel(){
  
  // add sub-menus
  add_submenu_page( 'plugins.php', 'Bundles', 'Bundles', 'manage_options', 'bundles', 'bndls_page_output');
  //add_submenu_page( 'options-general.php', 'Bundles Settings', 'Bundles Settings', 'manage_options', 'bundles-settings', 'bndls_settings');


}

add_action('admin_menu', 'bndls_options_panel');






// Create WP Admin Tabs on-the-fly.
function admin_tabs($page, $tabs, $current=NULL){
    if(is_null($current)){
        if(isset($_GET['tab'])){
            $current = $_GET['tab'];
        }
        else {
	        $current = 'bundles_overview';
        }
    }
    $content = '';
    $content .= '<h2 class="nav-tab-wrapper">';
    foreach($tabs as $tab => $tabname){
        if($current == $tab){
            $class = ' nav-tab-active';

        } else{
            $class = '';    
        }
        $content .= '<a class="nav-tab'.$class.'" href="?page='. $page . '&tab='.$tab.'">'.$tabname.'</a>';
    }
    $content .= '</h2>';
        
    echo $content;
    if (!$current) $current = key($tabs);
	require_once($current.'.php');
	return;


    
}


function bndls_page_output(){

	$bndls_plugin_tabs = array(
	    'bundles_overview' => 'Bundles (beta)',
	    'settings' => 'Settings',
	);

	$bndls_plugin_page = 'bundles';
	
	echo admin_tabs($bndls_plugin_page, $bndls_plugin_tabs);
}



/*
  * Simple Settings
  * https://github.com/clifgriffin/wordpress-simple-settings/
*/




// Include the framework only if another plugin has not already done so
if ( ! class_exists('WordPress_SimpleSettings') )
	require('classes/wordpress-simple-settings.php'); 

class bndlsPlugin extends WordPress_SimpleSettings {
	var $prefix = 'bndls'; // this is super recommended

	function __construct() {
		parent::__construct(); // this is required


		register_activation_hook(__FILE__, array($this, 'activate') );
	}

	function activate() {
		$this->add_setting('bndls_feed', 'default');
		$this->add_setting('bndls_feed_custom_url', 'http://');
		$this->add_setting('bndls_images', 'no');
		
	}
}

$bndlsPlugin = new bndlsPlugin();

?>