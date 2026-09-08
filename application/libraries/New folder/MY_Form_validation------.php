<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class MY_Form_validation extends CI_Form_validation
{
   
    public $CI;
    public function run($module = '', $group = '')
    {
        (is_object($module)) AND $this->CI = &$module;

        return parent::run($group);
    }
	
} 