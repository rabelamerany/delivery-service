<?php defined('BASEPATH') OR exit('No direct script access allowed');


class EcomProfile extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('general_helper');
		$this->load->database();
		$this->load->library('grocery_CRUD');
		$this->load->library('form_validation');
		
		$this->template->write_view('sidenavs', 'tes/customer_sidenavs.php', true);
		$this->template->write_view('navs', 'tes/customer_topnavs.php', true);	
	}
	
	function index()
	{
		$userdata = $this->session->userdata();

		if (strpos($_SERVER['REQUEST_URI'], $userdata['customerNumber']) === false) {
			redirect('ecomOrdersCustomers');
		}
		
		$this->template->write('title', 'Ecom Orders', true);
		$this->template->write('header', 'Ecom Orders');

		$crud = new grocery_CRUD();
		//$crud->set_theme('datatables');
		$crud->set_table('ecom_orders');
		$crud->set_subject('Ecom Orders');
		
		$crud = new grocery_CRUD();
		
		$crud->set_table('customers');
		$crud->set_subject('Customer');
		
		$crud->columns('customerName', 'phone', 'addressLine1', 'addressLine2', 'city', 'postalCode', 'registration_date');
		//$crud->fields('customerName', 'type', 'phone', 'email', 'addressLine1', 'addressLine2', 'city', 'postalCode', 'balance','password_customers');
		$crud->display_as('customerName', 'Name')
			->display_as('password_customers', 'Password')
			->display_as('phone', 'Phone')
			->display_as('email', 'Email')
			->display_as('addressLine1', 'Address Line 1')
			->display_as('addressLine2', 'Address Line 2')
			->display_as('city', 'City')
			->display_as('registration_date', 'Registration date')
			->display_as('postalCode', 'Postal Code')
			->display_as('balance', 'Balance');
		
			$crud->where(array('customerNumber' => $userdata['customerNumber']));

			$crud->unset_add();
			$crud->unset_list();
			$crud->callback_after_update([$this, 'generate_invoice_ecom']);

		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}
}