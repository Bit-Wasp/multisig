<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 * or	http://example.com/index.php/welcome/index
	 * or	http://example.com/ as this controller is the default.
	 */
	public function index($address = NULL)
	{
		$this->load->view('welcome');
	}
	
};
