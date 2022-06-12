<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller
 */
class DashboardCustomers extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper('general_helper');

		if (!is_user_logged_in()) {
			redirect('login');
		}

		$this->load->database();
		$this->load->helper('url');
		$this->load->library('grocery_CRUD');

		$this->template->write_view('sidenavs', 'tes/customer_sidenavs', true);
		$this->template->write_view('navs', 'tes/customer_topnavs.php', true);
	}

	function index()
	{
		$this->template->write('title', 'Dashboard', true);
		$this->template->write('header', 'Dashboard');
		$this->template->write_view('content', 'tes/dashboard_customers', '', true);


		$this->template->write('style', "");
		$this->template->write('javascript', "
            $('[data-toggle=\"tooltip\"]').tooltip(); 
            dashboard(moment().subtract(15, 'days'), moment(), '/DashboardCustomers/orderActivities');
            $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
                dashboard(picker.startDate, picker.endDate, '/DashboardCustomers/orderActivities');
            });
		");

		$this->template->render();
	}

	function orderActivities()
	{
		if ((isset($_POST['startDate']) && !empty($_POST['startDate'])) && (isset($_POST['endDate']) && !empty($_POST['endDate']))) {
			$query = $this->db->query("
				SELECT DATE_FORMAT(date_added, '%Y-%m-%d') AS order_date, COUNT(*) AS order_count
				FROM ecom_orders
				WHERE date_added BETWEEN '" . $_POST['startDate'] . " 00:00:00' AND '" . $_POST['endDate'] . " 23:59:59'
				AND order_customer = {$this->session->userdata('customerNumber')}
				GROUP BY DATE_FORMAT(date_added, '%Y-%m-%d')
			");

			$data   = [];
			$result = [];

			$begin    = new DateTime($_POST['startDate']);
			$end      = (new DateTime($_POST['endDate']))->modify('+1 day');
			$interval = DateInterval::createFromDateString('1 day');
			$period   = new DatePeriod($begin, $interval, $end);
			foreach ($period as $dt) {
				$result[$dt->getTimestamp()] = 0;
			}

			foreach ($query->result() as $row) {
				$result[strtotime($row->order_date)] = $row->order_count;
			}

			foreach ($result as $key => $value) {
				$data['chart'][] = [
					'day'   => $key * 1000,
					'count' => $value + 0,
				];
			}


			$data['products'] = [];
			$query            = $this->db->query("
				SELECT products.Name, COUNT(ecom_orders.ProductId) as product_count
				FROM `ecom_orders`
				JOIN products ON products.ProductId = ecom_orders.ProductId
				WHERE ecom_orders.date_added BETWEEN '" . $_POST['startDate'] . " 00:00:00' AND '" . $_POST['endDate'] . " 23:59:59'
				GROUP BY ecom_orders.ProductId
				ORDER BY product_count DESC
				LIMIT 3;
			");
			if (0 < count($query->result())) {
				$bestScore = $query->result()[0]->product_count;
				foreach ($query->result() as $row) {
					$data['products'][] = [
						'full_name' => $row->Name,
						'count'     => $row->product_count,
					];
				}
			}

			header('Content-Type: application/json');
			echo json_encode([
				'data' => $data,
			]);
		}
	}
}
