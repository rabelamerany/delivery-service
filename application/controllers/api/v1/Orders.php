<?php defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper('general_helper');
		$this->load->database();
	}

	function index()
	{
		header('Content-Type: application/json');

		$output = [
			'status'         => 'Error',
			'message_status' => [],
		];

		$http = 'Authorization';
		$http = strtoupper($http);
		$header = $_SERVER['HTTP_'.$http] ?? $_SERVER[$http] ?? null;

		if ($header === null) {
			$output['message_status'] = 'unauthorized user';
			echo json_encode($output);
			exit();
		}

		$authentification = str_replace('Basic ', '', $header);
		$authentification = base64_decode($authentification);
		$credentiations = explode(':', $authentification);

		$this->db->where('email', $credentiations[0]);
		$customer = $this->db->get('customers')->row();

		if ($customer === null) {
			$output['message_status'] = 'unauthorized user';
			echo json_encode($output);
			exit();
		}

		if (!pwd_verify($credentiations[1], $customer->password_customers)) {
			$output['message_status'] = 'unauthorized user';
			echo json_encode($output);
			exit();
		}

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$output['message_status'] = 'unsupported method';
			echo json_encode($output);
			exit();
		}

		$inputsValid = [];
		$inputsRequired = [
			'order_for',
			'order_from',
			'order_phone',
			'order_instructions',
			'order_address1',
			'order_address2',
			'order_city',
			'order_zipcode',
			'order_items_cost',
			'description',
		];

		$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		foreach ($inputsRequired as $input) {
			if (!isset($_POST[$input]) || empty($_POST[$input])) {
				$output['message_status'][$input] = $input.' required';
				continue;
			}

			$inputsValid[$input] = $_POST[$input];
		}

		if (is_array($output['message_status']) && 0 < count($output['message_status'])) {
			echo json_encode($output);
			exit();
		}

		$inputsValid['order_customer'] = $customer->customerNumber;
		$inputsValid['status'] = 'New';
		$inputsValid['order_source'] = 'API';
		$this->db->insert('orders', $inputsValid);
		$output['status'] = 'Ok';

		echo json_encode($output);
		exit();
	}
}
