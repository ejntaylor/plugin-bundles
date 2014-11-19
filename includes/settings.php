<?php
	
	

// Settings

global $bndlsPlugin; // we'll need this below


$default_plugins = $bndlsPlugin->get_setting('req_plugins_arr','multiarray');




?>


    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    	<?php $bndlsPlugin->the_nonce(); ?>
    	<table class="form-table">
		<tbody>


			<th colspan="2" ><h3>General</h3></th>


			<tr>
				<th scope="row" valign="top">Feed Settings</th>
				<td>
					<label>
						<input  <?php if ( $bndlsPlugin->get_setting('bndls_feed') == "default") echo 'checked="checked"'; ?> type="radio" name="<?php echo $bndlsPlugin->get_field_name('bndls_feed'); ?>" value="default" />Default Raison Feed
						
						<br />
						
						<input  <?php if ( $bndlsPlugin->get_setting('bndls_feed') == "custom") echo 'checked="checked"'; ?> type="radio" name="<?php echo $bndlsPlugin->get_field_name('bndls_feed'); ?>" value="custom" <?php if ( $bndlsPlugin->get_setting('disable_dash_welcome') == "yes") echo 'checked="checked"'; ?> />Custom Feed
						
						<br />
						
						<input type="text" name="<?php echo $bndlsPlugin->get_field_name('bndls_feed_custom_url'); ?>" value="<?php echo $bndlsPlugin->get_setting('bndls_feed_custom_url'); ?>" />
	
						
					</label>
				</td>
			</tr>
		
			


			<tr>
				<th scope="row" valign="top">Enable Plugin Images (slow)</th>
				<td>
					<label>
						<input type="hidden" name="<?php echo $bndlsPlugin->get_field_name('bndls_images'); ?>" value="no" />
						<input type="checkbox" name="<?php echo $bndlsPlugin->get_field_name('bndls_images'); ?>" value="yes" <?php if ( $bndlsPlugin->get_setting('bndls_images') == "yes") echo 'checked="checked"'; ?> />
					</label>
				</td>
			</tr>

		</tbody>
    	</table>
    	
    	<input class="button-primary" type="submit" value="Save Settings" />
    	
    </form>