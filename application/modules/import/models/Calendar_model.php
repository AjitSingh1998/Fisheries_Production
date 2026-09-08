<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[AllowDynamicProperties]
class Calendar_model extends MY_Model {

	function index()
	{
		$data = 'this is calendar model data';
		return $data;
	}
}
