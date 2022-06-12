<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('user');
    }

    function index()
    {
        $this->template->set_template('login');
        $this->template->write('title', 'CRM Authentication', TRUE);
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $error = 'Wrong username or password';
        $query = $this->input->post('query');

        $query1=$this->db->get_where('ecom_orders', array('status' => "New"))->num_rows();

        $newdata = array(
                   'query'  => $query1
               );

        $this->session->set_userdata($newdata);

        if ('POST' == $this->input->server('REQUEST_METHOD')) {
            if ((!empty($username)) and (!empty($password))) {

                if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                    $username_field = "driver_email";
                    $username_table = "drivers";
                } else {
                    $username_field = "username";
                    $username_table = "users";
                }
                $this->db->where($username_field, $username);
                $user = $this->db->get($username_table)->row();

                if ($user) {
                    // Email is okey lets check the password now
                    $this->load->helper('general_helper');

                    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                        if ($password == $user->password) {
                            $user_data = [
                                'user_id' => $user->driverNumber,
                                'username' => $user->driver_email,
                                'password' => $user->password,
                                'first_name' => $user->driverFirstName,
                                'last_name' => $user->driverLastName,
                                'email' => $user->driver_email,
                                'role' => 'Driver',
                                'login_ip' => $_SERVER["REMOTE_ADDR"],
                                'logged_in' => TRUE
                            ];

                            $this->session->set_userdata($user_data);

                            redirect('orders');
                        } else {
                            $this->template->write('error', $error, TRUE);
                        }
                    } else {
                        if (pwd_verify($password, $user->password)) {
                            $user_data = [
                                'user_id' => $user->user_id,
                                'username' => $user->username,
                                'password' => $user->password,
                                'first_name' => $user->first_name,
                                'last_name' => $user->last_name,
                                'email' => $user->email,
                                'role' => $user->role,
                                'login_ip' => $_SERVER["REMOTE_ADDR"],
                                'logged_in' => TRUE
                            ];

                            $this->session->set_userdata($user_data);

                            $this->db->set('last_login_ip', '"' . $_SERVER["REMOTE_ADDR"] . '"', FALSE);
                            $this->db->where('user_id', $user->user_id);
                            $this->db->update('users');

                            if ('Controller' == $user->role) {
                                redirect('drivers/driver_cash_in');
                            } else {
                                redirect('dashboard');
                            }
                        } else {
                            $this->template->write('error', $error, TRUE);
                        }
                    }
                } else {
                    $this->template->write('error', $error, TRUE);
                }
            } else {
                $this->template->write('error', $error, TRUE);
            }
        }

        $this->template->render();
        //}else{
        //redirect('logincustomers.php');
    //}
    }

    /*
     * User logout
     */
    public function logout()
    {
        $this->session->unset_userdata('user_logged_in');
        $this->session->sess_destroy();
        redirect('login');
    }
}
