<?php
	class sm_sp_interview extends sm_interview  {
		public function get_worktypes(){
			foreach($this->data['questions'] as $question){
				if ($question['name'] == 'sp_id_worktype'){
					return $question['options'];
				}
			}
			throw new sm_exception_general("Did not find worktype list in interview");
		}

		public function get_submission_result_obj_by_api($form_data){
			return $this->api->sp->create->post($form_data);
		}

	}