<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BalanceHistory extends CI_Controller
{
	function __construct()
	{
		set_time_limit(0);
		parent::__construct();
		
		$this->load->database();
		$this->load->library('grocery_CRUD');
		
		$this->template->write_view('sidenavs', 'template/default_sidenavs', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}
	
	function index()
	{
		$this->load->helper('general_helper');
		if (!is_user_logged_in()) {
			redirect('login');
		}
		
		if (!(is_granted('Admin') || is_granted('Dispatcher'))) {
			redirect('errors/error403');
		}
		
		$this->template->write('title', 'Balance History', TRUE);
		$this->template->write('header', 'Balance History');
		
		$crud = new grocery_CRUD();
		$crud->set_table('balance_history');
		$crud->set_subject('Balance History');
		
		$crud->set_relation('customer_id','customers','customerName');
		
		$crud->columns('customer_id', 'difference', 'new_balance', 'date_operation');
		$crud->display_as('customer_id', 'Customer Name')
			->display_as('difference', 'Difference')
			->display_as('new_balance', 'New Balance')
			->display_as('date_operation', 'Date Operation');
		
		$crud->order_by('id','desc');
		
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_delete();
		
		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}
}
