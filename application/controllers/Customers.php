<?php defined('BASEPATH') or exit('No direct script access allowed');


class Customers extends CI_Controller
{
	function __construct()
	{
		set_time_limit(0);
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');
		$this->load->library('grocery_CRUD');
		$this->load->helper('general_helper');

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

		$this->template->write('title', 'Customers', true);
		$this->template->write('header', 'Customers');

		$crud = new grocery_CRUD();
		$crud->set_table('customers');
		$crud->set_subject('Customer');

		$crud->columns('customerName', 'phone', 'addressLine1', 'addressLine2', 'city', 'postalCode', 'registration_date');
		$crud->fields('customerName', 'type', 'phone', 'email', 'password_customers', 'addressLine1', 'addressLine2', 'city', 'postalCode', 'balance');
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

		if ('update' == $crud->getState()) {
			$this->session->set_flashdata('edit_customer_id', $crud->getStateInfo()->primary_key);
		}

		$crud->callback_field('type', function ($value = 'Individual') {
			$checked = 'checked="checked"';
			$is_business = ($value == 'Business' ? $checked : "");
			$is_individual = ($value == 'Individual' || empty($value) ? $checked : "");
			return '
                <div class="pretty-radio-buttons">
                    <div class="radio"><label><input id="field-type-false" type="radio" name="type" ' . $is_individual . ' value="Individual">Individual</label></div>
                    <div class="radio"><label><input id="field-type-true" type="radio" name="type" ' . $is_business . '  value="Business">Business</label></div>
                </div>
            ';
		});

		$crud->callback_field('password_customers', [$this, 'set_password_input_to_empty']);
		$crud->callback_before_insert([$this, 'encrypt_password_before_insert_callback']);
		$crud->callback_before_update([$this, 'encrypt_password_callback']);

		$crud->callback_after_insert([$this, 'callback_after_change']);
		$crud->callback_after_update([$this, 'callback_after_change']);

		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}

	function encrypt_password_before_insert_callback($post_array)
	{
		//add pwd
		$post_array['password_customers'] = pwd_hash(trim(strip_tags($post_array['password_customers'])), 'md5', 2);

		$this->callback_before_insert($post_array);

		return $post_array;
	}

	function set_password_input_to_empty($value = '', $primary_key = null)
	{
		return '<input id="field-password" class="form-control" name="password_customers" type="password" value="" style="width: 100%;">';
	}

	function encrypt_password_callback($post_array)
	{
		//Encrypt password only if is not empty. Else don't change the password to an empty field
		$this->db->where('customerNumber', $this->session->flashdata('edit_customer_id'));
		$user = $this->db->get('customers')->row();

		if ($user) {
			if (!empty($post_array['password_customers']) && pwd_hash(trim(strip_tags($post_array['password_customers'])), 'md5', 2) != $customers->password_customers) {
				$post_array['password_customers'] = pwd_hash(trim(strip_tags($post_array['password_customers'])), 'md5', 2);
			} else {
				$post_array['password_customers'] = $customers->password_customers;
			}
		}

		$this->callback_before_update($post_array, null);

		return $post_array;
	}

	function callback_before_update($post_array, $primary_key)
	{
		$this->callback_before_change($post_array, $primary_key);
	}

	function callback_before_change($post_array, $primary_key)
	{
//		var_export($primary_key);
//		var_export($post_array);
//		die();
//		$this->session->set_userdata('customer.id', $primary_key);
//		$_SESSION['old_balance'] = $post_array['balance'];
//		$this->load->model('Balance');
//		$result = $this->Balance->getRows();

		$balance = 0;
		if ($primary_key != null) {
			$result = $this->db->query("SELECT * FROM balance_history WHERE customer_id = $primary_key ORDER BY date_operation DESC;")->row_array();

			if (is_array($result)) {
				$balance = $result['new_balance'];
			}
		}

		$_SESSION['old_balance'] = $balance;
	}

	function generate_report()
	{
		$data = [];
		$settings = $this->db->get('settings')->result();
		foreach ($settings as $setting) {
			$data[$setting->key] = $setting->value;
		}

		$data = array_merge($data, [
			'delivery_from' => (new DateTime())->modify("first day of last month")->format('Y-m-d'),
			'delivery_to'   => (new DateTime())->modify("last day of last month")->format('Y-m-d'),

		]);

		// load the library Html2pdf
		$this->load->library('Html2pdf');
		//Set folder to save PDF to
		$this->html2pdf->folder('./assets/pdfs/');
		//Set the paper defaults
		$this->html2pdf->paper('a4', 'portrait');

		// load the library email
		//$this->load->library('email');

		$query = $this->db->query('
            SELECT DISTINCT o.order_customer
            FROM orders o JOIN customers c ON o.order_customer = c.customerNumber 
            WHERE c.type = "Business" AND o.status = "Delivered"
            AND DATE(date_added) BETWEEN "' . $data['delivery_from'] . '" AND "' . $data['delivery_to'] . '";
	    ');

		$result = $query->result();
		foreach ($result as $row) {
			$query = $this->db->query('
                SELECT *, DATE(IFNULL(date_delivered, date_added)) AS date_Delivered
                FROM orders
                JOIN customers ON orders.order_customer = customers.customerNumber
                WHERE orders.order_customer = ' . $row->order_customer . '
                AND orders.status = "Delivered" AND customers.type = "Business"
                AND date_added BETWEEN "' . $data['delivery_from'] . '" AND "' . $data['delivery_to'] . '";
            ');
			$orders = $query->result();

			// Clearing previous orders
			unset($data['orders']);

			$amount = 0;
			foreach ($orders as $order) {
				$data['orders'][] = [
					'id'             => $order->id_order,
					'date_delivered' => (new DateTime($order->date_Delivered))->format('d/m/Y'),
					'for'            => $order->order_for,
					'items_cost'     => $order->order_items_cost,
					'delivery_cost'  => $order->order_delivery_cost,
				];

				$amount += $order->order_items_cost + $order->order_delivery_cost;
			}

			//$this->email->clear(true);

			if (count($orders)) {
				$data['customer_name'] = $orders[0]->customerName;
				$data['customer_address1'] = $orders[0]->addressLine1;
				$data['customer_address2'] = $orders[0]->addressLine2;
				$data['customer_phone'] = $orders[0]->phone;
				$data['customer_city'] = $orders[0]->city;
				$data['customer_postalCode'] = $orders[0]->postalCode;

				//Set the filename to save/download as
				$filename = 'invoice-' . (new DateTime())->modify("first day of last month")->format("m_Y") . '-' . $data['customer_name'] . '.pdf';
				$this->html2pdf->filename($filename);
				//Load html view
				$this->html2pdf->html($this->load->view('pdf/invoice', $data, true));
				if ($path = $this->html2pdf->create('save')) {
					//$this->email->attach($path);
				}

				//Set the filename to save/download as
				$tvaInvoiceFileName = 'tva-invoice-' . (new DateTime())->modify("first day of last month")->format("m_Y") . '-' . $data['customer_name'] . '.pdf';
				$this->html2pdf->filename($tvaInvoiceFileName);
				//Load html view
				$this->html2pdf->html($this->load->view('pdf/invoice_tva', $data, true));
				if ($path = $this->html2pdf->create('save')) {
					//$this->email->attach($path);
				}

				//$this->email->from('reports@' . $_SERVER['HTTP_HOST'], $data['app_name']);
				//$this->email->to($data['app_ReportsEmail']);
				//$this->email->subject("Monthly Delivery Report " . (new DateTime())->modify("first day of last month")->format("m/Y") . " - " . $data['customer_name']);
				//$this->email->message('You have the Monthly Delivery Report of this last month from ' . $data['app_name'] . '.');

//                if ($this->email->send()) {
				$query = '
                        INSERT INTO `invoices` (`invoice_number`, `invoice_client`, `invoice_amount`, `invoice_date`, `invoice_status`, `invoice_file_link`, `tva_invoice_file_link`) VALUES
                        (' . $data['app_InvoiceCounter'] . ', ' . $row->order_customer . ', ' . $amount . ', "' . (new DateTime())->format('Y-m-d') . '", "New", "' . $filename . '", "' . $tvaInvoiceFileName . '");
                    ';
				$this->db->query($query);
				$data['app_ReportCounter']++;
				$data['app_InvoiceCounter']++;
//                } else {
//                    die('error: mail not sent');
//                }
			}
		}

		$this->db->set('value', $data['app_ReportCounter'], false);
		$this->db->where('key', 'app_ReportCounter');
		$this->db->update('settings');

		$this->db->set('value', $data['app_InvoiceCounter'], false);
		$this->db->where('key', 'app_InvoiceCounter');
		$this->db->update('settings');

		die('Generation Finished.');
	}

	function callback_before_insert($post_array)
	{
		$this->callback_before_change($post_array, null);
	}

	function callback_after_change($post_array, $primary_key)
	{
		if (empty($post_array['email'])) {

			$variable = 'customer' . $primary_key . '@domain.com';
			$this->db->set('email', $variable);
			$this->db->where('customerNumber', $primary_key);
			$this->db->update('customers');
		}

		//
		$old_balance = $_SESSION['old_balance'];
		unset($_SESSION['old_balance']);

		$new_balance = $post_array['balance'];

		if ($old_balance != $new_balance) {
			$this->watch_balance($primary_key, $old_balance, $new_balance);
		}
	}

	function watch_balance($id_customer, $old_balance, $new_balance)
	{
		$data = [
			'customer_id' => $id_customer,
			'difference'  => $new_balance - $old_balance,
			'new_balance' => $new_balance,
		];

		$this->load->model('BalanceHistory');
		//insert user data to customers table
		$inserted = $this->BalanceHistory->insert($data);

		return $inserted;
	}
}
