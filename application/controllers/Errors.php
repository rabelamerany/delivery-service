<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Errors extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->template->write_view('sidenavs', 'template/default_sidenavs', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}
	
	function index()
	{
		// $this->template->set_template('Error 500');
		$this->template->write('title', 'Erreur 500: Erreur serveur', true);
		$this->template->write('content', 'Erreur 500: Erreur serveur', true);
		$this->template->render();
	}
	
	function Error403()
	{
		$this->template->write('title', 'Erreur 403: Accès refusé', true);
		$this->template->write('content', 'Erreur 403: Accès refusé', true);
		$this->template->render();
	}
}
