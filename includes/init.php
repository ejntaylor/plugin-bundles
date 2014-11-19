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
	require_once($current.'.php');
	return;


    
}


function bndls_page_output(){

	$bndls_plugin_tabs = array(
	    'bundles_overview' => 'Bundles',
	    'settings' => 'Settings',
	);

	$bndls_plugin_page = 'bundles';
	
	echo admin_tabs($bndls_plugin_page, $bndls_plugin_tabs);
}









?>