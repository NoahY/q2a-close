<?php

	class qa_html_theme_layer extends qa_html_theme_base {

		var $closed = false;

		function doctype(){

			if (qa_opt('close_enable') || (qa_opt('close_selected') && $this->content['q_view']['raw']['selchildid'])) {

				$closed_array = qa_db_read_all_assoc(
					qa_db_query_sub(
						'SELECT * FROM ^postmeta WHERE meta_key=$',
						'is_closed'
					)
				);
				foreach($closed_array as $q) {
					$closed[(int)$q['post_id']] = $q['meta_value'];
				}
				if(isset($this->content['q_view'])) {

					$qid = $this->content['q_view']['raw']['postid'];
					$author = $this->content['q_view']['raw']['userid'];

					$this->closed = @$closed[$qid];

					if(qa_clicked('close_post') && !$this->closed) $this->close_post($qid,$author);
					else if(qa_clicked('reopen_post') && $this->closed) $this->reopen_post($qid,$author);

					if(!$this->closed && qa_opt('close_selected') && $this->content['q_view']['raw']['selchildid']) {
						$this->closed = true;
					}

					if($this->closed) {

					// add post elements
						
						if($this->closed !== true) {
							
							// button
							
							$closer = preg_replace('/\^.*/','',$this->closed);
							
							if (($closer == $author && qa_opt('close_enable_own') && qa_get_logged_in_userid() == $closer) || qa_get_logged_in_level()>=QA_USER_LEVEL_MODERATOR ){
								$this->content['q_view']['form']['buttons']['open'] = array(
									'tags'=>'NAME="reopen_post"',
									'label'=> qa_opt('reopen_button_text'),
								);
							}
							
							// title and message

							$this->content['title'] .= ' '.qa_opt('closed_question_title');
							$closed_parts = explode('^',$this->closed,2);
							
							$closed_message_title = str_replace('$',$closed_parts[1],qa_opt('closed_question_text'));
							$closed_message_title = preg_replace('/<[^>]*>/','',$closed_message_title);
							$closed_message_title = str_replace('#',$this->getHandleFromId((int)$closed_parts[0]),$closed_message_title);
							
							$closed_message_div = str_replace('$','<span class="question-closed-reason">'.$closed_parts[1].'</span>',qa_opt('closed_question_text'));
							$closed_message_div = str_replace('#','<A HREF="'.qa_path_html('user/'.$this->getHandleFromId((int)$closed_parts[0])).'" CLASS="qa-user-link">'.$this->getHandleFromId((int)$closed_parts[0]).'</A>',$closed_message_div);

							$this->content['q_view']['c_list'][] = array('content' => '<div id="question-closed-message">'.$closed_message_div.'</div>','hidden'=>'');
						}

					// remove editing capabilities
						
						if(qa_get_logged_in_level()<QA_USER_LEVEL_MODERATOR) {

							unset($GLOBALS['qa_state']);
							unset($this->qa_state);
							unset($_POST['doanswerq']);
							unset($_POST['doansweradd']);
							unset($_POST['doeditq']);
							unset($_POST['dosaveq']);
							unset($_POST['doedit']);
							unset($_POST['dosave']);
							unset($_POST['docommentq']);
							unset($_POST['docommentaddq']);
							
							unset($this->content['q_view']['form']['buttons']['edit']);
							unset($this->content['q_view']['form']['buttons']['answer']);
							unset($this->content['q_view']['form']['buttons']['comment']);
							
							if($this->closed !== true) {
								
								$this->content['q_view']['vote_state'] = 'disabled';
								if(isset($this->content['q_view']['vote_down_tags'])) $this->content['q_view']['vote_down_tags'] = preg_replace('/TITLE="[^"]+"/i','TITLE="'.$closed_message_title.'"',$this->content['q_view']['vote_down_tags']);
								if(isset($this->content['q_view']['vote_up_tags'])) $this->content['q_view']['vote_up_tags'] = preg_replace('/TITLE="[^"]+"/i','TITLE="'.$closed_message_title.'"',$this->content['q_view']['vote_up_tags']);
							}
							
							if(isset($this->content['q_view']['c_list'])) {
								foreach($this->content['q_view']['c_list'] as $cdx => $comment) {
									unset($this->content['q_view']['c_list'][$cdx]['form']['buttons']['edit']);
									unset($this->content['q_view']['c_list'][$cdx]['form']['buttons']['answer']);
									unset($this->content['q_view']['c_list'][$cdx]['form']['buttons']['comment']);							
								}
							}
							
							
							if(isset($this->content['a_list']['as'])) {
								foreach($this->content['a_list']['as'] as $idx => $answer) {

									unset($_POST['doedita_'.$idx]);
									unset($_POST['dosavea_'.$idx]);
									unset($_POST['docommenta_'.$idx]);
									unset($_POST['docommentadda_'.$idx]);
									
									unset($this->content['a_list']['as'][$idx]['c_form']);


									unset($this->content['a_list']['as'][$idx]['select_tags']);
									unset($this->content['a_list']['as'][$idx]['unselect_tags']);
									
									unset($this->content['a_list']['as'][$idx]['form']['buttons']['edit']);
									unset($this->content['a_list']['as'][$idx]['form']['buttons']['comment']);						
									
									if($this->closed !== true) {
										$this->content['a_list']['as'][$idx]['vote_state'] = 'disabled';
										if(isset($this->content['a_list']['as'][$idx]['vote_down_tags'])) $this->content['a_list']['as'][$idx]['vote_down_tags'] = 'TITLE="'.$closed_message_title.'"';
										if(isset($this->content['a_list']['as'][$idx]['vote_up_tags'])) $this->content['a_list']['as'][$idx]['vote_up_tags'] = 'TITLE="'.$closed_message_title.'"';
									}
									
									if(isset($answer['c_list'])) {
										foreach($answer['c_list'] as $cdx => $comment) {
											unset($this->content['a_list']['as'][$idx]['c_list'][$cdx]['form']['buttons']['edit']);
											unset($this->content['a_list']['as'][$idx]['c_list'][$cdx]['form']['buttons']['comment']);
										}
									}
								}
							}
							unset($this->content['q_view']['a_form']);					
							unset($this->content['q_view']['c_form']);		
						}			
						
					}
					else if (isset($this->content['q_view']['form']) && ((qa_opt('close_enable_own') && qa_get_logged_in_userid() == $author) || qa_get_logged_in_level()>=QA_USER_LEVEL_MODERATOR )){
						
						$this->content['q_view']['form']['buttons']['close'] = array(
							'tags'=>'NAME="close_post"'.(strpos(qa_opt('closed_question_text'),'$') !== false?' onclick="var reason=prompt(\'Please enter a reason for closing this question:\'); if(!reason) return false; jQuery(\'#close_question_reason\').val(reason)"':''),
							'label'=> qa_opt('close_button_text'),
						);
						$this->content['q_view']['form']['hidden']['close_question_reason'] = '" id="close_question_reason';
					}
				}
				if(isset($this->content['q_list']) && qa_opt('close_enable')) {
					foreach($this->content['q_list']['qs'] as $idx => $question) {
						if(isset($closed[$question['raw']['postid']])) {
							$closed_parts = explode('^',$closed[$question['raw']['postid']],2);
							$closed_message = preg_replace('/<[^>]*>/','',qa_opt('closed_question_text'));
							$closed_message = str_replace('$',preg_replace('/<[^>]*>/','',$closed_parts[1]),qa_opt('closed_question_text'));
							$closed_message = str_replace('#',$this->getHandleFromId((int)$closed_parts[0]),$closed_message);
							
							$this->content['q_list']['qs'][$idx]['title'] .= ' '.qa_opt('closed_question_title');
							
							$this->content['q_list']['qs'][$idx]['vote_state'] = 'disabled';
							if(isset($this->content['q_list']['qs'][$idx]['vote_down_tags'])) $this->content['q_list']['qs'][$idx]['vote_down_tags'] = 'TITLE="'.$closed_message.'"';
							if(isset($this->content['q_list']['qs'][$idx]['vote_up_tags'])) $this->content['q_list']['qs'][$idx]['vote_up_tags'] = 'TITLE="'.$closed_message.'"';
						}
					}					
				}
			}
			qa_html_theme_base::doctype();
		}

	// theme replacement functions

		function head_custom() {
			$this->output('<style>',
'#question-closed-message{
	font-style:italic; 
	background-color:silver; 
	padding:12px; 
	margin-top:12px;
}'
,
'.question-closed-reason{
	font-weight:bold; 
}'
,'</style>');
			qa_html_theme_base::head_custom();
		}




	// worker functions

		function close_post($qid,$author) {

			if ( (!qa_get_logged_in_userid()) || ((!qa_opt('close_enable_own') || qa_get_logged_in_userid() != $author) && qa_get_logged_in_level()<QA_USER_LEVEL_MODERATOR) ) return;
			$reason = qa_sanitize_html(qa_post_text('close_question_reason'));
			$this->closed = qa_get_logged_in_userid().'^'.$reason;
			
			qa_db_query_sub(
				'INSERT INTO ^postmeta (post_id,meta_key,meta_value) VALUES (#,$,$)',
				$qid,'is_closed',$this->closed
			);
		}

		function reopen_post($qid,$author) {
			
			$closer = preg_replace('/\^.*/','',$this->closed);
			if ( (!qa_get_logged_in_userid()) || ((!qa_opt('close_enable_own') || qa_get_logged_in_userid() != (int)$closer) && qa_get_logged_in_level()<QA_USER_LEVEL_MODERATOR) ) return;

			$this->closed = false;

			qa_db_query_sub(
				'DELETE FROM ^postmeta WHERE post_id=# AND meta_key=$',
				$qid,'is_closed'
			);
		}
		
		function getHandleFromId($userid) {
			require_once QA_INCLUDE_DIR.'qa-app-users.php';
			
			if (QA_FINAL_EXTERNAL_USERS) {
				$publictohandle=qa_get_public_from_userids(array($userid));
				$handle=@$publictohandle[$userid];
				
			} 
			else {
				$user = qa_db_single_select(qa_db_user_account_selectspec($userid, true));
				$handle = @$user['handle'];
			}
			return $handle;
		}		
	}

