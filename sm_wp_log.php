<?php
	class sm_wp_log {
		function __construct($data){
			global $wpdb;
			global $current_user;

			if (!is_array($data)){
				$data = array("message" => $data);
			}

			//$_SERVER can contain too much info, filter to smaller set
			$filtered_server = array();
			$saveables = array(
				"HTTP",
				// Ci-dessous, on recherche 'HTTP' dans les clés de $_SERVER. 'HTTPS' sera trouvé aussi du coup. Pas de portage HTTPS nécessaire.
				"SERVER_PROTOCOL",
				"PATH"
			);

			foreach($saveables as $save_str){
				foreach ($_SERVER as $name => $val){
					if (strpos($name, $save_str) === 0){
						$filtered_server[$name] = $val;
					}
				}
			}

			$more_data = array(
				'path' => $_SERVER['REQUEST_URI'],
				'get_json' => json_encode($_GET),
				'post_json' => json_encode($_POST),
				'timest' => time(),
				'type' => "message",
				'user_name' => $current_user->user_login,
				'server_json' => json_encode($filtered_server)
			);

			$all_data = array_merge($more_data, $data);

			if (isset($all_data["message_more"]) && !is_string($all_data["message_more"])){
				$all_data["message_more"] = json_encode($all_data["message_more"]);
			}

			$r = $wpdb->insert(
				$wpdb->prefix . 'sm_log',
				$all_data
			);

		}
	}