<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Settings extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->helper('general_helper');
        if (!is_user_logged_in())
        {
            redirect('login');
        }

        if (!is_granted('Admin'))
        {
            redirect('errors/error403');
        }

        $this->load->database();

        $this->template->write_view('sidenavs', 'template/default_sidenavs.php', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index()
    {
        $this->template->write('title', 'Settings', TRUE);
        $this->template->write('header', 'Settings');

        $data = [];
        foreach ($this->db->get('settings')->result() as $setting)
        {
            if ('POST' == $this->input->server('REQUEST_METHOD')) {
                $this->db->set('value', "'".$this->input->post($setting->key)."'", FALSE);
                $this->db->where('key', $setting->key);
                $this->db->update('settings');

                $data[$setting->key] = $this->input->post($setting->key);
            } else {
                $data[$setting->key] = $setting->value;
            }
        }

        $this->template->write('content' ,$this->load->view('template/settings', ['settings' => $data], true));
        $this->template->render();
    }
}