<?php
	class sm_sr_category_list__basic extends sm_renderable  {

		public $default_parameters = array(
			"cols" => 2,
			"activity_link" => "<a href=\"get_activity_list.php?category_id=[id]\">[label]</a>\n"
		);
	
		function render(){		
			$category_list = $this->data->get_data();
			
			$item_count = count($category_list);
			$cols = $this->data->get_parameter("cols");
			$split_int = round($item_count / $cols);
			
			$s = "<div class=\"sm_cols\">\n";
			$s .= "<div class=\"sm_splitcol_$cols\">\n";
			foreach($category_list as $cntr => $link){
				$s .= "<div class=\"sm_item\">\n";
				$s .= $this->use_template('activity_link', $link);
				$s .= "</div>\n";
				if (($cntr+1) % $split_int == 0){
					$s .= "</div>\n<div class=\"sm_splitcol_$cols\">\n";
				}
			}
			$s .= "</div>\n";
			$s .= "<br class=\"sm_clear\">";
			$s .= "</div>\n\n";

			return $s;
		}
	}