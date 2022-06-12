<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Promotion extends CI_Controller
{
	function __construct()
	{
		set_time_limit(0);
		parent::__construct();
		
		$this->load->helper('general_helper');
		if (!is_user_logged_in()) {
			redirect('login');
			exit();
		}
		
		if (!is_granted('Admin') && !is_granted('Dispatcher')) {
			redirect('errors/error403');
			exit();
		}
		
		$this->load->database();
		$this->load->helper('url');
		$this->load->library('grocery_CRUD');
		
		$this->template->write_view('sidenavs', 'template/default_sidenavs', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}
	
	function index()
	{
		$this->template->write('title', 'Coupon Campaigns', true);
		$this->template->write('header', 'Coupon Campaigns');
		
		$crud = new grocery_CRUD();
		$crud->set_table('coupon_campaigns');
		$crud->set_subject('Coupon Campaigns');
		
		$columns = ['name', 'start_date', 'end_date', 'Campaign_code'];
		$fields = ['name', 'start_date', 'end_date', 'Campaign_code'];
		
		if ('read' == $crud->getState()) {
			$columns = ['name', 'start_date', 'end_date', 'Campaign_code'];
		} elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
			$fields = ['name', 'start_date', 'end_date', 'Campaign_code'];
		}
		
		//$crud->display_as('campaign_id','Id Campaign');
		$crud->display_as('name', 'Name');
		$crud->display_as('start_date', 'Start Date');
		$crud->display_as('end_date', 'End Date');
		$crud->display_as('Campaign_code', 'Campaign Code');
		
		$crud->columns($columns);
		$crud->fields($fields);
		
		$crud->callback_add_field('Campaign_code', [$this, 'only_alphabet']);
		
		if (!is_granted('Admin')) {
			$crud->unset_add();
			$crud->unset_edit();
			$crud->unset_delete();
		}
		
		//$crud->required_fields('Id_Projet','Nom_Bloc');
		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}
	
	function only_alphabet()
	{
		return '<input type="text" name="Campaign_code" pattern="[a-zA-Z]{1,}">';
	}
}
