<?php
  $form_config = stripslashes_deep($_REQUEST['form_config']);
  $form_config = json_decode($form_config, 1);

  if (empty($form_config['steps']) OR empty($form_config['steps'])){
     $validator->add_error("form_config", __("Issue with form configuration, missing steps or q_in_col", "sm_translate"));
  }
