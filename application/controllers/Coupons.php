<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Coupons extends CI_Controller
{
	function __construct()
	{
		set_time_limit(0);
		parent::__construct();
		
		$this->load->helper('general_helper');
		if (!is_user_logged_in()) {
			redirect('login');
		}
		
		if (!is_granted('Admin') && !is_granted('Dispatcher')) {
			redirect('errors/error403');
		}
		
		$this->load->database();
		$this->load->helper('url');
		$this->load->library('grocery_CRUD');
		
		$this->template->write_view('sidenavs', 'template/default_sidenavs', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}
	
	function index()
	{
		$this->template->write('title', 'Coupons', true);
		$this->template->write('header', 'Coupons');
		
		$crud = new grocery_CRUD();
		$crud->set_table('coupons');
		$crud->set_subject('Coupons');
		
		$columns = ['giver', 'receiver', 'campaign', 'coupon_code', 'status'];
		$fields = ['giver', 'campaign'];
		
		if ('read' == $crud->getState()) {
			$columns = ['giver', 'receiver', 'campaign', 'coupon_code', 'status'];
		} elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
			$fields = ['giver', 'campaign'];
		}
		
		$crud->set_relation('giver', 'customers', 'customerName');
		$crud->set_relation('receiver', 'customers', 'customerName');
		$crud->set_relation('campaign', 'coupon_campaigns', 'name');
		$crud->display_as('coupon_code', 'Coupon Code');
		$crud->display_as('status', 'status');
		
		if (!is_granted('Admin')) {
			$crud->unset_edit();
			$crud->unset_delete();
		}
		
		if (!is_granted('Admin') && !is_granted('Dispatcher')) {
			$crud->unset_add();
		}
		
		$crud->callback_before_insert([$this, 'callback_add_status']);
		$crud->callback_after_insert([$this, 'callback_after_change']);
		
		$crud->columns($columns);
		$crud->fields($fields);
		
		//$crud->required_fields('Id_Projet','Nom_Bloc');
		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}
	
	function callback_after_change($post_array, $primary_key)
	{
		$v1 = $post_array['campaign'];
		
		$query = $this->db->get_where('coupon_campaigns', ['campaign_id' => $v1])->row_array();
		
		$variable = $query['Campaign_code'] . $_POST['giver'];
		$this->db->set('coupon_code', $variable);
		$this->db->where('coupon_id', $primary_key);
		$this->db->update('coupons');
	}
	
	function callback_add_status($post_array)
	{
		$post_array['status'] = 'Ready';
		
		return $post_array;
	}
}
