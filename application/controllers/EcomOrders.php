<?php defined('BASEPATH') OR exit('No direct script access allowed');


class EcomOrders extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('general_helper');
		$this->load->database();
		$this->load->library('grocery_CRUD');
		$this->load->library('form_validation');

		if (is_granted('Controller') || is_granted('Driver')) {
			redirect('errors/error403');
		}
		
			$this->template->write_view('sidenavs', 'template/default_sidenavs.php', true);
			$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}
	
	function index()
	{
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

		$crud = new grocery_CRUD();
		//$crud->set_theme('datatables');
		$crud->set_table('ecom_orders');
		$crud->set_subject('Ecom Orders');


		if (is_granted('Controller') && ('list' == $crud->getState())) {
			$crud->unset_add();
			$crud->unset_edit();
			$crud->unset_delete();
		}
		
		$columns = ['OrderNo', 'ProductId','date_added', 'status', 'order_customer', 'order_driver_assigned'];
		$fields = ['order_source','ProductId','order_customer','order_driver_assigned', 'date_added', 'order_from', 'order_for', 'order_address1', 'order_address2', 'order_zipcode', 'order_phone', 'order_city', 'description', 'order_instructions', 'order_items_cost', 'order_delivery_cost', 'driver_delivery_cost', 'order_type'];

		if ('read' == $crud->getState()) {
			$columns = ['order_customer', 'ProductId','order_driver_assigned', 'order_from', 'order_for', 'order_phone', 'order_items_cost', 'status', 'description', 'order_instructions', 'order_address1', 'order_address2', 'order_city', 'order_zipcode', 'date_added', 'date_pickedup', 'date_delivered', 'order_source', 'order_type', 'order_delivery_cost', 'driver_delivery_cost', 'user_id','include_invoice'];
		} elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
			$fields = ['ProductId','status', 'description', 'order_items_cost', 'date_added', 'date_pickedup', 'date_delivered', 'order_driver_assigned', 'order_instructions', 'order_phone', 'order_from', 'order_for', 'order_address1', 'order_address2', 'order_city', 'order_zipcode', 'order_source', 'order_type', 'order_delivery_cost', 'driver_delivery_cost', 'order_customer','include_invoice'];
		}
		
		$crud->columns($columns);
		$crud->fields($fields);
		$crud->required_fields('order_customer', 'order_for');
		$crud->field_type('include_invoice', 'true_false');
		$crud->set_relation('user_id', 'users', 'username');
		$crud->set_relation('ProductId', 'products', 'ProductId');
		$crud->set_relation('order_customer', 'customers', 'customerName');
		$crud->set_relation('order_driver_assigned', 'drivers', '{driverFirstName} {driverLastName}');
		$crud->display_as('order_customer', 'Customer')
			->display_as('OrderNo', 'OrderNo')
			->display_as('ProductId', 'Product')
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
			->display_as('include_invoice', 'Include in Invoice ')
			->display_as('user_id', 'Username');
		
			$crud->order_by('OrderNo', 'desc');
			$crud->unset_add();

		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
		
	}
	function generate_invoice_ecom()
    {
			$data = [];
			$settings = $this->db->get('settings')->result();
			foreach ($settings as $setting) {
				$data[$setting->key] = $setting->value;
			}
		
			$data = array_merge($data, [
				'delivery_from' => (new DateTime())->modify("first day of last month")->format('Y-m-d'),
				'delivery_to'   => (new DateTime())->modify("last day of last month")->format('Y-m-d')
			]);
		
			// load the library Html2pdf
			$this->load->library('Html2pdf');
			//Set folder to save PDF to
			$this->html2pdf->folder('./assets/pdfs/');
			//Set the paper defaults
			$this->html2pdf->paper('a4', 'portrait');
		
			// load the library email
			$this->load->library('email');
		
			$query = $this->db->query('
            	SELECT DISTINCT o.order_customer
            	FROM ecom_orders o JOIN customers c ON o.order_customer = c.customerNumber 
            	WHERE o.include_invoice = 1
	            AND DATE(date_added) BETWEEN "' . $data['delivery_from'] . '" AND "' . $data['delivery_to'] . '";
	    	');
			$result = $query->result();
			foreach ($result as $row) {
				$query = $this->db->query('
                	SELECT *, DATE(IFNULL(date_delivered, date_added)) AS date_Delivered
                	FROM ecom_orders
                	JOIN customers ON ecom_orders.order_customer = customers.customerNumber
                	WHERE ecom_orders.order_customer = ' . $row->order_customer . '
                	AND ecom_orders.include_invoice = 1
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
				
					$amount += $order->order_delivery_cost;
				}
			
				$this->email->clear(TRUE);
				
				if (count($orders)) {
					$data['customer_name'] = $orders[0]->customerName;
					$data['customer_address1'] = $orders[0]->addressLine1;
					$data['customer_address2'] = $orders[0]->addressLine2;
					$data['customer_phone'] = $orders[0]->phone;
					$data['customer_city'] = $orders[0]->city;
					$data['customer_postalCode'] = $orders[0]->postalCode;
				
					//Set the filename to save/download as
					$filename = 'invoice_ecom-' . (new DateTime())->modify("first day of last month")->format("m_Y") . '-' . $data['customer_name'] . '.pdf';
					$this->html2pdf->filename($filename);
					//Load html view
					$this->html2pdf->html($this->load->view('pdf/invoice_Ecom', $data, true));
					if ($path = $this->html2pdf->create('save')) {
						$this->email->attach($path);
					}
				
					//Set the filename to save/download as
					$tvaInvoiceFileName = 'tva-invoice_ecom-' . (new DateTime())->modify("first day of last month")->format("m_Y") . '-' . $data['customer_name'] . '.pdf';
					$this->html2pdf->filename($tvaInvoiceFileName);
					//Load html view
					$this->html2pdf->html($this->load->view('pdf/invoice_tvaEcom', $data, true));
					if ($path = $this->html2pdf->create('save')) {
						$this->email->attach($path);
					}
				
					$this->email->from('reports@' . $_SERVER['HTTP_HOST'], $data['app_name']);
					$this->email->to($data['app_ReportsEmail']);
					$this->email->subject("Monthly Delivery Report " . (new DateTime())->modify("first day of last month")->format("m/Y") . " - " . $data['customer_name']);
					$this->email->message('You have the Monthly Delivery Report of this last month from ' . $data['app_name'] . '.');

//                if ($this->email->send()) {
					$query = '
                    	    INSERT INTO `invoices_ecom` (`invoice_ecom_number`, `invoice_ecom`, `invoice_ecom_amount`, `invoice_ecom_date`, `invoice_ecom_status`, `invoice_ecom_file_link`, `tva_invoice_ecom_file_link`) VALUES
                        	(' . $data['app_InvoiceCounter'] . ', ' . $row->order_customer . ', ' . $amount . ', "' . (new DateTime())->format('Y-m-d') . '", "New", "' . $filename . '", "' . $tvaInvoiceFileName . '");
                    	';
					$this->db->query($query);
					$data['app_ReportCounter']++;
					$data['app_InvoiceCounter']++;
//                	} else {
//                    die('error: mail not sent');
//                	}
				}
			}
		
			$this->db->set('value', $data['app_ReportCounter'], FALSE);
			$this->db->where('key', 'app_ReportCounter');
			$this->db->update('settings');
		
			$this->db->set('value', $data['app_InvoiceCounter'], FALSE);
			$this->db->where('key', 'app_InvoiceCounter');
			$this->db->update('settings');
		
			die('Generation Finished.');
    	}
}
