<h2>Display Options</h2>
<input type="hidden" name="form_config">
<div class="sm_form_item">
	<label for="next_string" class="sm_label"><?php _e("Text on the \"Next\" button", "sm_translate")?></label>
	<input type="text" id="next_string" class="sm_text" name="next_string" value="<?php print htmlspecialchars(sm_val_in_arrays("next_string", $form_data_list, ""));?>">
	<div class="sm_hint"><?php _e("Defaults to \"Next\".", "sm_translate");?></div>
</div>

<?php /* ?>
<div class="sm_form_item">
	<label for="back_string" class="sm_label"><?php _e("Text on the \"Back\" button", "sm_translate")?></label>
	<input type="text" id="back_string" class="sm_text" name="back_string" value="<?php print sm_val_in_arrays("back_string", $form_data_list, "");?>">
	<div class="sm_hint"><?php _e("Defaults to \"Back\".", "sm_translate");?></div>
</div>
<?php */ ?>

<div class="sm_form_item">
	<label for="submit_string" class="sm_label"><?php _e("Text on the \"Submit\" button", "sm_translate")?></label>
	<input type="text" id="submit_string" class="sm_text" name="submit_string" value="<?php print  htmlspecialchars (sm_val_in_arrays("submit_string", $form_data_list, ""));?>">
	<div class="sm_hint"><?php _e("Defaults to \"Get quotes\".", "sm_translate");?></div>
</div>


<div id="sm_multiplude_builder"></div>

<script>
    jQuery(function(){
		jQuery("form.sm_form").on("activity_chosen.sm", function(evt, activity_id, config){
			jQuery("#sm_multiplude_builder").sm_mp_builder("destroy");
			jQuery.get("admin-ajax.php?action=sm_ajax_interview&activity_id=" + activity_id, function(data){
				var params = {"questions" : data, "form" : config};
				jQuery("#sm_multiplude_builder").sm_mp_builder("init", params);
			}, "json");			
		});
		
		<?php
			if (!empty($myform->activity_id)){
				$config = array();
				if (isset($myform->parameters['form_config'])) $config = $myform->parameters['form_config'];
				print 'jQuery("form.sm_form").trigger("activity_chosen.sm", [' . $myform->activity_id . ', ' . json_encode($config) . "]);";
			} 
		?>		
	});
</script>
