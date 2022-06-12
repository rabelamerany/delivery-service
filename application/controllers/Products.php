<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

    function __construct()
    {
        set_time_limit(0);
        parent::__construct();
        $this->load->helper('general_helper');
        
        if (!is_user_logged_in()) {
            redirect('login');
        }
        
        if (is_granted('Controller')) {
            redirect('errors/error403');
        }
        
        $this->load->database();
        $this->load->library('grocery_CRUD');

        $this->template->write_view('sidenavs', 'template/default_sidenavs', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index() {

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
       
        $crud->display_as("ProductId","Product Id");
		$crud->display_as("Name","Name");        

        $crud->columns($columns);
        $crud->fields($fields);

        $crud->required_fields('Name');
        $this->template->write_view('content', 'example', $crud->render());
        $this->template->render();
    }
}
