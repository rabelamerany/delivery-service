<?php defined('BASEPATH') or exit('No direct script access allowed');

class Logincustomers extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');

	}

	function index()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$data['error'] = 'Wrong username or password';
		$query = $this->input->post('query');

		$query1 = $this->db->get_where('ecom_orders', ['status' => "New"])->num_rows();

		$newdata = [
			'query' => $query1,
		];

		$this->session->set_userdata($newdata);

		if ('POST' == $this->input->server('REQUEST_METHOD')) {

			if ((!empty($username)) and (!empty($password))) {

				$username_field = "email";
				$username_table = "customers";
				$this->db->where($username_field, $username);
				$customer = $this->db->get($username_table)->row();
				if ($customer) {
					// Email is okey lets check the password now
					$this->load->helper('general_helper');

					if (pwd_verify($password, $customer->password_customers)) {
						$customer_data = [
							'customerNumber' => $customer->customerNumber,
							'username'       => $customer->email,
							'password'       => $customer->password_customers,
							'email'          => $customer->email,
							'first_name'     => $customer->customerName,
							'logged_in'      => true,
						];
						$this->session->set_userdata($customer_data);
						redirect('ecomOrdersCustomers');
					} else {

						$this->load->view('tes/login_customers', $data);

						return;
					}
				} else {
					$this->load->view('tes/login_customers', $data);

					return;
				}
			} else {
				$this->load->view('tes/login_customers', $data);

				return;
			}
		}
		$this->load->view('tes/login_customers');

		return;
	}

	/*
	 * User logout
	 */
	public function logout()
	{
		$this->session->unset_userdata('customer_logged_in');
		$this->session->sess_destroy();
		redirect('logincustomers');
	}
}
