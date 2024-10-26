<?php
/*
 * @redmine #11003		: AFFILIATION | Amélioration design API Wordpress SR FORMS
 */
?>
<!-- WEBSITE-3658 : API wording fixes -->
<div class="wrap">
<h2><?php print __( 'Form Options', 'sm_translate' ); ?></h2>

<?php sm_show_messages($messages);?>

<form method="post" action="" class="sm_appearance">
	<input type="hidden" name="_nonce" value="<?php print $nounce;?>">

	<!--WEBSITE-3684 : API Split the tab "option forms"-->
	<?php if(current_user_can('sm_api_manage_forms')){ ?>

	<h3><?php _e( 'Module Settings', 'sm_translate' ); ?></h3>

	<?php if(current_user_can('sm_api_manage_options')){ ?>
	<?php if ($sm_accept_spa) {?>
		<div class="sm_form_item">
			<label for="sm_font_size"><?php _e("Choose your country", "sm_translate")?></label><br />
			<select class="sm_select" id="sm_sp_submit_to_country" name="sm_sp_submit_to_country">
				<option <?php if (sm_val_in_arrays('sm_sp_submit_to_country', $form_data_list, $sm_sp_submit_to_country) == $sm_api_server_country) print "selected";?> value="<?php print $sm_api_server_country;?>"><?php print $sm_api_server_country;?></option>
				<option <?php if (sm_val_in_arrays('sm_sp_submit_to_country', $form_data_list, $sm_sp_submit_to_country) == 'other') print "selected";?> value="other"><?php _e("other", "sm_translate");?></option>
			</select>
		</div>
	<?php } ?>

	<div class="sm_form_item">
		<label class="sm_inherit_pointer" for="parent"><?php _e("Temporarily disable API connections when slow", "sm_translate");?></label><br />
		<input
			id="sm_deactivate_api_during_slow"
			type="checkbox"
			class="sm_checkbox"
			name="sm_deactivate_api_during_slow"
			value="1" <?php if (sm_val_in_arrays('sm_deactivate_api_during_slow', $form_data_list, $sm_deactivate_api_during_slow)) print "checked=\"checked\"";?>>
		<label for="sm_deactivate_api_during_slow"><?php _e("Yes","sm_translate");?></label>
	</div>

	<div class="sm_form_item">
		<label class="sm_inherit_pointer" for="parent"><?php _e("Clear all traces of plugin on deactivation", "sm_translate");?></label><br />
		<input
			id="sm_clear_all_trace_on_deactivation"
			type="checkbox"
			class="sm_checkbox"
			name="sm_clear_all_trace_on_deactivation"
			value="1"
			<?php if (sm_val_in_arrays('sm_clear_all_trace_on_deactivation',  $form_data_list, $sm_clear_all_trace_on_deactivation)) print "checked=\"checked\"";?>>
			<label for="sm_clear_all_trace_on_deactivation"><?php _e("Yes","sm_translate");?></label>
		<div class="sm_hint">
			<?php
				_e('Check here if you wish all traces of the plugin to be removed when you deactivate the plugin.',"sm_translate");
			?>
		</div>
	</div>

	<div class="sm_form_item">
		<label class="sm_inherit_pointer" for="parent"><?php _e("Allow this plugin to set a user cookie", "sm_translate");?></label><br />
		<input
			id="sm_set_user_cookie"
			type="checkbox"
			class="sm_checkbox"
			name="sm_set_user_cookie"
			value="1"
			<?php if (sm_val_in_arrays('sm_set_user_cookie',  $form_data_list, $sm_set_user_cookie)) print "checked=\"checked\"";?>>
			<label for="sm_set_user_cookie"><?php _e("Yes","sm_translate");?></label>
		<div class="sm_hint">
			<?php
				_e('Checking this box allows this plugin to set a cookie to track users on your site.  This helps ServiceMagic make improvements.',"sm_translate");
			?>
		</div>
	</div>

	<div class="sm_form_item">
		<label class="sm_inherit_pointer"><?php _e("API cache mechanism", "sm_translate");?></label><br />
		<p>
			<input type="radio" class="sm_checkbox" name="sm_api_cache_mechanism" id="etag_cache" value="ETAG" <?php if (sm_val_in_arrays('sm_api_cache_mechanism',  $form_data_list, $sm_api_cache_mechanism) == "ETAG") print "checked=\"checked\"";?>> <b>ETAG :</b>
			<label for="etag_cache"><?php _e("The plugin will make a request to servicemagic.eu servers to see if the cached data is current.  This option while marginally slower ensures the data is always current.","sm_translate");?></label>
		</p>
		<p>
			<input type="radio" class="sm_checkbox" name="sm_api_cache_mechanism" id="tiemout_cache" value="Timeout" <?php if (sm_val_in_arrays('sm_api_cache_mechanism',  $form_data_list, $sm_api_cache_mechanism) == "Timeout") print "checked=\"checked\"";?>> <b>Trusted Cache :</b>
			<label for="tiemout_cache"><?php _e("The plugin will use cached data if it exists.  It is possible that the user might  stale data and therefore see a code issue while submitting an interview.","sm_translate");?></label>
		</p>
		<p>
			<input id="clear_api_cache" type="button" class="button action" value="<?php _e("Clear API Cache", "sm_translate")?>">
			<span id="clear_api_cache_status"></span>
		</p>
	</div>

	<?php } ?>

	<div class="sm_form_item">
		<label for="sm_default_success_more_text"><?php _e("Tracking pixel HTML", "sm_translate");?></label><br />
		<textarea id="sm_default_success_more_text" class="sm_textarea" name="sm_default_success_more_text"><?php print sm_val_in_arrays('sm_default_success_more_text', $form_data_list, $sm_default_success_more_text);?></textarea>
		<div class="sm_hint"><?php _e("Typically used with adwords pixels, this parameter allows you to append html to the success/thanks messaging on submission(javascript will not work here). Leave blank to not use this feature.","sm_translate")?></div>
	</div>

	<!-- Affiliation API WP - add settings generic pages -->

	<h3><?php _e( 'Generic pages', 'sm_translate' ); ?></h3>

	<div class="sm_form_item">
		<label for="sm_default_allact_title"><?php _e("Title Page 'All activities'", "sm_translate");?></label><br />
		<input id="sm_default_allact_title" type="text" size="50" class="sm_textbox sm_pagen_text" name="sm_default_allact_title" value="<?php print sm_val_in_arrays('sm_default_allact_title',  $form_data_list, $sm_default_allact_title);?>"  >
	</div>

	<div class="sm_form_item">
		<label for="sm_default_listcat_title"><?php _e("Title Page 'categories list for one activity'", "sm_translate");?></label><br />
		<input id="sm_default_listcat_title" type="text" size="50" class="sm_textbox sm_pagen_text" name="sm_default_listcat_title" value="<?php print sm_val_in_arrays('sm_default_listcat_title',  $form_data_list, $sm_default_listcat_title);?>"  >
	</div>

	<div class="sm_form_item">
		<label for="sm_default_genform_title"><?php _e("Title Page 'generic form'", "sm_translate");?></label><br />
		<input id="sm_default_genform_title" type="text" size="50" class="sm_textbox sm_pagen_text" name="sm_default_genform_title" value="<?php print sm_val_in_arrays('sm_default_genform_title',  $form_data_list, $sm_default_genform_title);?>"  >
	</div>

	<div class="sm_form_item">
		<?php if (count($sm_sr_generic_forms)>0 ) { ?>
		<label for="sm_default_form_gen"><?php _e("Choose the generic form", "sm_translate")?></label><br />
		<select class="sm_select sm_pagen_text"  name="sm_default_form_gen" id="sm_default_form_gen">
			<option value=""><?php _e("Choose", "sm_translate")?></option>
			<?php
				$default_gen_form = sm_val_in_arrays('sm_default_form_gen',  $form_data_list, $sm_default_form_gen);
				foreach ($sm_sr_generic_forms as $sm_sr_generic_form){
					$selected = ($sm_sr_generic_form['embedable_name']==$default_gen_form)?'selected':'';
					echo "<option value=\"{$sm_sr_generic_form['embedable_name']}\" $selected >{$sm_sr_generic_form['name']}</option>";
				}
			?>
		</select>
		<?php } else { ?>
		<span id="create_gen_pages_status"><?php _e('You must create a generic form for generate the generic pages.', 'sm_translate'); ?></span>
		<input id="sm_default_form_gen" type="text" class="sm_textbox" style="display:none" name="sm_default_form_gen" value=""  >
		<?php } ?>
	</div>

	<p>
		<input id="create_gen_pages" type="button" class="button action" value="<?php _e("Save Generic pages", "sm_translate")?>" >
		<span id="create_gen_pages_status"></span>
	</p>

	<!-- end - Affiliation API WP - add generic pages -->

	<h3><?php print __( 'Manage Form Appearance', 'sm_translate' ); ?></h3>
	<p><?php print __( 'Customize the form\'s appearance', 'sm_translate' ); ?></p>

	<div class="sm_form_item">
		<label for="sm_default_aff_str"><?php _e("Your tracking code", "sm_translate");?></label><br />
		<input id="sm_default_aff_str" type="text" class="sm_textbox" name="sm_default_aff_str" value="<?php print sm_val_in_arrays('sm_default_aff_str',  $form_data_list, $sm_default_aff_str);?>"  >
		<div class="sm_hint"><?php _e("This parameter allows you to track the results of default forms independently from other forms.","sm_translate")?></div>
	</div>

	<div class="sm_form_item">
		<label for="sm_sr_ty_message"><?php _e("Thank you message on Service Request form submission", "sm_translate");?></label><br />
		<textarea id="sm_sr_ty_message" class="sm_textarea" name="sm_sr_ty_message"><?php print htmlspecialchars(sm_val_in_arrays('sm_sr_ty_message',  $form_data_list, $sm_sr_ty_message));?></textarea>
	</div>

	<?php if (get_option("sm_accept_spa", 0)) : ?>
	<div class="sm_form_item">
		<label for="sm_sp_ty_message"><?php _e("Thank you message on Service Provider form submission", "sm_translate");?></label><br />
		<textarea id="sm_sp_ty_message" class="sm_textarea" name="sm_sp_ty_message"><?php print htmlspecialchars(sm_val_in_arrays('sm_sp_ty_message',  $form_data_list, $sm_sp_ty_message));?></textarea>
	</div>
	<?php endif;?>

	<div class="sm_form_item">
		<label for="sm_font_size"><?php _e("Font size", "sm_translate")?></label><br />
		<select class="sm_select" id="sm_font_size" name="sm_font_size">
			<option value=""><?php _e("Default", "sm_translate")?></option>
			<?php
				foreach(range(8, 20) as $size){
					print "<option value=\"$size\" ";
					if (sm_val_in_arrays('sm_font_size', array_merge( $form_data_list, $sm_display_defaults), "") == $size) print "selected";
					print ">$size</option>\n";
				}

			?>
		</select>
	</div>

	<div class="sm_form_item">
		<label for="sm_font_color"><?php _e("Font color", "sm_translate")?></label><br />
		<input type="text" class="sm_txt_wide" id="sm_font_color" name="sm_font_color" value="<?php print sm_val_in_arrays('sm_font_color', array($_POST, $sm_display_defaults), "");?>"  >
		<a href="" class="color_picker_reset" data_target="sm_font_color"><?php _e("Reset default", "sm_translate")?></a>
	</div>

	<div class="sm_form_item">
		<label for="sm_bg_color"><?php _e("Section background", "sm_translate")?></label><br />
		<input type="text" class="sm_txt_wide" id="sm_bg_color" name="sm_bg_color" value="<?php print sm_val_in_arrays('sm_bg_color', array($_POST, $sm_display_defaults), "");?>"  >
		<a href="" class="color_picker_reset" data_target="sm_bg_color"><?php _e("Reset default", "sm_translate")?></a>
	</div>

	<div class="sm_form_item">
		<label for="sm_design"><?php _e("Changing form design", "sm_translate")?></label><br />
		<select class="sm_select" id="sm_design" name="sm_design">
			<option value="1"
			<?php if ($sm_display_defaults['sm_design'] == 1) print "selected"; ?>
			><?php _e("Default", "sm_translate")?></option>
			<option value="2"
			<?php if ($sm_display_defaults['sm_design'] == 2) print "selected"; ?>
			><?php _e("Design 2", "sm_translate")?></option>
		</select>
	</div>
	<img id="view_design" style="float:center" src="<?php print plugins_url(($sm_display_defaults['sm_design'] == 2) ? 'form_design_2.jpg' : 'form_design_default.jpg', __FILE__);?>">

	<div class="sm_form_item"><br />
		<input type="submit" class="sm_submit button-primary" value="<?php _e("Save", "sm_translate")?>">
	</div>

	<?php } ?>

</form>

<script>
	var $j = jQuery.noConflict();
	$j(function(){
		$j("#sm_font_color, #sm_bg_color").miniColors({initColor:""});
		$j(".color_picker_reset").click(function(evt){
			var t = $j(this).attr("data_target");
			$j('input[name="'+t+'"]').miniColors("value","").blur();
			evt.preventDefault();
		});
		$j("input#clear_api_cache").click(function(){
			$j("#clear_api_cache_status").html("<?php _e("clearing...", "sm_translate");?>");
			$j.ajax({
			type: "POST",
			url: "admin-ajax.php",
			data: {"action":"sm_ajax_api_clear_cache"},
			success: function(data){
				$j("#clear_api_cache_status").html(data + " <?php _e("files cleared", "sm_translate");?>");
				setTimeout(function(){$j("#clear_api_cache_status").html("");}, 2000);
			}
			});
		});

		$j('#sm_design').on({
			'change': function() {
				if($j(this).val() == 2){
					$j('#view_design').attr('src', '<?php print plugins_url('form_design_2.jpg', __FILE__);?>');
				}else{
					$j('#view_design').attr('src', '<?php print plugins_url('form_design_default.jpg', __FILE__);?>');
				}
			}
		});

		if ($j('#sm_default_allact_title').val() && $j('#sm_default_listcat_title').val() &&
				$j('#sm_default_genform_title').val() && $j('#sm_default_form_gen').val() ) {
			$j('#create_gen_pages').prop("disabled", false );
		} else {
			$j('#create_gen_pages').prop("disabled", true );
		}

		$j(".sm_pagen_text").change(function() {
			if ($j('#sm_default_allact_title').val() && $j('#sm_default_listcat_title').val() &&
					$j('#sm_default_genform_title').val() && $j('#sm_default_form_gen').val() ) {
				$j('#create_gen_pages').prop("disabled", false );
			} else {
				$j('#create_gen_pages').prop("disabled", true );
			}
		});

		$j('#create_gen_pages').click(function(){
			$j("#create_gen_pages_status").html("<?php _e("saving...", "sm_translate");?>");
			$j.ajax({
			type: "POST",
			url: "admin-ajax.php",
			data: {	"action":"sm_ajax_create_genpage",
					"sm_default_allact_title":$j('#sm_default_allact_title').val(),
					"sm_default_listcat_title":$j('#sm_default_listcat_title').val(),
					"sm_default_genform_title":$j('#sm_default_genform_title').val(),
					"sm_default_form_gen":$j('#sm_default_form_gen').val()
			},
			success: function(data){
				$j("#create_gen_pages_status").html(" <?php _e("Generics pages saved", "sm_translate");?>");
				setTimeout(function(){$j("#create_gen_pages_status").html("");}, 2000);
			}
			});
		});

	});
</script>
</div>