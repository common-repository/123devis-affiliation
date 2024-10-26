<!-- WEBSITE-3658 : API wording fixes -->
<h3><?php _e("Display Options", "sm_translate")?></h3>
<div class="sm_form_item">
	<label for="show_required_1" class="sm_radio_label">
		<input type="radio" id="show_required_1" value="1" class="sm_radio" name="only_required_fields" <?php if (sm_val_in_arrays("only_required_fields", $form_data_list, "") === "1") print "checked";?>>
		<?php _e("Show only required fields", "sm_translate")?>
	</label>
	<label for="show_required_0" class="sm_radio_label"><br />
		<input type="radio" id="show_required_0" value="0" class="sm_radio" name="only_required_fields" <?php if (sm_val_in_arrays("only_required_fields", $form_data_list, "") === "0") print "checked";?>>
		<?php _e("Show all fields", "sm_translate")?>
	</label>
</div>

<div class="sm_form_item">
	<label for="text_above_questions" class="sm_label"><?php _e("Add text above the form", "sm_translate")?></label><br />
	<textarea id="text_above_questions" class="sm_textarea" name="text_above_questions"><?php print sm_val_in_arrays("text_above_questions", $form_data_list, "");?></textarea>
	<div class="sm_hint"><?php _e("Replaceable strings : [id], [title]", "sm_translate")?></div>
</div>

<div class="sm_form_item">
	<label for="submit_string" class="sm_label"><?php _e("Text on the \"Submit\" button", "sm_translate")?></label><br />
	<input type="text" id="submit_string" class="sm_text" name="submit_string" value="<?php print  htmlspecialchars (sm_val_in_arrays("submit_string", $form_data_list, ""));?>">
	<div class="sm_hint"><?php _e("Defaults to \"Get quotes\".", "sm_translate");?></div>
</div>

<div <?php if (!(array_key_exists('dev', $_REQUEST) || array_key_exists('SM_IS_OUR_IP', $_SERVER))){print 'style="display:none"';}?>>
	<h3><?php _e("Form field defaults", "sm_translate")?></h3>

	<?php if (isset($interview_obj)){
		print "<p>" . __("If you provide a default, that form will not be shown to the consumer and the API will submit that value.", "sm_translate") . "</p>";
		print '<div class="sm_form_item">';
		$questions = $interview_obj->get_questions();

		$defaults = sm_val_in_arrays("defaults", $form_data_list, array());
		//$defaults_activated = sm_val_in_arrays("default_activated", $form_data_list, array());
		foreach ($questions as $question){
			if (empty($question['label'])) continue;
			$is_this_dflt_set = (isset($defaults[$question['name']]) ? $defaults[$question['name']] : "");
			$this_dflt = (isset($defaults[$question['name']]) ? $defaults[$question['name']] : "");

			print "<div class=\"sm_q\">";
			print "<input type=\"checkbox\" class=\"default_val\" name=\"default_activated[]\" value=\"{$question['name']}\"";

			if ($is_this_dflt_set) print "checked=\"1\"";

			print "> ";
			print $question['label'];
			//print "<div class=\"sm_q_default\">";

			switch($question['type']){
				case "text":
				case "textarea":
					print " <input type=\"text\" class=\"dflt_form\" name=\"default[{$question['name']}]\" value=\"$this_dflt\">";
				break;
				case "radio":
				case "checkbox":
				case "select":
					print " <select class=\"dflt_form\"name=\"default[{$question['name']}]\">";
					print "<option value=\"\">" . __("Choose", "sm_translate") . "</option>";
					foreach($question['options'] as $option){
						print "<option value=\"{$option['value']}\"";
						if ($this_dflt == $option['value']) print " selected=\"selected\"";
						print ">";
						print $option['label'];
						print "</option>";
					}
					print "</select>";
				break;
			}
			//print "</div>";
			print "</div>";
		}
		?>
		</div>
	<?php } else { ?>
		<p><?php _e("Save and re-edit to set defaults.", "sm_translate")?></p>
	<?php }?>
</div>
<script>
	jQuery(function($){
		//this configures defaults based on loaded dom info
		$("input.default_val").each(function(){
			var $this = $(this);
			if (!$this.attr("checked")){
				//$(this).parent().find(".dflt_form").hide();
			//} else {
				$(this).parent().find(".dflt_form").hide();

			}
			$this.click(function(){
				console.log($this.attr("checked"));
				if ($this.attr("checked")){
					$(this).parent().find(".dflt_form").show();
				} else {
					$(this).parent().find(".dflt_form").val("").hide();
				}
			})
		});
	});
</script>