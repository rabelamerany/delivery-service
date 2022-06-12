<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* This is Example Controller
*/
class Roles extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();

		$this->load->helper('general_helper');
		if (!is_user_logged_in())
		{
			redirect('login');
		}

		if (!is_granted('Admin'))
		{
			redirect('errors/error403');
		}

		$this->load->database();
		$this->load->helper('url');
		$this->load->library('grocery_CRUD');

		$this->template->write_view('sidenavs', 'template/default_sidenavs', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}

	function index() {
		$this->template->write('title', 'Customers', TRUE);
		$this->template->write('header', 'Customers');

		$crud = new grocery_CRUD();
		$crud->set_table('customers');
		$crud->columns('customerName','contactLastName','phone','city','country','salesRepEmployeeNumber','creditLimit');
		$crud->display_as('salesRepEmployeeNumber','from Employeer')
			 ->display_as('customerName','Name')
			 ->display_as('contactLastName','Last Name');
		$crud->set_subject('Customer');
		$crud->set_relation('salesRepEmployeeNumber','employees','lastName');
		$output = $crud->render();
		$this->template->write_view('content','example',(array)$output);

		$this->template->render();
	}


}
