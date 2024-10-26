<?php 
//WEBSITE-3691 : API Change the publishers' rights
$url = site_url();
$url .='/wp-admin/admin.php?page=sm_';
?>
<div class="wrap">
<h2>First steps, help and suggestion</h2>

<p>Make your first steps with the ServiceMagic API.</p>

<ol>
<?php if(current_user_can('sm_api_manage_options')){ ?>
	<li>Enter your ServiceMagic <a href="<?php echo $url?>admin_settings">credentials</a>. Your username is your affiliate login. If you do not have this information, please contact the person in charge of your account.</li>
<?php } ?>
<!--<li>Manage your technical options</li>-->
<?php if (get_option('sm_creds')){ ?>
	<li>Manage your design and tracking <a href="<?php echo $url?>admin_form_defaults">options.</a></li>
	<li>Create your <a href="<?php echo $url?>admin_forms">embeddable forms.</a></li>
<?php } ?>	
<li>How does it work? Find the answers in our <a href="<?php echo $url?>admin_docs">documentation</a>.</li>
<?php if(current_user_can('sm_api_manage_options')){ ?>
	<li>The plugin's <a href="<?php echo $url?>history">history</a> allows you to track news by user.</li>
<?php } ?>
</ol>

<p>If after reading the documentation, you have trouble or wish to make a suggestion: </p>
<ol>
<li>Call your ServiceMagic account manager.</li>
<li>Or send an email to <a href="mailto:apihelp@servicemagic.eu">apihelp@servicemagic.eu</a>.</li>
</ol>
</div>