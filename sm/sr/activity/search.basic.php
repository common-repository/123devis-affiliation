<?php
	class sm_sr_activity_search__basic extends sm_renderable  {
		
		public function render(){
			$s = "<div class=\"sm_search_box\">\n";
			$s .= "<div class=\"form\">\n";
			
			$s .= "<form method=\"post\" id=\"sm_search_box_form\"  action=\"#formname\" class=\"sm_form ";
		
			$s .= $this->data->get_api()->get_country();
			$s .= "\">\n";
			$s .= "<div class=\"sm_item\">\n";
			$s .= "<label for=\"sm_search_box_input\" class=\"sm_label\">We can help! Enter a keyword (ex. remodel)</label>\n";
			$s .= "<input type=\"text\" id=\"sm_search_box_input\" class=\"sm_submit required\" value=\"\">\n";
			$s .= "<input type=\"submit\" class=\"sm_submit\" value=\"Start\">\n";
			$s .= "<div id=\"sm_search_results\"></div>\n";
			$s .= "</div>";
			$s .= "</form>\n";
			$s .= "</div>\n";
			$s .= "</div>\n";
			
			
			$s .= "<script type=\"text/javascript\">\n".
				"jQuery(function($){\n".	
				"	$(\"#sm_search_box_form\").submit(function(){\n".
				"	 	var \$this = $(this);\n".
				'		var ctner = $("#sm_search_results", $this).html("Searching...");console.log(ctner);'."\n".
				'		$.get("/wp-admin/admin-ajax.php", {"action":"sm_ajax_activity_search","keyword":$("#sm_search_box_input", $(this)).val()}, function(data){'."\n".
				"			if (data.length){\n".
				"				ctner.html(\"<div>Does your project relate to one of the following?</div>\");\n".
				"				for (var sr_i in data){\n".
				'					ctner.append("<div>"+(sr_i > 0 ? "or " : "")+"<a href=\"/sm/interview\?id_activity="+data[sr_i].id+"\">"+data[sr_i].label+"</a></div>");'."\n".
				"				}\n".
				"			} else {\n".
				"				ctner.html(\"We could not find anything with this criteria, please try something different.\");\n".
				"			}\n".
				"		}, 'json');\n".
				"		return false;\n".
				"	}).validate({\n".
				"		errorPlacement: function(error, element) { \n".
				"			error.insertAfter( element.parent() );\n".
				"		}\n".
				"	});\n".
			"});\n";

			$s .= "</script>\n";
			
			return $s;
		}
		
	}