<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Productcustomers extends CI_Controller
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

        $this->template->write('title', 'Products', TRUE);
        $this->template->write('header', 'Products');

        $crud = new grocery_CRUD();
        $crud->set_table('products');
        $crud->set_subject('Product');

        $columns = ['ProductId','Name'];
        $fields = ['ProductId','Name'];

        if ('read' == $crud->getState()) {
            $columns = ['ProductId','Name'];
        } elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
            $fields = ['ProductId','Name'];
        }
        $crud->where(array('customerNumber' => $userdata['customerNumber']));

        $crud->display_as("ProductId","Product Id");
        $crud->display_as("Name","Name");   

        $crud->columns($columns);
        $crud->fields($fields);

        $crud->required_fields('Name');
        $crud->callback_after_insert([$this, 'addUser']);

        $this->template->write_view('content', 'example', $crud->render());
        $this->template->render();
    }
    function addUser($post_array, $primary_key)
    {
        $userdata = $this->session->userdata();
        $variable = $userdata['customerNumber'];                        

        $this->db->set('customerNumber', $variable);
        $this->db->where('ProductId', $primary_key);
        $this->db->update('products');
    }
}
