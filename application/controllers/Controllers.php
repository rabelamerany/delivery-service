<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Controllers extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper('general_helper');
		if (!is_user_logged_in()) {
			redirect('login');
		}

		if (!(is_granted('Admin') || is_granted('Controller'))) {
			redirect('errors/error403');
		}

		$this->load->database();
		$this->load->library('grocery_CRUD');
		$this->load->library('form_validation');

		$this->template->write_view('sidenavs', 'template/default_sidenavs', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}

	function index()
	{
		$this->template->write('title', 'Cash In/Out', TRUE);
		$this->template->write('header', 'Cash in/Out');

		$crud = new grocery_CRUD();
		try {
			$crud->set_table('controllers');
		} catch (Exception $e) {
			die($e->getMessage());
		}
		$crud->set_subject('Controller');

		$fields = ['direction', 'amount', 'date', 'remarks', 'from_To', 'type'];

		$crud->columns($fields);
		$crud->fields($fields);
		$crud->display_as('direction', 'Direction')
			->display_as('amount', 'Amount')
			->display_as('date', 'Date')
			->display_as('remarks', 'Remarks')
			->display_as('from_To', 'From/To')
			->display_as('type', 'Type');
		$crud->required_fields($fields);

		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}
}
