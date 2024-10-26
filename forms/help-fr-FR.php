<?php
//WEBSITE-3691 : API Change the publishers' rights
$url = site_url();
$url .='/wp-admin/admin.php?page=sm_';
?>
<div class="wrap">
<h2>Premiers pas, aide et suggestions </h2>

<p>Faites vos premiers pas avec l'API 123devis.</p>

<ol>
<?php if(current_user_can('sm_api_manage_options')){ ?>
	<li>Rentrez vos <a href="<?php echo $url?>admin_settings">identifiants</a> 123devis. Votre identifiant est votre login affilié. Si vous n'avez plus ces informations, rapprochez vous de la personne en charge de votre compte.</li>
<?php } ?>
<!--<li>Gérez vos options techniques</li>-->
<?php if (get_option('sm_creds')){ ?>
	<li>Gérez vos <a href="<?php echo $url?>admin_form_defaults">options</a> de design et de tracking.</li>
	<li>Créez vos <a href="<?php echo $url?>admin_forms">formulaires intégrables</a>.</li>
<?php } ?>
<li>Comment ça marche? Trouvez les réponses dans notre <a href="<?php echo $url?>admin_docs">documentation.</a></li>
<?php if(current_user_can('sm_api_manage_options')){ ?>
	<li>L'<a href="<?php echo $url?>history">historique</a> du plugin vous permet de suivre l'actualité par utilisateur.</li>
<?php } ?>
</ol>

<p>Si la documentation n’est pas suffisante et que vous rencontrez un problème avec l’API ou si vous souhaitez nous faire une suggestion :</p>
<ol>
<li>Appelez votre responsable de compte 123devis.</li>
<li>Ou par email à  <a href="mailto:apihelp@servicemagic.eu">apihelp@servicemagic.eu</a>.</li>
</ol>
</div>