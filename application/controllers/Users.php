<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper('general_helper');
		if (!is_user_logged_in()) {
			redirect('login');
		}

		$this->load->database();
		$this->load->library('grocery_CRUD');
		$this->load->library('form_validation');

		$this->template->write_view('sidenavs', 'template/default_sidenavs', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}

	function index()
	{
		$this->template->write('title', 'Users', TRUE);
		$this->template->write('header', 'Users');

		$crud = new grocery_CRUD();
		$crud->set_table('users');
		$crud->set_subject('User');

		$fields = ['username', 'password', 'role', 'first_name', 'last_name', 'email', 'phone_number', 'phone_number2', 'address', 'city', 'zipcode'];

		if ('edit' == $crud->getState()) {
			if (($this->session->userdata()['user_id'] != $crud->getStateInfo()->primary_key) && (!(is_granted('Admin') || is_granted('Dispatcher')))) {
				redirect('errors/error403');
			}

			if (($this->session->userdata()['user_id'] == $crud->getStateInfo()->primary_key) && !is_granted('Admin')) {
				unset($fields[array_search('role', $fields)]);
			}
		} elseif (!(is_granted('Admin') || is_granted('Dispatcher'))) {
			redirect('errors/error403');
		}

		if ('list' == $crud->getState() && (is_granted('Dispatcher'))) {
			$crud->unset_operations();
		}

		$crud->columns('username', 'first_name', 'last_name', 'email', 'phone_number', 'address', 'city', 'zipcode', 'last_login', 'role');
		$crud->fields($fields);
		$crud->display_as('username', 'Username')
			->display_as('password', 'Password')
			->display_as('role', 'Role')
			->display_as('first_name', 'First Name')
			->display_as('last_name', 'Last Name')
			->display_as('email', 'Email')
			->display_as('phone_number', 'Phone Number')
			->display_as('phone_number2', 'Phone Number 2')
			->display_as('address', 'Address')
			->display_as('city', 'City')
			->display_as('zipcode', 'Zip code');

		$crud->field_type('role', 'dropdown', array_combine($this->config->item('ROLES'), $this->config->item('ROLES')));

		$required_fields = ['username', 'first_name', 'last_name', 'email', 'phone_number'];

		if ('update' == $crud->getState()) {
			$this->session->set_flashdata('edit_user_id', $crud->getStateInfo()->primary_key);
		} elseif ('add' == $crud->getState()) {
			$required_fields[] = ['password', 'role'];
			$crud->callback_field('role', [$this, 'set_default_role']);
		}

		$crud->required_fields($required_fields);

		$crud->callback_field('password', array($this, 'set_password_input_to_empty'));
		$crud->callback_before_insert(array($this, 'encrypt_password_before_insert_callback'));
		$crud->callback_before_update(array($this, 'encrypt_password_callback'));

		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}

	function encrypt_password_before_insert_callback($post_array)
	{
		$post_array['password'] = pwd_hash(trim(strip_tags($post_array['password'])), 'md5', 2);

		return $post_array;
	}

	function encrypt_password_callback($post_array)
	{
		$this->load->helper('general_helper');

		//Encrypt password only if is not empty. Else don't change the password to an empty field
		$this->db->where('user_id', $this->session->flashdata('edit_user_id'));
		$user = $this->db->get('users')->row();

		if ($user) {
			if (!empty($post_array['password']) && pwd_hash(trim(strip_tags($post_array['password'])), 'md5', 2) != $user->password) {
				$post_array['password'] = pwd_hash(trim(strip_tags($post_array['password'])), 'md5', 2);
			} else {
				$post_array['password'] = $user->password;
			}
		}

		return $post_array;
	}

	function set_password_input_to_empty($value = '', $primary_key = null)
	{
		return '<input id="field-password" class="form-control" name="password" type="password" value="" style="width: 100%;">';
	}

	function set_default_role($value = '', $primary_key = null)
	{
		return form_dropdown(
			'role',
			array_combine($this->config->item('ROLES'), $this->config->item('ROLES')),
			$this->config->item('DEFAULT_ROLE'),
			[
				'id' => 'field-role',
				'class' => 'chosen-select'
			]
		);
	}
}
