<h3><?php _e("ServiceMagic Form Selection", "sm_translate")?></h3>
<div class="sm_form_item">
	<div id="form_change_start" <?php if (! ($myform AND $myform->activity_id)){print "style=\"display:none\"";}?>>
		<span id="activity_label"><?php if ($myform AND $myform->activity_id) print __("The form is currently set as :", "sm_translate") . " \"$myform->activity_title\"";?> </span>
		<button type="button" id="alter_interview_btn"><?php _e("Change", "sm_translate")?></button>
	</div>

	<div id="form_selector" <?php if ($myform AND $myform->activity_id){print "style=\"display:none\"";}?>>
		<?php _e("First, choose a category", "sm_translate")?> :
		<select name="category" id="cat_start">
			<option value=""><?php _e("Choose", "sm_translate")?></option>
			<option value="999"><?php _e("All categories", "sm_translate"); ?></option>
		<?php
			foreach ($categories as $cat){
				echo "<option value=\"{$cat['id']}\">{$cat['label']}</option>";
			}
		?>
		</select>
		<div id="activity_chooser_wrap" style="display:none;">
			<?php _e("then pick an interview", "sm_translate")?> :
			<select name="int" id="activity_chooser">
				<option value=""><?php _e("Choose", "sm_translate")?></option>
			</select>
		</div>
		<div id="form_change_wait" style="display:none"><img src="<?php print plugins_url('ui/img/wait_spinner.gif', dirname(__FILE__)); ?>" style="position:relative;top:3px;"></div>
	</div>
	<input type="hidden" name="activity_id" id="activity_id" value="<?php print sm_val_in_arrays("activity_id", $form_data_list, "")?>">
	<input type="hidden" name="activity_title" id="activity_title" value="<?php print sm_val_in_arrays("activity_title", $form_data_list, "")?>">
</div>
	