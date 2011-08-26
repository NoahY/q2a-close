<?php
        
/*              
        Plugin Name: Embed YouTube
        Plugin URI: https://github.com/NoahY/q2a-embed
        Plugin Description: Embed YouTube links
        Plugin Version: 0.2
        Plugin Date: 2011-07-30
        Plugin Author: NoahY
        Plugin Author URI:                              
        Plugin License: GPLv2                           
        Plugin Minimum Question2Answer Version: 1.3
*/                      
                        
                        
        if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
                        header('Location: ../../');
                        exit;   
        }               

        qa_register_plugin_module('module', 'qa-close-admin.php', 'qa_close_admin', 'Close Admin');
        
        qa_register_plugin_layer('qa-close-layer.php', 'Close Layer');
                        
                        
/*                              
        Omit PHP closing tag to help avoid accidental output
*/                              
                          

