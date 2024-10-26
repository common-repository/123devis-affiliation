<!-- WEBSITE-3658 : API wording fixes -->
<div class="wrap">
<?php
	if (empty($myform)) {
		echo "<h2>" . __( 'New embeddable form', 'sm_translate' ) . "</h2>";
	} else {
		echo "<h2>" . __( 'Editing Embeddable form', 'sm_translate' ) . ' "' . $myform->name . "\"</h2>";
	}
?>

<?php sm_show_messages($messages);?>

<p><?php _e( 'Configure the form', 'sm_translate' );?></p>

<form method="post" action="<?php print $save_action_target;?>" class="sm_form">
	<input type="hidden" name="_nonce" value="<?php print wp_create_nonce( 'embeddable_form' );?>">
	<input type="hidden" name="id" value="<?php print $id;?>">
	<input type="hidden" name="view" value="<?php print sm_val_in_arrays("view", $form_data_list, "2page")?>">

	<h3><?php _e("Form Info", "sm_translate")?></h3>
	<div class="sm_form_item">
		<label for="sm_name"><?php _e("Form name", "sm_translate")?></label><br />
		<input id="sm_name" type="text" class="sm_txt" name="name" value="<?php print sm_val_in_arrays("name", $form_data_list, "");?>">
	</div>
	<?php if (!empty($myform->id)){ ?>
	<div class="sm_form_item" id="embeddable_name_form">
		<label for="sm_embedable_name"><?php _e("Form identifyer", "sm_translate")?></label><br />
		<input disabled="disabled" type="text" class="sm_txt_wider" name="embedable_name" id="sm_embedable_name" value="<?php print sm_val_in_arrays("embedable_name", $form_data_list, "");?>">
		<button type="button" id="sm_change_slug"><?php _e("Make Editable","sm_translate")?></button>
		<div class="sm_hidden_warning"><?php _e("This field is used as the name in shortcodes that identify this form in pages and posts. If you change it here, please update all of your shortcodes accordingly.","sm_translate")?></div>
		<div class="sm_hint"><?php _e("This is the identifyer for this form used in shortcodes.  Typically, do not change.","sm_translate")?></div>
	</div>
	<script>
		jQuery(function(){
			jQuery("#sm_change_slug").click(function(){
				jQuery("#sm_embedable_name").removeAttr("disabled");
				jQuery("#embeddable_name_form .sm_hidden_warning").show();
				jQuery("#embeddable_name_form .sm_hint").hide();
				jQuery("#embeddable_name_form").addClass("sm_warning");
				jQuery(this).hide();
			});
		});
	</script>
	<?php } ?>

	<div class="sm_form_item">
		<label for="sm_tracking_label"><?php _e("Your tracking code", "sm_translate");?></label><br />
		<input id="sm_tracking_label" type="text" class="sm_txt" name="tracking_label" value="<?php print sm_val_in_arrays("tracking_label", $form_data_list, "");?>">
		<div class="sm_hint"><?php _e("This parameter allows you to track the results of this form independently from other forms.","sm_translate")?></div>
	</div>

	<div class="sm_form_item">
		<label for="sm_success_more_text">
			<?php
				_e("Tracking pixel HTML", "sm_translate");
				print " (";
				if (empty($sm_default_success_more_text)){
					_e("Default is blank", "sm_translate");
				} else {
					_e("Default is set", "sm_translate");
				}
				print " <a href=\"admin.php?page=sm_admin_form_defaults\">" . __("edit the default", "sm_translate") . "</a>)";
			?>
		</label><br />
		<textarea id="sm_success_more_text" class="sm_textarea" name="success_more_text" ><?php print sm_val_in_arrays("success_more_text", $form_data_list, "");?></textarea>
		<div class="sm_hint"><?php _e("Typically used with adwords, this parameter allows you to append javascript or html to the success/thanks messaging on submission.","sm_translate")?></div>
	</div>
	<?php
		include("embeddable_$view_type.php");

		$view = sm_val_in_arrays("view",$form_data_list, "none");
		$this_file = dirname(__FILE__) . "/embed_" . $view_type . "_" . $view . "/more_form.php";

		if (file_exists($this_file)){
			include $this_file;
		}
	?>

	<div class="sm_form_item">
		<input type="submit" class="sm_submit button-primary" value="<?php _e("Save Form", "sm_translate")?>">
	</div>
</form>

<?php if ($view_type == "sr"){?>
<script>
	var $j = jQuery.noConflict();
	$j(function(){
		$j("#cat_start").change(function(){
			var $this = $j(this);
			if ($this.val() != '' && $this.val() != '999'){
				$j("#form_change_wait").css("display", "inline");
				$j("#activity_chooser_wrap").hide();
				$j.get("admin-ajax.php", {"action":"sm_ajax_activities", "category_id":$this.val()}, function(data){
					$j("#activity_chooser_wrap").css("display","inline");
					$j("#form_change_wait").css("display", "none");
					var $activity = $j("#activity_chooser");
					$activity.empty();
					$activity.append($j("<option value=\"\"><?php _e("Choose again", "sm_translate")?></option>"));
					$j.each(data, function(i, dataItem){
						$activity.append($j("<option value=\"" + dataItem['id'] + "\">" + dataItem['label'] + " (" + dataItem['id'] + ")</option>"));
					});
					$j("#activity_chooser_wrap").show();
				}, "json");
			}
			if ( $this.val() == '999') {
				$j("#activity_chooser_wrap").hide();
				$j("#activity_id").val(999);
				$j("#activity_title").val('Generic form');
			}
		});
		$j("#activity_chooser").change(function(){
			var $interview_option = $j(this).find("option:selected");
			var $category_option = $j("#cat_start").find("option:selected");
			var savestr = $category_option.html() + ' / ' + $interview_option.html();
			if ($interview_option.attr("value") != ''){
				$j("#activity_chooser_wrap").hide();
				$j("#activity_id").val($interview_option.val());
				$j("#activity_title").val(savestr);
				$j("#form_change_start").show();
				$j("#activity_label").html("<?php _e("The form is currently set as :", "sm_translate")?> " + savestr);
				$j("#activity_chooser").empty().append($j("<option value=\"\"><--<?php _e("Choose", "sm_translate")?></option>"));
				$j("#cat_start").val('');
				$j("#form_selector").hide();
				$j("form.sm_form").trigger("activity_chosen.sm", [$interview_option.val(), {}]);
			}
		});
		$j("#alter_interview_btn").click(function(){
			$j("#form_change_start").hide();
			$j("#form_selector").show();
		});
	});
</script>
<?php } ?>
</div>