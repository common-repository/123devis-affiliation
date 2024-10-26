<?php if (array_key_exists('dev', $_REQUEST) || array_key_exists('SM_IS_OUR_IP', $_SERVER)){ ?>
    <h3><?php _e('"User Config" presentation', "sm_tranlate")?></h3>
    <p><?php _e("This presentation allows the admin to configure most things about a form including creating multiple steps.", "sm_translate")?></p>
    <span class="sm_beta"><?php _e("This form presentation is in BETA", "sm_translate")?></span> <a href="?page=sm_admin_sr_forms_form&id=0&view=multiplude" disabled="true"><?php _e("Choose", "sm_translate")?></a> 
<?php } ?>