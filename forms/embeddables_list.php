<div class="wrap">
<h2><?php _e('Embeddable Forms', 'sm_translate' );?></h2>
<p>
	<?php _e('Here you can create and edit ServiceMagic forms with available parameters and then easily embed them into posts via shortcodes', 'sm_translate');?>
</p>
<?php
	sm_show_messages($messages);
	
	if (empty($myspforms) AND empty($mysrforms)) print '<p>' . __('No forms saved.', 'sm_translate') . ' <a href="?page=sm_admin_forms_form&id=0">' . __('Create new form', 'sm_translate') . '</a>?</p>';
	else {
		print '<p><a href="?page=sm_admin_forms_form&id=0" class="add-new-h2">' . __('Create new form', 'sm_translate') . '</a></p>';
?>
<?php if (!empty($mysrforms)){?>
	<h3>
		<?php 
			_e('Service Request Forms', 'sm_translate' );
		?>		
	</h3>
	
	<div class="sm_list_status">
		<?php 
			foreach ($mysrforms_counts as $type => $count){
				if ($count) print "<div class=\"sm_status_label\">" . __(ucfirst($type), "sm_translate") . "</div><div class=\"sm_status_value\">" . $count . "</div>";
			}
			if (!empty($mysrforms_counts['archived']) AND !isset($_GET['show_archived'])) { ?>
			<div class="sm_show_archived"><a href="admin.php?page=sm_admin_forms&show_archived"><?php _e("Show archived","sm_translate"); ?></a></div>
		<?php } ?>
	</div>
	
	<?php if (!empty($mysrforms_counts['active']) OR ( isset($_GET['show_archived']) AND !empty($mysrforms_counts['archived']) )){?>
		<table cellspacing="0" class="wp-list-table widefat">
			<thead>
				<tr>
					<th><?php _e("Form name", "sm_translate")?></th>
					<th><?php _e("View", "sm_translate")?></th>
					<th><?php _e("ServiceMagic Activity", "sm_translate")?></th>
					<th><?php _e("Created/Edited", "sm_translate")?></th>
				</tr>
			</thead>
			<tfoot></tfoot>	
			<tbody >
				<?php foreach ($mysrforms as $form_item){ 
					//show archived only if in url
					if (!(isset($_GET['show_archived']) OR !$form_item->is_archived)) continue;
					$this_parameters = json_decode($form_item->parameters);?>
					<tr valign="top" >
						<td>
							<?php 
								print $form_item->name;
								if ($form_item->is_archived) print " (" . __("Archived", "sm_translate") . ")";
							?>
							<div class="row-actions">
							<?php if ($form_item->is_archived) { ?>
								<a class="alter_me" href="" data-id="<?php print $form_item->id;?>" data-type="sr" data-action="unarchive"><?php _e("Un archive", "sm_translate")?></a>
								| <a class="alter_me" href="" data-id="<?php print $form_item->id;?>" data-type="sr" data-action="delete"><?php _e("Delete", "sm_translate")?></a>
							<?php } else { ?>
								<a href="?page=sm_admin_sr_forms_form&id=<?php print $form_item->id;?>" ><?php _e("Edit", "sm_translate")?></a>
								| <a class="capture_me" href="" data_shortcode="[sm action=&quot;named_sr_form&quot; form_name=&quot;<?php print $form_item->embedable_name;?>&quot;]" ><?php _e("Shortcode", "sm_translate")?></a>
								| <a class="alter_me" href="" data-id="<?php print $form_item->id;?>" data-type="sr" data-action="archive"><?php _e("Archive", "sm_translate")?></a>
								
							<?php } ?>
							</div>
						</td>
						<td><?php _e($this_parameters->view, "sm_translate");?></td>
						<td><?php print $form_item->activity_title;?></td>
						<td><?php print $form_item->created . '<br>' . $form_item->altered;?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php } ?>
<?php } ?>

<?php if (!empty($myspforms)){?>
	<h3>
		<?php 
			_e('Service Provider Forms', 'sm_translate' );
		?>
	</h3>
	
	<div class="sm_list_status">
		<?php 
			foreach ($myspforms_counts as $type => $count){
				if ($count) print "<div class=\"sm_status_label\">" . __(ucfirst($type), "sm_translate") . "</div><div class=\"sm_status_value\">" . $count . "</div>";
			}
		 if (!empty($myspforms_counts['archived']) AND !isset($_GET['show_archived'])) { ?>
			<div class="sm_show_archived"><a href="admin.php?page=sm_admin_forms&show_archived"><?php _e("Show archived","sm_translate"); ?></a></div>
		<?php } ?>
	</div>
	<?php if (!empty($myspforms_counts['active']) OR ( isset($_GET['show_archived']) AND !empty($myspforms_counts['archived']) )){?>
		<table cellspacing="0" class="wp-list-table widefat">
			<thead>
				<tr>
					<th><?php _e("Form name", "sm_translate")?></th>
					<th><?php _e("View", "sm_translate")?></th>
					<th><?php _e("ServiceMagic Trades", "sm_translate")?></th>
					<th><?php _e("Created/Edited", "sm_translate")?></th>
				</tr>
			</thead>
			<tfoot></tfoot>	
			<tbody >
				<?php foreach ($myspforms as $form_item){ 
					//show archived only if in url
					if (!(isset($_GET['show_archived']) OR !$form_item->is_archived)) continue;
					$this_parameters = json_decode($form_item->parameters);?>
					<tr valign="top" >
						<td>
							<?php 
								print $form_item->name;
								if ($form_item->is_archived) print " (" . __("Archived", "sm_translate") . ")";
							?>
							<div class="row-actions">
							<?php if ($form_item->is_archived) { ?>
								<a class="alter_me" href="" data-id="<?php print $form_item->id;?>" data-type="sp" data-action="unarchive"><?php _e("Un archive", "sm_translate")?></a>
								| <a class="alter_me" href="" data-id="<?php print $form_item->id;?>" data-type="sp" data-action="delete"><?php _e("Delete", "sm_translate")?></a>
							<?php } else { ?>
							<a href="?page=sm_admin_sp_forms_form&id=<?php print $form_item->id;?>" ><?php _e("Edit", "sm_translate")?></a>
							| <a class="capture_me" href="" data_shortcode="[sm action=&quot;named_sp_form&quot; form_name=&quot;<?php print $form_item->embedable_name;?>&quot;]" ><?php _e("Shortcode", "sm_translate")?></a>
							| <a class="alter_me" href="" data-id="<?php print $form_item->id;?>" data-type="sp" data-action="archive"><?php _e("Archive", "sm_translate")?></a>
							<?php } ?>
							</div>
						</td>
						<td><?php _e($this_parameters->view, "sm_translate");?></td>
						<td><?php print implode(", ", $this_parameters->worktypes);?></td>
						<td><?php print $form_item->created . '<br>' . $form_item->altered;?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php } ?>
<?php } ?>
<br>

<div class=\"tip\"><p><?php _e('Tip : To embed a form, copy the shortcode and paste it into a post.', 'sm_translate');?></p></div>
<form id="archiver" action="admin.php?page=sm_admin_forms" method="post">
	<input type="hidden" name="_archive_nounce" value="<?php print $nounce;?>">
	<input type="hidden" name="id" value="">
	<input type="hidden" name="action" value="">
	<input type="hidden" name="type" value="">
</form>
<script>
	jQuery(function($){
		$(".capture_me").click(function(){
			window.prompt ("<?php _e("1. Copy Shortcode (Ctrl+C)", "sm_translate")?>\n<?php _e("2. Close popup (Enter)", "sm_translate")?>\n<?php _e("3. Navigate to a post or page", "sm_translate")?>\n<?php _e("4. Paste into content (Ctrl+V)", "sm_translate")?>", $(this).attr("data_shortcode"));
		});
		$(".alter_me").click(function(){
			var $this = $(this);
			switch($this.data("action")){
				case "delete": var msg = '<?php _e("Confirm you wish to delete this item permanently", "sm_translate")?>?';break;
				case "archive": var msg = '<?php _e("Confirm you wish to archive this item", "sm_translate")?>?';break;
				case "unarchive": var msg = '<?php _e("Confirm you wish to de-archive this item", "sm_translate")?>?';break;
			}
			if ( window.confirm(msg) ){
				var $archiver = $("#archiver");
				$("input[name='id']", $archiver).val($this.data("id"));
				$("input[name='type']", $archiver).val($this.data("type"));
				$("input[name='action']", $archiver).val($this.data("action"));
				$archiver.submit();
			}
			return false;
		});
	})
</script>
<?php
		
	}
	//placeholder for dynamic translationz	
	__("basic","sm_translate");
?>

</div>