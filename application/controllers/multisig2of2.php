<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Multisig2of2 extends CI_Controller {

	public function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('bitcoin');			// Load the bitcoin api functions
		$this->load->library('BitcoinLib'); 		// Load general bitcoin functions
		$this->load->library('transaction');		// Load a transaction crafting library
		$this->load->model('bitcoin_model');		// Load the bitcoin database functions
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/multisig2of2 
	 * or   http://example.com/multisig2of2
	 */
	public function index()
	{

		// Do Form Validation
		if($this->form_validation->run('submit_2public_keys') == TRUE)
		{
			if(BitcoinLib::import_public_key($this->input->post('public_key1') == FALSE) ||
			   BitcoinLib::import_public_key($this->input->post('public_key2') == FALSE)){
				echo "Invalid public keys!!";
			} else {
				// Load the public key from POST
				$data['pubkeys']['public_key1'] = $this->input->post('public_key1');
				$data['pubkeys']['public_key2'] = $this->input->post('public_key2');

				// Create the multisig address, and then add the address to the wallet.
				$array = array($data['pubkeys']['public_key1'], $data['pubkeys']['public_key2']);
				$data['multisig'] = $this->bitcoin->createmultisig(2, $array);
				$this->bitcoin->addmultisigaddress(2, $array);

				if($this->bitcoin_model->check_have_watch_address($data['multisig']['address']) == FALSE){
					$random = hash('sha256', openssl_random_pseudo_bytes('32'));
					$this->bitcoin_model->add_watch_address(array('public_key1' => $data['pubkeys']['public_key1'],
											'public_key2' => $data['pubkeys']['public_key2'],
											'public_key3' => '',
											'site_priv_key' => $this->session->userdata('privKey'),
											'unique_id' => $random,
											'n' => '2',
											'address' => $data['multisig']['address'],
											'redeemScript' => $data['multisig']['redeemScript']));
					$this->session->unset_userdata('privKey');
					redirect('welcome/id/'.$random);
				} else {
					echo "This address has already been created! Hope you still have your URL bookmarked!";
				}
			}
		} else {
			// Generate a new bitcoin keypair 
			$keypair_2 = BitcoinLib::get_new_key_set('00');
			$data['public_key2'] = $keypair_2['pubKey'];
			$this->session->set_userdata('privKey', $keypair_2['privWIF']);

			// Import the generated keypair in order to sign the transaction
			$this->bitcoin->importprivkey_noindex($keypair_2['privWIF']);

			// Display the '2 Pubkeys' form
			$this->load->view('get2pubkeys', $data);
		}
	}

	public function pay2address()
	{
		$data['address'] = $this->session->userdata('address');
		if($data['address'] == NULL)
			redirect('/');
		$address = $this->bitcoin_model->watch_address_by_address($data['address']);
		if($address == FALSE)
			redirect('welcome');
		if($address['complete'] == '1')
			redirect('welcome/complete/'.$address['unique_id']);

		$data['payments'] = $this->bitcoin_model->payments_to_address($data['address']);

		$extras = array('redeemScript' => $address['redeemScript']);
		$data['value'] = 0; // value in BTC
		foreach($data['payments'] as $pmt) {
			$this->transaction->add_input($pmt['tx_id'], $pmt['vout'], $extras);
			$data['value'] += (float)$pmt['value'];
		}

		$data['destination'] = $this->input->post('destination');	

		$this->transaction->add_output($this->input->post('destination'), (float)($data['value']-0.0001));
		$data['json'] = $this->transaction->print_json();
		$generated = $this->transaction->generate();

		// Create, decode, and sign the new transaction
		$data['newrawtransaction'] = $this->bitcoin->createrawtransaction($generated);
		$data['decodenewtransaction'] = $this->bitcoin->decoderawtransaction($data['newrawtransaction']);
		$data['json_inputs'] = json_encode($generated['inputs']);
		$this->bitcoin_model->log_transaction($data['decodenewtransaction']['vout'], $data['address'], $address['unique_id']);		
		$data['signedtransaction'] = $this->bitcoin->signrawtransaction($data['newrawtransaction'], (array)json_decode($data['json_inputs']), array($address['site_priv_key']));
		$data['n'] = $address['n'];
		$this->load->view('signtransaction', $data);
	}
}

/* End of file multisig2of2.php */
/* Location: ./application/controllers/multisig2of2.php */
