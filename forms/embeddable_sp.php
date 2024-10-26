<h3><?php _e("ServiceMagic Trade Selection", "sm_translate")?></h3>
<div class="sm_form_item sm_worktypes">
	<p>
		<?php _e("Choose at least one trade.  Click no checkboxes to show all trades.", "sm_translate")?>
	</p>
	<?php
		foreach($interview_obj->get_worktypes() as $i => $worktype){?>
			<div class="worktype_list_item">
			<input
				type="checkbox" 
				name="worktypes[]" 
				<?php if (in_array($worktype['value'], sm_val_in_arrays("worktypes", $form_data_list, array()))) print "checked=\"checked\"";?> 
				id="worktype_id_<?php print $worktype['value'];?>" 
				value="<?php print $worktype['value'];?>">
			<label for="worktype_id_<?php print $worktype['value'];?>"><?php print $worktype['label'];?></label>
			</div>
			<?php 
			if ($i % 3 == 2){
				print "<br class=\"clear\">";
			}
		}
	?>
	<br class="clear">
</div>
	