<?php
    class qa_close_admin {

	function option_default($option) {
		
	    switch($option) {
		case 'closed_question_title':
		    return '[closed]';
		case 'closed_question_text':
		    return 'This question has been closed by # for the following reason: $';
		case 'close_button_text':
		    return 'close';
		case 'reopen_button_text':
		    return 'reopen';
		default:
		    return null;				
	    }
		
	}
        
        function allow_template($template)
        {
            return ($template!='admin');
        }       
            
        function admin_form(&$qa_content)
        {                       
                            
        // Process form input
            
            $ok = null;
            
            if (qa_clicked('close_save')) {
		if((bool)qa_post_text('close_enable') && !qa_opt('close_enable')) {
		    $table_exists = qa_db_read_one_value(qa_db_query_sub("SHOW TABLES LIKE '^postmeta'"),true);
		    if(!$table_exists) {
			qa_db_query_sub(
			    'CREATE TABLE IF NOT EXISTS ^postmeta (
			    meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			    post_id bigint(20) unsigned NOT NULL,
			    meta_key varchar(255) DEFAULT NULL,
			    meta_value longtext,
			    PRIMARY KEY (meta_id),
			    KEY post_id (post_id),
			    KEY meta_key (meta_key)
			    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8'
			);			
		    }		    
		}
                qa_opt('close_enable',(bool)qa_post_text('close_enable'));
                qa_opt('close_enable_own',(bool)qa_post_text('close_enable_own'));
                qa_opt('close_selected',(bool)qa_post_text('close_selected'));
                qa_opt('closed_question_title',qa_post_text('closed_question_title'));
                qa_opt('closed_question_text',qa_post_text('closed_question_text'));
                qa_opt('close_button_text',qa_post_text('close_button_text'));
                qa_opt('reopen_button_text',qa_post_text('reopen_button_text'));
                $ok = 'Settings Saved.';
            }
  
	    qa_set_display_rules($qa_content, array(
		    'close_enable_own' => 'close_enable',
	    ));
                    
        // Create the form for display
            
            $fields = array();
            
            $fields[] = array(
                'label' => 'Enable question closing',
                'tags' => 'NAME="close_enable"',
                'value' => qa_opt('close_enable'),
                'type' => 'checkbox',
                'note' => '<i>by default, only moderators and admin can close questions.</i>',
            );
            
            $fields[] = array(
                'label' => 'Allow closing own questions',
                'tags' => 'NAME="close_enable_own"',
                'value' => qa_opt('close_enable_own'),
                'type' => 'checkbox',
            );
            
            
            $fields[] = array(
                'label' => 'Automatically close questions with selected answers',
                'tags' => 'NAME="close_selected"',
                'value' => qa_opt('close_selected'),
                'type' => 'checkbox',
            );
            
            $fields[] = array(
                'label' => 'Text to add to closed question title',
                'tags' => 'NAME="closed_question_title"',
                'value' => qa_opt('closed_question_title'),
            );
            
            $fields[] = array(
                'label' => 'Closed question notice text',
                'tags' => 'NAME="closed_question_text"',
                'note' => '<i>$ will be replaced by the reason and # by the closing user.</i>',
                'value' => qa_opt('closed_question_text'),
            );

            $fields[] = array(
                'label' => 'Close button text',
                'tags' => 'NAME="close_button_text"',
                'value' => qa_opt('close_button_text'),
            );

            $fields[] = array(
                'label' => 'Reopen button text',
                'tags' => 'NAME="reopen_button_text"',
                'value' => qa_opt('reopen_button_text'),
            );

            return array(           
                'ok' => ($ok && !isset($error)) ? $ok : null,
                    
                'fields' => $fields,
             
                'buttons' => array(
                    array(
                        'label' => 'Save',
                        'tags' => 'NAME="close_save"',
                    )
                ),
            );
        }
    }

