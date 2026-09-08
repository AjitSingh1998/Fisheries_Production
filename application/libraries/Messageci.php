<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter messaging library
 *
 * Message makes it easy to display success, error and info alerts to your users
 *
 * @author		Kyle Noland
 * @link		http://www.kylenoland.com
 * @since		Version 0.0.1
 *
 */

// ------------------------------------------------------------------------

#[AllowDynamicProperties]
class Messageci {
    
    var $CI;
    var $messages = array();
    
    private $success_css;
    private $error_css;
    private $info_css;
    private $success_icon;
    private $error_icon;
    private $info_icon;
	private $close_button;
    
    function __construct()
    {    
        $this->CI =& get_instance();        
        
        // If there are already messages in flashdata, copy them locally
        if($this->CI->session->flashdata('_messages'))
        {
        	$this->messages = $this->CI->session->flashdata('_messages');
        }
        
        // Get css classes for each message type from the config file
        $this->success_css	= implode(' ', $this->CI->config->item('message_success_css'));
        $this->error_css 	= implode(' ', $this->CI->config->item('message_error_css'));
        $this->info_css 	= implode(' ', $this->CI->config->item('message_info_css'));
		// Get icon for each message type from the config file
        $this->success_icon	= implode(' ', $this->CI->config->item('message_success_icon'));
        $this->error_icon 	= implode(' ', $this->CI->config->item('message_error_icon'));
        $this->info_icon 	= implode(' ', $this->CI->config->item('message_info_icon'));
		// Get close button for each message type from the config file
        $this->close_button	= implode(' ', $this->CI->config->item('message_close_button'));
    }
    
    
    function set($message, $type)
    {
    	$obj = new stdClass();
    	$obj->content = $message;
    	$obj->type = $type;
    	$this->messages[] = $obj;
        
        $this->CI->session->set_flashdata('_messages', $this->messages);
    }
    
    
    function display()
    {
    	if(count($this->messages) > 0)
    	{	$messageContainer = array();
    		foreach($this->messages as $msg)
    		{
	    		$css = $this->_get_css($msg->type);
				$icon = $this->_get_icon($msg->type);
				$close = $this->close_button;
				
	    		$otag = $this->CI->config->item('message_inner_wrapper');
	    		$ctag = $this->_get_ctag($otag);	    		
	    		
	    		$messageContainer[] = "<div class=\"$css\">{$close}{$icon} {$otag}{$msg->content}{$ctag}</div>";
    		}
			return $messageContainer;
        }        
    }
    
    
    private function _get_css($type)
    {
	    switch($type)
	    {
		    case 'success':	return $this->success_css; break;
		    case 'error': 	return $this->error_css; break;
		    case 'info': 	return $this->info_css; break;
	    }
    }
    
	private function _get_icon($type)
    {
	    switch($type)
	    {
		    case 'success':	return $this->success_icon; break;
		    case 'error': 	return $this->error_icon; break;
		    case 'info': 	return $this->info_icon; break;
	    }
    }
    
    private function _get_ctag($otag)
    {
    	switch($otag)
    	{
	    	case '':
		    	return '';
		    	break;
		    case '<p>':
		    	return '</p>'; 
		    	break;
		    case '<div>':
		    	return '</div>'; 
		    	break;
		    case '<span>':
		    	return '</span>'; 
		    	break;
		    case '<td>':
		    	return '</td>'; 
		    	break;	
    	}
    }
}
