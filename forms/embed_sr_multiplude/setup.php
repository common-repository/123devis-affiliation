<?php 
   wp_enqueue_script('jquery-ui-core', array("jquery"));
   wp_enqueue_script("jquery-ui-sortable", array("jquery-ui-core"));
   wp_enqueue_script("jquery-ui-widget", array("jquery-ui-core"));
   wp_enqueue_script("jquery-ui-mouse", array("jquery-ui-core"));
   wp_enqueue_script("json2");
    
   wp_enqueue_script("eip",  plugins_url('ui/js/jquery.edit_in_place.js', dirname(dirname(__FILE__))));
   wp_enqueue_style('eip',  plugins_url('ui/css/edit_in_place.css', dirname(dirname(__FILE__))));

   wp_enqueue_script('sm_mp_builder',  plugins_url('ui/js/jquery.mp_builder.js', dirname(dirname(__FILE__))));
   wp_enqueue_style('sm_mp_builder',  plugins_url('ui/css/mp_builder.css', dirname(dirname(__FILE__))));
