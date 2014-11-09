<?php
	
	// Add Menu Items
function bndls_options_panel(){
  
  // add sub-menus
  add_submenu_page( 'plugins.php', 'Bundles', 'Bundles', 'manage_options', 'bundles', 'bndls_bundles');
  add_submenu_page( 'options-general.php', 'Bundles Settings', 'Bundles Settings', 'manage_options', 'bundles-settings', 'bndls_settings');


}

add_action('admin_menu', 'bndls_options_panel');



function bndls_bundles(){
                echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
                <h2>Plugin Bundles</h2>';
				require_once('bundles.php');
				echo '</div>';
}


function bndls_settings(){
                echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
                <h2>Plugin Bundle Settings</h2></div>';
				require_once('settings.php');

}



?>