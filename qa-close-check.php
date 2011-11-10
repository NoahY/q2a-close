<?php

	class close_check {
		
// main event processing function
		
		function process_event($event, $userid, $handle, $cookieid, $params) {
			
			if(!qa_opt('close_auto_close')) return;
			
 
			 $special = array(
				'a_post',
				'c_post'
			);

			
			if($event == 'a_post') {
				$count = qa_db_read_one_value(
					qa_db_query_sub(
						'SELECT COUNT(postid) FROM ^posts WHERE parentid=#',
						$params['parentid']
					),
					true
				);	
				if($count >= qa_opt('close_auto_close')) {
					qa_db_query_sub(
						'INSERT INTO ^postmeta (post_id,meta_key,meta_value) VALUES (#,$,$) ON DUPLICATE KEY UPDATE meta_value=meta_value',
						$params['parentid'],'is_closed',$userid.'^'.qa_opt('close_auto_close_reason')
					);			
				}
			}
		}
	}