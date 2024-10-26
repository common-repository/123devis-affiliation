<h2><?php _e('Choose your Form', 'sm_translate');?></h2>
<?php foreach ($embeddable_folders as $folder){
	$file_path = $folder . "/info.php";

	if (file_exists($file_path)){
		require $file_path;
	}
}