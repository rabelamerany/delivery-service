<?php defined('BASEPATH') OR exit('No direct script access allowed');


class EcomOrdersCustomers extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper('general_helper');
		$this->load->database();
		$this->load->library('grocery_CRUD');
		$this->load->library('form_validation');

		if (!is_customer_logged_in()) {
			redirect('logincustomers');
		}

		if (is_granted('Dispatcher') || is_granted('Controller') || is_granted('Driver')) {
			redirect('errors/error403');
		}

		$this->template->write_view('sidenavs', 'tes/customer_sidenavs.php', true);
		$this->template->write_view('navs', 'tes/customer_topnavs.php', true);
	}

	function index()
	{
		$userdata = $this->session->userdata();

		$this->template->write('title', 'Ecom Orders', true);
		$this->template->write('header', 'Ecom Orders');
		$this->template->write('javascript', "
			$('#field-order_customer').on('change', function(e) {
				var customer = $(this).val();
				$.ajax({
					url: 		'" . base_url() . "orders/customerAddress',
					method: 	'POST',
					data:		{customer: customer},
					success: 	function(response) {
         				$('#field-order_address1').val(response.address1);
         				$('#field-order_address2').val(response.address2);
         				$('#field-order_zipcode').val(response.postalCode);
         				$('#field-order_phone').val(response.phone);
         				$('#field-order_city').val(response.city);
      				}
				});
			});
		");

//		if (strpos($_SERVER['PHP_SELF'], 'ecomOrdersCustomers') === false) {
//			redirect('ecomOrdersCustomers');
//		}

		$crud = new grocery_CRUD();
		//$crud->set_theme('datatables');
		$crud->set_table('ecom_orders');
		$crud->set_subject('Ecom Orders');

		$columns = ['ProductId', 'OrderNo', 'order_for', 'order_phone', 'order_items_cost', 'description', 'order_address1', 'status', 'date_added'];
		$fields  = ['ProductId', 'OrderNo', 'description', 'order_items_cost', 'order_phone', 'order_for', 'order_address1', 'order_address2', 'order_instructions'];

		if ('read' == $crud->getState()) {
			$columns = ['ProductId', 'OrderNo', 'order_for', 'order_phone', 'order_items_cost', 'description', 'order_address1', 'status', 'date_added'];
		} elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
			$fields = ['ProductId', 'OrderNo', 'description', 'order_customer', 'order_items_cost', 'order_phone', 'order_for', 'order_address1', 'order_address2', 'order_instructions'];
		}

		$crud->columns($columns);
		$crud->fields($fields);
		$crud->set_relation('ProductId', 'products', 'Name', ['customerNumber' => $userdata['customerNumber']]);
		$crud->display_as('OrderNo', 'OrderNo')
			->display_as('ProductId', 'Product')
			->display_as('order_for', 'To Recipient')
			->display_as('date_added', 'Date')
			->display_as('order_phone', 'To Phone')
			->display_as('status', 'Status')
			->display_as('order_items_cost', 'Merchandise Cost')
			->display_as('description', 'Description')
			->display_as('order_address1', 'To Address 1')
			->display_as('order_instructions', 'Special Instructions')
			->display_as('order_address2', 'To Address 2');

		$crud->required_fields('order_for');
		$crud->unset_edit();
		$crud->unset_delete();
		$crud->where(['order_customer' => $userdata['customerNumber']]);
		$crud->callback_after_insert([$this, 'callback_after_change']);

		if ('read' == $crud->getState()) {
			$crud->unset_fields('user_id', 'date_pickedup', 'date_delivered', 'order_driver_assigned', 'order_instructions', 'order_from', 'order_city', 'order_zipcode', 'order_source', 'order_type', 'order_delivery_cost', 'driver_delivery_cost', 'order_customer', 'coupon_code');
		}

		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}

	function callback_after_change($post_array, $primary_key)
	{
		$userdata = $this->session->userdata();
		$v1       = $userdata['customerNumber'];

		$variable = $v1;
		$this->db->set('order_customer', $variable);
		$this->db->where('id_order', $primary_key);
		$this->db->update('ecom_orders');
	}

	function today()
	{
		$userdata = $this->session->userdata();

		$crud = new grocery_CRUD();

		$crud->set_table('ecom_orders');
		$crud->set_subject('Ecom Orders');
		$crud->where(['order_customer' => $userdata['customerNumber']]);
		$crud->where(['DATE(date_added)' => (new DateTime())->format('Y-m-d')]);
		$crud->columns(['ProductId', 'OrderNo', 'order_for', 'order_phone', 'order_items_cost', 'description', 'order_address1', 'status', 'date_added']);
		$crud->set_relation('ProductId', 'products', 'Name', ['customerNumber' => $userdata['customerNumber']]);
		$crud->display_as('OrderNo', 'OrderNo')
			->display_as('ProductId', 'Product')
			->display_as('order_for', 'To Recipient')
			->display_as('date_added', 'Date')
			->display_as('order_phone', 'To Phone')
			->display_as('status', 'Status')
			->display_as('order_items_cost', 'Merchandise Cost')
			->display_as('description', 'Description')
			->display_as('order_address1', 'To Address 1')
			->display_as('order_instructions', 'Special Instructions')
			->display_as('order_address2', 'To Address 2');

		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_delete();

		$this->template->write('title', 'Today\'s orders', true);
		$this->template->write('header', 'Today\'s orders');
		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}
}
