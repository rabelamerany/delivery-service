<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Controller extends CI_Model
{
	function __construct()
	{
		$this->userTbl = 'controllers';
	}
}
