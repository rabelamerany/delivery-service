<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Orders extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('general_helper');
		if (!is_user_logged_in()) {
			redirect('login');
		}
		
		if (!(is_granted('Admin') || is_granted('Dispatcher') || is_granted('Controller') || is_granted('Driver'))) {
			redirect('errors/error403');
		}
		
		$this->load->database();
		$this->load->library('grocery_CRUD');
		$this->load->library('form_validation');
		
		$this->template->write_view('sidenavs', 'template/default_sidenavs.php', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}
	
	function index()
	{
		$this->template->write('title', 'Orders', true);
		$this->template->write('header', 'Orders');
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
		
		$crud = new grocery_CRUD();
		//$crud->set_theme('datatables');
		$crud->set_table('orders');
		$crud->set_subject('Order');
		
		if (is_granted('Controller') && ('list' == $crud->getState())) {
			$crud->unset_add();
			$crud->unset_edit();
			$crud->unset_delete();
		}
		
		$columns = ['id_order', 'date_added', 'status', 'order_customer', 'order_driver_assigned'];
		$fields = ['order_source', 'order_customer', 'order_driver_assigned', 'date_added', 'order_from', 'order_for', 'order_address1', 'order_address2', 'order_zipcode', 'order_phone', 'order_city', 'description', 'order_instructions', 'order_items_cost', 'order_delivery_cost', 'driver_delivery_cost', 'order_type', 'coupon_code'];
		
		if ('read' == $crud->getState()) {
			$columns = ['order_customer', 'order_driver_assigned', 'order_from', 'order_for', 'order_phone', 'order_items_cost', 'status', 'description', 'order_instructions', 'order_address1', 'order_address2', 'order_city', 'order_zipcode', 'date_added', 'date_pickedup', 'date_delivered', 'order_source', 'order_type', 'order_delivery_cost', 'driver_delivery_cost', 'user_id'];
		} elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
			$fields = ['status', 'description', 'order_items_cost', 'date_pickedup', 'date_delivered', 'order_driver_assigned', 'order_instructions', 'order_phone', 'order_from', 'order_for', 'order_address1', 'order_address2', 'order_city', 'order_zipcode', 'order_source', 'order_type', 'order_delivery_cost', 'driver_delivery_cost', 'order_customer'];
		}
		
		if (is_granted('Driver')) {
			$columns = ['id_order', 'order_from', 'order_for', 'description', 'date_added'];
			$crud->unset_operations();
			$CI =& get_instance();
			$crud->where('driverNumber', $CI->session->userdata()['user_id']);
		}
		
		$crud->columns($columns);
		$crud->fields($fields);
		$crud->required_fields('order_customer', 'order_for');
		$crud->set_relation('user_id', 'users', 'username');
		$crud->set_relation('order_customer', 'customers', 'customerName');
		$crud->set_relation('order_driver_assigned', 'drivers', '{driverFirstName} {driverLastName}');
		$crud->display_as('order_customer', 'Customer')
			->display_as('id_order', 'Order #')
			->display_as('order_driver_assigned', 'Driver')
			->display_as('order_from', 'From')
			->display_as('order_for', 'To Recipient')
			->display_as('order_phone', 'To Phone')
			->display_as('order_items_cost', 'Merchandise Cost')
			->display_as('status', 'Status')
			->display_as('description', 'Description')
			->display_as('order_instructions', 'Special Instructions')
			->display_as('order_address1', 'To Address 1')
			->display_as('order_address2', 'To Address 2')
			->display_as('order_city', 'To City')
			->display_as('order_zipcode', 'To Zip Code')
			->display_as('date_added', 'Date Added')
			->display_as('date_pickedup', 'Date Pickedup')
			->display_as('date_delivered', 'Date Delivered')
			->display_as('order_delivery_cost', 'Client Delivery Cost')
			->display_as('driver_delivery_cost', 'Driver Delivery Cost')
			->display_as('order_type', 'Type')
			->display_as('order_source', 'Source')
			->display_as('user_id', 'Username')
			->display_as('coupon_code', 'Coupon Code');
		
		$crud->order_by('id_order', 'desc');
		
		//$crud->callback_after_insert([$this, 'addUser']);
		$crud->callback_after_update([$this, 'generate_invoice']);
		
		$crud->callback_before_update([$this, 'update_balance_before_update']);
		$crud->callback_after_insert([$this, 'update_balance_after_insert']);
		//$crud->callback_after_insert([$this, 'callback_change_coupon_status_and_set_receiver']);
		$crud->callback_after_update([$this, 'update_balance_after_update']);
		
		$crud->set_rules('coupon_code', 'coupon code', 'callback_check_coupon');
		
		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}
	
	public function check_coupon($str)
	{
		if ($str) {
			$user_id = $_POST['order_customer'];
			$var = $_POST['date_added'];
			$date = str_replace('/', '-', $var);
			$date1 = date('Y-m-d H:i:s', strtotime($date));
			$coupon = $this->db->from('coupons')
				->join('coupon_campaigns', 'coupon_campaigns.campaign_id = coupons.campaign')
				->where('coupons.coupon_code', $str)
				->where('coupon_campaigns.start_date <=', $date1) // 2019-09-11 00:00:00
				->where('coupon_campaigns.end_date >=', $date1)
				//->where('coupons.giver <> coupons.receiver')
				/*->group_start()
				->or_where('coupons.status', 'Ready')
				->where('coupons.receiver <> ' , $user_id)
				//->where('coupons.receiver = ' . $user_id)
				->group_end()
				->or_group_start()
				->or_where('coupons.status', 'Used by Receiver')
				->where('coupons.giver = ' . $user_id)
				->group_end()*/
				->get()->result_array();
			
		if ($coupon == null) {
				
			$this->form_validation->set_message(__FUNCTION__, "Invalid coupon code used.");
			return false;
		}
		elseif('Used by Both' == $coupon[0]['status']) {

			$this->form_validation->set_message(__FUNCTION__, "Coupon already has a used by Both in status.");
			return false;
		}
		elseif ('Ready' == $coupon[0]['status'] && $coupon[0]['giver'] ==  $user_id) {

			$this->form_validation->set_message(__FUNCTION__, "the giver do not use the coupons yet.");
			return false;			
		}
		elseif ('Used by Receiver' == $coupon[0]['status'] && $coupon[0]['giver'] !=  $user_id) {

			$this->form_validation->set_message(__FUNCTION__, "another receiver use the coupons .");
			return false;			
		}
		}
		return true;
	}/*
		function callback_change_coupon_status_and_set_receiver($post_array, $primary_key)
		{			
			if (isset($post_array['coupon_code']) && !empty($post_array['coupon_code'])) {
				$coupon = $this->db->from('coupons')
					->where('coupon_code', $_POST['coupon_code'])
					->get()->row_array();
			
				if ('Ready' == $coupon['status']) {
			
					$this->db->set('status', 'Used by Receiver');
					$this->db->set('receiver', $_POST['order_customer']);
					$this->db->where('coupon_code', $_POST['coupon_code']);
					$this->db->update('coupons');
				}
				elseif ('Used by Receiver' == $coupon['status']) {

					$this->db->set('status', 'Used by Both');
					$this->db->where('coupon_code', $_POST['coupon_code']);
					$this->db->update('coupons');
				}
		}
	}*/
	
	function update_balance_after_insert($post_array, $primary_key)
	{
		// add new balance to balance history table
		$this->db->where('customerNumber', $_POST['order_customer']);
		$customer = $this->db->get('customers')->row();
		
		$data = [
			'customer_id' => $customer->customerNumber,
			'difference'  => $post_array['order_delivery_cost'] * -1,
			'new_balance' => $customer->balance - $post_array['order_delivery_cost'],
		];
		
		$this->load->model('BalanceHistory');
		$inserted = $this->BalanceHistory->insert($data);
		
		// update customer balance
		$data = [
			'balance' => $customer->balance - $post_array['order_delivery_cost'],
		];
		$this->db->where('customerNumber', $_POST['order_customer']);
		$this->db->update('customers', $data);

		// add user_id
		//$this->db->set('user_id', $this->session->userdata('user_id'), false);
		$userdata = $this->session->userdata();				
		$this->db->set('user_id', $userdata['user_id']);
		$this->db->where('id_order', $primary_key);
		$this->db->update('orders');

		//callback_change_coupon_status_and_set_receiver
		if (isset($post_array['coupon_code']) && !empty($post_array['coupon_code'])) {
				$coupon = $this->db->from('coupons')
					->where('coupon_code', $_POST['coupon_code'])
					->get()->row_array();
			
				if ('Ready' == $coupon['status']) {
			
					$this->db->set('status', 'Used by Receiver');
					$this->db->set('receiver', $_POST['order_customer']);
					$this->db->where('coupon_code', $_POST['coupon_code']);
					$this->db->update('coupons');
				}
				elseif ('Used by Receiver' == $coupon['status']) {

					$this->db->set('status', 'Used by Both');
					$this->db->where('coupon_code', $_POST['coupon_code']);
					$this->db->update('coupons');
				}
		}
	}
	
	function update_balance_before_update($post_array, $primary_key)
	{
		$this->db->where('id_order', $primary_key);
		$order = $this->db->get('orders')->row();
		
		$_SESSION['status'] = $order->status;
	}
	
	function update_balance_after_update($post_array, $primary_key)
	{
		$status = $_SESSION['status'];
		unset($_SESSION['status']);
		
		if ($post_array['status'] == 'Canceled' && $status != 'Canceled') {
			// add new balance to balance history table
			$this->db->where('customerNumber', $_POST['order_customer']);
			$customer = $this->db->get('customers')->row();
			
			$data = [
				'customer_id' => $customer->customerNumber,
				'difference'  => $post_array['order_delivery_cost'],
				'new_balance' => $customer->balance + $post_array['order_delivery_cost'],
			];
			
			$this->load->model('BalanceHistory');
			$inserted = $this->BalanceHistory->insert($data);
			
			// update customer balance
			$data = [
				'balance' => $customer->balance + $post_array['order_delivery_cost'],
			];
			$this->db->where('customerNumber', $_POST['order_customer']);
			$this->db->update('customers', $data);
		}
	}
	
	function customerAddress()
	{
		if (isset($_POST['customer']) && !empty($_POST['customer'])) {
			$this->db->where('customerNumber', $_POST['customer']);
			$customer = $this->db->get('customers')->row();
			
			header('Content-Type: application/json');
			echo json_encode([
				'address1'   => $customer->addressLine1,
				'address2'   => $customer->addressLine2,
				'phone'      => $customer->phone,
				'city'       => $customer->city,
				'postalCode' => $customer->postalCode,
			]);
		}
	}
	/*
	function addUser($post_array, $primary_key)
	{
		$this->db->set('user_id', $this->session->userdata('user_id'), false);
		$this->db->where('id_order', $primary_key);
		$this->db->update('orders');
	}
		*/
	function generate_invoice($post_array, $primary_key)
	{
//		if ('Delivered' == $post_array['status']) {
//			$settings = $this->db->get('settings')->result();
//			foreach ($settings as $setting) {
//				$data[$setting->key] = $setting->value;
//			}
//
//			// load the library Html2pdf
//			$this->load->library('Html2pdf');
//			//Set folder to save PDF to
//			$this->html2pdf->folder('./assets/pdfs/');
//			//Set the filename to save/download as
//			$this->html2pdf->filename('invoice-' . $data['app_InvoiceCounter'] . '.pdf');
//			//Set the paper defaults
//			$this->html2pdf->paper('a4', 'portrait');
//
//			$data = array_merge($data, [
//				'order_id' => $primary_key,
//				'order_description' => $post_array['description'],
//				'order_date_delivered' => DateTime::createFromFormat('d/m/Y H:i:s', $post_array['date_delivered'])->format('d/m/Y'),
//				'order_items_cost' => $post_array['order_items_cost'],
//				'order_delivery_cost' => $post_array['order_delivery_cost'],
//			]);
//
//			$this->db->where('customerNumber', $post_array['order_customer']);
//			$customer = $this->db->get('customers')->row();
//
//			$data = array_merge($data, [
//				'customer_name' => $customer->customerName,
//				'customer_address1' => $customer->addressLine1,
//				'customer_address2' => $customer->addressLine2,
//				'customer_phone' => $customer->phone,
//				'customer_city' => $customer->city,
//				'customer_postalCode' => $customer->postalCode,
//			]);
//
//			//Load html view
//			$this->html2pdf->html($this->load->view('pdf/invoice', $data, true));
//
//			if ($path = $this->html2pdf->create('save')) {
//				$userdata = $this->session->userdata();
//				$this->db->where('id_order', $primary_key);
//				$order = $this->db->get('orders')->row();
//				$this->db->where('user_id', $order->user_id);
//				$user = $this->db->get('users')->row();
//
//				// load the library Html2pdf
//				$this->load->library('email');
//				$this->email->from('invoices@' . $_SERVER['SERVER_NAME'], $data['app_name']);
//				$this->email->to($userdata['email']);
//				$this->email->cc($user->email);
//				$this->email->subject("Facture " . str_pad($data['app_InvoiceCounter'], 3, "0", STR_PAD_LEFT) . "/" . (new DateTime())->format("Y") . " - " . $data['customer_name']);
//				$this->email->message('You have an invoice from ' . $data['app_name'] . '.');
//				$this->email->attach($path);
//				if ($this->email->send()) {
//					$this->db->set('value', ($data['app_InvoiceCounter'] + 1), FALSE);
//					$this->db->where('key', 'app_InvoiceCounter');
//					$this->db->update('settings');
//				} else {
//					die('error: mail not sent');
//				}
//			}
//		}
	}
}
