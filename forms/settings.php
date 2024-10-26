<!-- WEBSITE-3658 : API wording fixes -->
<div class="wrap">
<h2><?php _e('Credentials', 'sm_translate' );?></h2>

<?php
    if (empty($messages)){
        $messages["updated"] = __("You are using the Servicemagic Wordpress plugin version", "sm_translate"). " : <b>" . $sm_version . "</b>.";
        if (!empty($current_sm_creds["sm_username"])){
            $messages['updated'] .= "<br>" . __("You are logged in as", "sm_translate") . " : <b>" . $current_sm_creds["sm_username"] . "</b>.";
        }
    }
    sm_show_messages($messages);    
?>

		
<p><?php _e("Please enter your ServiceMagic login and password.", 'sm_translate');?></p>
<form method="post" action="" class="sm_form">
	<input type="hidden" name="_nonce" value="<?php print $nounce?>">
	<input type="hidden" name="sm_api_url" value="<?php print sm_val_in_arrays('sm_api_url', array($_POST), $sm_api_url);?>">
	
	<div class="sm_form_item">
		<label for="sm_username"><?php _e("Username", "sm_translate")?></label><br />
		<input id="sm_username" type="text" class="sm_txt" name="sm_username" value="<?php if (isset($_POST['sm_username'])) echo $_POST['sm_username'];?>">
	</div>
	<div class="sm_form_item">
		<label for="sm_password"><?php _e("Password", "sm_translate")?></label><br />
		<input id="sm_password" type="password" class="sm_pwd" name="sm_password" value="<?php if (isset($_POST['sm_password'])) echo $_POST['sm_password'];?>">
	</div>
	<div class="sm_form_item">
		<label><?php _e("Locale", "sm_translate")?></label>	<br />	
		<?php foreach(array("fr", "uk") as $server){?>
		<input type="radio" id="sm_api_server_<?php echo $server; ?>" name="sm_api_server" value="<?php print $server;?>" <?php if (sm_val_in_arrays('sm_api_server', array($_POST), $sm_api_server) == $server) print " checked ";?>><label for="sm_api_server_<?php echo $server; ?>"><?php print strtoupper($server)?></label><br>
		<?php } ?>
	</div>
	<div <?php if (!(array_key_exists('dev', $_REQUEST) || array_key_exists('SM_IS_OUR_IP', $_SERVER))){?>style="display:none"<?php } ?>>
		<h3>Options Dev</h3>
		<div class="sm_form_item">
			<label><?php _e("Locale", "sm_translate")?></label><br />
			<?php
            foreach(array("dev-fr", "ldev-fr-".substr(SM_DB_MASTER_PORT,1)) as $server){?>
                <input type="radio" id="sm_api_server_<?php echo $server; ?>" name="sm_api_server" value="<?php print $server;?>" <?php if (sm_val_in_arrays('sm_api_server', array($_POST), $sm_api_server) == $server) print " checked ";?>><label for="sm_api_server_<?php echo $server; ?>"><?php print strtoupper($server)?></label><br>
			<?php } ?>
		</div>
		<div class="sm_form_item" >
			<label for="parent">SM API Url</label><br />
			<input type="text" class="sm_txt_wide" name="sm_api_url" value="<?php print sm_val_in_arrays('sm_api_url', array($_POST), $sm_api_url);?>"  style="width:200px">
		</div>
	</div>
	
	<div class="sm_form_item" >
		<input type="submit" class="sm_submit button-primary" value="<?php _e('Save Login Details', 'sm_translate');?>">
	</div>
</form>