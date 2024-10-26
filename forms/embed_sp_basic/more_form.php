<!-- WEBSITE-3658 : API wording fixes -->
<h3><?php _e("Display Options", "sm_translate")?></h3>

<div class="sm_form_item">
	<label><?php _e("If multiple categories are checked above, how would you like them presented to the user?", "sm_translate")?></label>
	<br>
	<label for="multiple_display_form_type_radio" class="sm_radio_label">
		<input type="radio" id="multiple_display_form_type_radio" value="radio" class="sm_radio" name="multiple_display_form_type" <?php if (sm_val_in_arrays("multiple_display_form_type", $form_data_list, "radio") === "radio") print "checked";?>>
		<?php _e("Radio button options", "sm_translate")?>
	</label>
	<br>
	<label for="multiple_display_form_type_select" class="sm_radio_label">
		<input type="radio" id="multiple_display_form_type_select" value="select" class="sm_radio" name="multiple_display_form_type" <?php if (sm_val_in_arrays("multiple_display_form_type", $form_data_list, "") === "select") print "checked";?>>
		<?php _e("Select dropdown", "sm_translate")?>
	</label>
</div>

<div class="sm_form_item">
	<label for="text_above_questions" class="sm_label"><?php _e("Add text above the form", "sm_translate")?></label><br />
	<textarea id="text_above_questions" class="sm_textarea" name="text_above_questions"><?php print sm_val_in_arrays("text_above_questions", $form_data_list, "");?></textarea>
</div>

<div class="sm_form_item">
	<label for="submit_string" class="sm_label"><?php _e("Text on the \"Submit\" button", "sm_translate")?></label><br />
	<input type="text" id="submit_string" class="sm_text" name="submit_string" value="<?php print  htmlspecialchars (sm_val_in_arrays("submit_string", $form_data_list, ""));?>">
	<div class="sm_hint"><?php _e("Defaults to \"Get quotes\".", "sm_translate");?></div>
</div>