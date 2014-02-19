<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Callback extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('Bitcoin');
		$this->load->model('bitcoin_model');
	}

	/**
	 * Blocknotify calls this URL with the $block_id - it's hash.
	 * The function caches transaction ID's and the blockheight so they
	 * can be processed at a later time.
	 */
	public function block($block_id = NULL) {
		if($block_id == NULL)
			return FALSE;

		$watched_addresses = $this->bitcoin_model->watch_address_list();
		if(count($watched_addresses) == 0)
			return FALSE;

		$block = $this->bitcoin->getblock($block_id);

		$txs = array();
		foreach($block['tx'] as $id => $tx_id){
			array_push($txs, array('tx_id' => $tx_id,
									'block_height' => $block['height']));
		}
		$this->bitcoin_model->add_tx_cache($txs);
	}

	/**
	 * This URI is called by a cronjob to process the cached tx list.
	 * - Payments being received are stored in tx_payments
	 * - Payments being spent are checked for the correct outputs, 
	 *   and the txid stored.
	 */
	public function process(){
		// Load the cached transactions to process. Die if nothing to do.
		$list = $this->bitcoin_model->tx_cache_list();
		if($list == FALSE || count($list) == 0 ) 
			return FALSE;

		$input_list = $this->bitcoin_model->tx_payments_list();
		$watched_addresses = $this->bitcoin_model->watch_address_list();

		$received_payments = array();
		$successful_transactions = array();
		$invalid_transactions = array();
		$delete_cache = array();
		foreach($list as $tx) {
			$t = $this->bitcoin->decoderawtransaction($this->bitcoin->getrawtransaction($tx['tx_id']));

			// pay from multisig
			if(count($t['vin']) > 0) {
				// check txid/vout against array - any returned transactions
				// contain inputs we have on record.
				$spending_txns = $this->bitcoin_model->check_vins_against_list($t['vin'], $input_list);

				if(count($spending_txns) > 0) {
					foreach($spending_txns as $txn) {
						// Check that these sending transactions are expected.
						$check = $this->bitcoin_model->check_if_expected($t['vout']);
						if($check !== FALSE) {
							$successful_transactions[] = array('final_id' => $t['txid'],
											   'address' => $txn['assoc_address']);
							// mark escrow as complete
						} else {
							$invalid_transactions[] = array('final_id' => $t['txid'],
											   'address' => $txn['assoc_address']);
							// flag error to the admin
						}
					}
				}
			}

			// pay to multisig
			if(count($t['vout']) > 0) {
				$output_list = $this->bitcoin_model->build_tx_output_array($t['txid'], $tx['block_height'], $t['vout']);
				foreach($output_list as $tmp) {
					// Someone is paying money to a watched address. Record
					// the transaction.
					if(in_array($tmp['address'], $watched_addresses) == TRUE)
						$received_payments[] = $tmp;
				}
			}

			$delete_cache[] = array('tx_id' => $tx['tx_id']);
		}

		if(count($received_payments) > 0)
			$this->bitcoin_model->add_tx_payments($received_payments);

		if(count($delete_cache) > 0)
			$this->bitcoin_model->delete_tx_cache($delete_cache);

		if(count($successful_transactions) > 0)
			$this->bitcoin_model->set_successful($successful_transactions);

		if(count($invalid_transactions) > 0)
			$this->bitcoin_model->set_invalid($invalid_transactions);
	}
};

/* End of File: callback.php */
/* Location : application/controllers/callback.php */

