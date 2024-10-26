<?php
	class sm_sp_interview__thanks extends sm_renderable  {
		public function render(){
			$translation = $this->data->get_parameter("translate");
			$ty_message = get_option("sm_sp_ty_message", "");
      
			$s = '<div class="sm_interview sm_thanks">';
      
      if ($ty_message) {
        $s .= $ty_message;
      } else {
        $s .= '<h2>' . $translation->trans("Thank you") . '</h2>';
        $s .= $translation->trans('<p>We received your information and one of our account managers will contact you shortly to help you get started and receive leads.</p>');      
      }
      
      $s .= '</div>';

			$s .= $this->data->get_parameter("success_more_text_ty", "");
			
			return $s;
		}
	}
	/*	
		'<div class="sm_interview sm_thanks"><h2>Merci pour votre demande</h2>'.
		'<p>L\'un de nos conseillers va maintenant traiter votre demande d\'inscription.</p>'.
		'<p>Il vous confirmera votre inscription sous 48 heures.</p>'.
		'</div>';
	*/