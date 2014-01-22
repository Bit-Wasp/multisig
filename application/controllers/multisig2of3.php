<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Multisig2of3 extends CI_Controller {

	public function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('bitcoin');			// Load the bitcoin api functions
		$this->load->library('transaction');		// Load a transaction crafting library
	}
	
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/multisig2of3
	 * or   http://example.com/multisig2of3 with tidy urls enabled.
	 */
	public function index()
	{
		$this->load->library('bitcoin/BitcoinLib'); // Load general bitcoin functions

		// Do Form Validation
		if($this->form_validation->run('submit_3public_keys') == TRUE)
		{
			// Load two public keys from POST.
			$data['pubkeys']['public_key1'] = $this->input->post('public_key1');
			$data['pubkeys']['public_key2'] = $this->input->post('public_key2');
			$data['pubkeys']['public_key3'] = $this->input->post('public_key3');
			
			// Create the multisig address, and then add the address to the wallet.
			$array = array($data['pubkeys']['public_key1'], $data['pubkeys']['public_key2'], $data['pubkeys']['public_key3']);
			$data['multisig'] = $this->bitcoin->createmultisig(2, $array);
			$this->bitcoin->addmultisigaddress(2, $array); 

			// Store the address, the redeemScript, and the private key 
			$this->session->set_userdata('address', $data['multisig']['address']);
			$this->session->set_userdata('redeemScript', $data['multisig']['redeemScript']);
			$data['n'] = $this->session->userdata('n');
			$this->load->view('displaymultisigaddress', $data);

		} else {
			// Generate a new bitcoin keypair 
			$keypair_3 = BitcoinLib::get_new_key_set('00');
			$data['public_key3'] = $keypair_3['pubKey'];
			$this->session->set_userdata('privKey', $keypair_3['privWIF']);
			
			// Import the generated keypair in order to sign the transaction
			$this->bitcoin->importprivkey_noindex($keypair_3['privWIF']);
			$this->session->set_userdata('n','3');			
			$data['n'] = '3';
			// Display the '3 Pubkeys' form
			$this->load->view('get3pubkeys',$data);
		}
	}
	
	public function pay3address()
	{
		$this->load->library('form_validation');
		$data['address'] = $this->session->userdata('address');
		if($data['address'] == NULL)
			redirect('/');
		
		$transaction_id = $this->input->post('txid');
		$this->session->set_userdata('txid',json_encode($transaction_id));
		
		// Load the raw transaction from the submitted transaction ID.
		$rawtransaction = $this->bitcoin->getrawtransaction($transaction_id);
		$data['rawtransaction'] = $this->bitcoin->decoderawtransaction($rawtransaction);
		
		// Load the script pubkey
		foreach($data['rawtransaction']['vout'] as $vout => $output)
		{
			$value = $output['value'];
				break;
		}
		
		// Generate the transaction
		$extras = array('redeemScript' => $this->session->userdata('redeemScript'));
		$this->transaction->add_input($transaction_id, $vout, $extras);
		$this->transaction->add_output($data['address'], $value-0.0001);
		$data['json'] = $this->transaction->print_json();
		$generated = $this->transaction->generate();
		
		// Create, decode, and sign the new transaction
		$data['newrawtransaction'] = $this->bitcoin->createrawtransaction($generated);
		$data['decodenewtransaction'] = $this->bitcoin->decoderawtransaction($data['newrawtransaction']);
		$private_key = $this->session->userdata('privKey');
		$data['signedtransaction'] = $this->bitcoin->signrawtransaction($data['newrawtransaction'], $generated['inputs'], array($private_key));
		$data['n'] = $this->session->userdata('n');
		$this->load->view('signtransaction', $data);
	}
}

/* End of file multisig2of3.php */
/* Location: ./application/controllers/multisig2of3.php */
