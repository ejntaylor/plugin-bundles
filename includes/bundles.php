<?php


		$plugin_array1 = Array(
			0 => array(
				'name' => 'WooCommerce',
				'slug' => 'woocommerce',
				'required' => true,
			) ,
			1 => array(
				'name' => 'Cookies for Comments',
				'slug' => 'cookies-for-comments',
				'required' => false,
			),
			2 => array(
				'name' => 'Wordpress SEO',
				'slug' => 'wordpress-seo',
				'required' => false,
			) ,
			3 => array(
				'name' => 'Usersnap',
				'slug' => 'usersnap',
				'required' => false,
			) ,
			4 => array(
				'name' => 'Video User Manuals',
				'slug' => 'video-user-manuals',
				'required' => false,
			) ,
			5 => array(
				'name' => 'Woo Dojo',
				'slug' => 'woodojo',
				'required' => true,
			) ,
			6 => array(
				'name' => 'WooThemes Updater',
				'slug' => 'woothemes-updater',
				'required' => true,
			) ,
			7 => array(
				'name' => 'Advanced Custom Fields',
				'slug' => 'advanced-custom-fields',
				'required' => true,
			) ,
			8 => array(
				'name' => 'BWP Minify',
				'slug' => 'bwp-minify',
				'required' => false,
			) ,
);






?>

<script type="text/javascript">

// http://jesin.tk/dynamic-textbox-jquery-php/

jQuery(document).ready(function($){
    $('.my-form .add-box').click(function(){
        var n = $('.text-box').length + 1;
        if( 20 < n ) {
            alert('Limited to 20 Plugins - Come on you don\'t need that many!');
            return false;
        }
        var box_html = $('<p class="text-box">\n\
			<label for="box' + n + '">Plugin <span class="box-number">' + n + '</span></label>\n\
			<input type="text" name="bamboo_setting[req_plugins_arr][multiarray][' + n + '][name]" value="" id="box' + n + '" />\n\
			<label for="box1">Slug </label>\n\
			<input type="text" name="bamboo_setting[req_plugins_arr][multiarray][' + n + '][slug]" value="" id="box' + n + '" />\n\
			<label for="box1">Required </label>\n\
			<input type="hidden" name="bamboo_setting[req_plugins_arr][multiarray][' + n + '][required]" value="0" />\n\
			<input type="checkbox" name="bamboo_setting[req_plugins_arr][multiarray][' + n + '][required]" value="1" id="box' + n + '" />\n\
			<a href="#" class="remove-box">Remove</a></p>');
        box_html.hide();
        $('.my-form p.text-box:last').after(box_html);
        box_html.fadeIn('slow');
        return false;
    });
    $('.my-form').on('click', '.remove-box', function(){
        $(this).parent().css( 'background-color', '#FF6C6C' );
        $(this).parent().fadeOut("slow", function() {
            $(this).remove();
            $('.box-number').each(function(index){
                $(this).text( index + 1 );
            });
        });
        return false;
    });

    $('.my-form .domain-add-box').click(function(){
        var n = $('.domain-text-box').length + 1;
        if( 20 < n ) {
            alert('Limited to 20 Plugins - Come on you don\'t need that many!');
            return false;
        }

        var box_html = $('<p class="domain-text-box">\n\
			<label for="box1">Domain Name <span class="domain-box-number">' + n + '</span></label>\n\
			<input type="text" name="bamboo_setting[allowed_domains_arr][multiarray][]" value="" id="domain-box' + n + '" />\n\
			<a href="#" class="remove-box">Remove</a></p>');
        box_html.hide();
        $('.my-form p.domain-text-box:last').after(box_html);
        box_html.fadeIn('slow');
        return false;
    });


});
</script>

    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

    	<table class="form-table">
		<tbody>

			<th colspan="2" ><h3>Plugin Installer</h3></th>


			<tr class="my-form">
				<th scope="row" valign="top">Required Plugins List</th>
				<td>

<?php

// ******** MULTIPLE ITEMS SECTION *********


if (!empty($plugin_array1)) {
	$i = 0;
	foreach ($plugin_array1 as $key => $values) :
		?>

		<p class="text-box">
			<label for="box<?php echo $key+1; ?>">Plugin <span class="box-number"><?php echo $key+1; ?></span>
			<input type="text" name="<?php echo $plugin_array1; ?>[<?php echo $i; ?>][name]" value="<?php echo $values['name']; ?>" id="box<?php echo $key+1; ?>" />
			</label>

			<label for="box1">Slug</label>
			<input type="text" name="<?php echo $plugin_array1; ?>[<?php echo $i; ?>][slug]" value="<?php echo $values['slug']; ?>" id="box<?php echo $key+1; ?>" />
			<label for="box1">Required</label>
			<input type="hidden" name="<?php echo $plugin_array1; ?>[<?php echo $i; ?>][required]" value="0" />
			<input type="checkbox" name="<?php echo $plugin_array1; ?>[<?php echo $i; ?>][required]" value="1" <?php if ( isset($values['required']) && '1' == $values['required'] ) echo 'checked="checked"'; ?> id="box<?php echo $key+1; ?>" />
			<?php echo ( 0 == $key ? '' : '<a href="#" class="remove-box">Remove</a>' ); ?>
		</p>
		<?php
		 $i++;
	endforeach;
	echo '<p><a href="#" class="add-box">Add More</a></p>';
} else {

	global $bundlesPlugin;

    ?>
        <p class="text-box">
            <label for="box1">Name <span class="box-number">1</span></label>
            <input type="text" name="<?php echo $plugin_array1; ?>[0][name]" value="" id="box1" />
            <label for="box1">Slug</label>
            <input type="text" name="<?php echo $plugin_array1; ?>[0][slug]" value="" id="box1" />
            <label for="box1">Required</label>
	    <input type="hidden" name="<?php echo $plugin_array1; ?>[0][required]" value="0" />
            <input type="checkbox" name="<?php echo $plugin_array1; ?>[0][required]" value="1" id="box1" />
        </p>
	<p><a href="#" class="add-box">Add More</a></p>
<?php
    }



?>
			</td>
		</tr>
		
		
<?php bb_settings_end(); ?>		
		
		
		
		
		
		
		
		
		
		
		
	</tbody>
</table>
<input class="button-primary" type="submit" value="Save Settings" />
</form>
<p><a href="<?php echo admin_url( '/admin.php?page=bamboo-settings&reset' ); ?>">RESET</a></p>