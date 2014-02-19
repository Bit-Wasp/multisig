<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Homepage 
	 */
	public function index($address = NULL)
	{
		$this->load->view('welcome');
	}

	/**
	 * Show information about a generated address, using $unique_id.
	 * This can be bookmarked so users can return later to view the details.
	 */
	public function id($unique_id = NULL){
		// Initial checks of unique_id
		if($unique_id == NULL)
			redirect('welcome');
		$this->load->model('bitcoin_model');
		$address = $this->bitcoin_model->watch_address_by_unique_id($unique_id);
		if($address == FALSE)
			redirect('welcome');
		if($address['complete'] == '1')
			redirect("welcome/complete/$unique_id");

		$this->load->library('bitcoin');
		$this->load->library('form_validation');
		// Set the minimum confirmations before the submit button is shown.
		$data['minimum_confirmations'] = 1;

		// Load the public key from POST
		$data['pubkeys'] = array();
		for($i = 1; $i <= $address['n']; $i++) {
			$pos = 'public_key'.$i;
			$data['pubkeys'][] = $address[$pos];
		}

		$server_info = $this->bitcoin->getinfo();
		$data['current_block'] = $server_info['blocks'];
		$data['payments'] = $this->bitcoin_model->payments_to_address($address['address']);

		// Store the address, the redeemScript, and the private key 
		$this->session->set_userdata('address', $address['address']);
		$this->session->set_userdata('redeemScript', $address['redeemScript']);
		$this->session->set_userdata('unique_id', $unique_id);
		$data['multisig'] = $address;
		$data['n'] = $address['n'];
		$this->load->view('displaymultisigaddress', $data);
	}

	/**
	 * Whenever a transaction is marked as complete, users on another
	 * page will be redirected here.
	 */
	public function complete($unique_id) {
		if($unique_id == NULL)
			redirect('welcome');		
		$this->load->model('bitcoin_model');
        $address = $this->bitcoin_model->watch_address_by_unique_id($unique_id);
        if($address == FALSE)
			redirect('welcome');
		if($address['complete'] !== '1')
			redirect('welcome');

		$this->load->library('bitcoin');
		$data['address'] = $address;
		$data['decoded_txn'] = $this->bitcoin->decoderawtransaction($this->bitcoin->getrawtransaction($address['final_id']));
		$this->load->view('display_transaction', $data);
	}
};
