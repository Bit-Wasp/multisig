<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Broadcast extends CI_Controller {

	public function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('bitcoin');			// Load the bitcoin api functions
		$this->load->library('transaction');		// Load a transaction crafting library
	}

	public function index() 
	{
		$this->load->library('bitcoin');
		$input_transaction_id = json_decode($this->session->userdata('txid'));
		$input = $this->input->post('input');
		
		$get_ID = $this->bitcoin->id_from_input($input);
		
		$data['found'] = FALSE;
		if($get_ID !== FALSE) 
		{
			$transaction = $this->bitcoin->decoderawtransaction($this->bitcoin->getrawtransaction($get_ID));
			// Need to check each transaction which paid into the address
			foreach($transaction['vin'] as $vin) 
			{
				if($vin['txid'] == $input_transaction_id)
					$data['found'] = $get_ID;
			}
		}
		
		$this->load->view('verify', $data);
	}
};

/* End of file broadcast.php */
/* Location: ./application/controllers/broadcast.php */
