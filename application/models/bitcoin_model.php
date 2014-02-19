<?php

class Bitcoin_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}


	// watch_addresses
	public function watch_address_list() {
		$query = $this->db->get('watched_addresses');
		if($query->num_rows() > 0) {
			$array = $query->result_array();
			$results = array();
			foreach($array as $id => $addr) {
				$results[] = $addr['address'];
			}
			return $results;
		} else {
			return array();
		}
	}

	public function add_watch_address($address) {
		return ($this->db->insert('watched_addresses',  $address) == TRUE) ? TRUE : FALSE;
	}

	public function check_have_watch_address($address) {
		$this->db->where('address', $address);
		$query = $this->db->get('watched_addresses');
		return ($query->num_rows() > 0) ? TRUE : FALSE;
	}

	public function watch_address_by_unique_id($unique_id) {
		$this->db->where('unique_id', $unique_id);
		$query = $this->db->get('watched_addresses');
		return ($query->num_rows() > 0) ? $query->row_array() : FALSE;
	}
	public function watch_address_by_address($address) {
		$this->db->where('address', $address);
		$query = $this->db->get('watched_addresses');
		return ($query->num_rows() > 0) ? $query->row_array() : FALSE;
	}
        public function set_successful($array) {
                foreach($array as $record) {
			$update = array('final_id' => $record['final_id'],
					'complete' => '1');

                        $this->db->where('address', $record['address']);
                        if($this->db->update('watched_addresses', $update) == TRUE) {
//                                $this->db->where('address', $record['address']);
//                                $this->db->delete('tx_payments');

//                                $this->db->where('address', $record['address']);
//                                $this->db->delete('tx_sent');
                        }
                }
        }

        public function set_invalid($array) {
                foreach($array as $record) {
			$update = array('final_id' => $record['tx_id'],
					'complete' => '1');

                        $this->db->where('address', $record['address']);
                        if($this->db->update('watched_addresses', $update) == TRUE) {
//                                $this->db->where('address', $record['address']);
//                               $this->db->delete('tx_payments');

//                                $this->db->where('address', $record['address']);
//                                $this->db->delete('tx_sent');

                                $record['address'] = $address;
                                $this->db->insert('tx_scam', $record);
                        }
                }

        }





	// tx_cache
	public function tx_cache_list() {
		$query = $this->db->get('tx_cache');
		return ($query->num_rows() > 0) ? $query->result_array() : FALSE ;
	}

	public function add_tx_cache($tx_array) {
		return ($this->db->insert_batch('tx_cache', $tx_array) == TRUE) ? TRUE : FALSE;
	}

	public function delete_tx_cache($array) {
		$i = 0;
		foreach($array as $tx) {
			if($i == 0){
				$this->db->where('tx_id', $tx['tx_id']);
			} else {
				$this->db->or_where('tx_id',$tx['tx_id']);
			}
			$i++;
		}
		return $this->db->delete('tx_cache');
	}

	public function build_tx_output_array($txid, $block_height,  $outputs) {
		$addrs = array();

		foreach($outputs as $v_out => $output) {
			$addrs[] = array('address' => $output['scriptPubKey']['addresses'][0],
					 'value' => $output['value'],
					 'tx_id' => $txid,
					 'vout' => $v_out,
					 'block_height' => $block_height);
		}
		return $addrs;
	}




	// tx_payments
	public function add_tx_payments($tx_array) {
		return ($this->db->insert_batch('tx_payments', $tx_array) == TRUE) ? TRUE : FALSE;
	}

	public function payments_to_address($address) {
		$this->db->where('address', $address);
		$query = $this->db->get('tx_payments');
		return ($query->num_rows() > 0) ? $query->result_array() : FALSE ;
	}
	public function payment_ids_to_address($address) {
		$this->db->select('tx_id');
		$this->db->where('address', $address);
		$query = $this->db->get('tx_payments');
		if($query->num_rows() > 0) {
			$id_list = array();
			foreach($query->result_array() as $result) {
				$id_list[] = $result['tx_id'];
			}
			return $id_list;
		} else {
			return FALSE;
		}
	}
	
	public function tx_payments_list() {
		$query = $this->db->get('tx_payments');
		if($query->num_rows() > 0) {
			$txid_list = array();
			$tx_list = array();
			foreach($query->result_array() as $id => $tx) {
				$tx_list[$tx['tx_id']] = $tx;
				$txid_list[] = $tx['tx_id'];
			}
			return array('tx_ids' => $txid_list,
				 'txs' => $tx_list);
		} else {
			return FALSE;
		}
	}

	public function check_vins_against_list($inputs, $list) {
		$interesting = array();
		foreach($inputs as $input) {
			if(isset($input['txid'])){
				if(in_array($input['txid'], $list['tx_ids'])) {
					if( $list['txs'][$input['txid']]['vout'] == $input['vout']) {
						$input['assoc_address'] = $list['txs'][$input['txid']]['address'];
						$interesting[] = $input;
					}
				}
			}
		}
		return $interesting;
	}

	// tx_sent functions
	public function setup_transaction($json, $hex, $destination, $address) {
		$this->db->where('address', $address);
		$update = array('json_inputs' => $json,
						'destination' => $destination,
						'unsigned_transaction' => $hex);
		return ($this->db->update('watched_addresses', $update) == TRUE) ? TRUE : FALSE;
	}
	
	public function outputs_to_log_array($outputs){
		$array = array();
		foreach($outputs as $vout => $output) {
			$array[] = array('address' => $output['scriptPubKey']['addresses'][0],
							'value' => $output['value']);
		}
		return $array;
	}

	public function log_transaction($outputs, $multisig_address, $unique_id) {
		$outputs = $this->outputs_to_log_array($outputs);
		$outputs_hash = hash('sha256', json_encode($outputs));

		if($this->search_log_hashes($outputs_hash) !== FALSE)
			return FALSE;

		$insert = array('outputs' => $outputs_hash,
				'address' => $multisig_address,
				'unique_id' => $unique_id);
		return ($this->db->insert('tx_sent', $insert) == TRUE) ? TRUE : FALSE;
	}

	public function search_log_hashes($out_hash) {
		$this->db->where('outputs', $out_hash);
		$query = $this->db->get('tx_sent');
		return ($query->num_rows > 0) ? $query->row_array() : FALSE;
	}

	public function check_if_expected($output) {
		$outputs = $this->outputs_to_log_array($output);
		$outputs_hash = hash('sha256', json_encode($outputs));

		$search = $this->search_log_hashes($outputs_hash);
		return ($search == FALSE) ? FALSE : $search['address'];
	}
	
	
	/**
	 * This function accepts a $transaction_hex string and a multisig
	 * address, and attempts to validate that the transaction has been 
	 * signed correctly and using the appropriate inputs. 
	 * 
	 * If any of the conditions imposed cause the check to fail, FALSE is
	 * returned. Otherwise, any information extracted from the transaction
	 * is returned. 
	 * 
	 * @param	string	$transaction_hex
	 * @param	string	$multisig_address
	 * @return	array/FALSE
	 */
	public function validate_transaction_hex($transaction_hex, $multisig_address)
	{
		$this->load->library('Bitcoin');
		$this->load->library('BitcoinLib');
		
		$address_info = $this->watch_address_by_address($multisig_address);
		if($address_info == FALSE)
			return FALSE;
			
		$allowed_inputs = $this->payment_ids_to_address($multisig_address);
		if($allowed_inputs == FALSE)
			return FALSE;
			
		$decode = $this->bitcoin->decoderawtransaction($transaction_hex);
		
		// Restrict inputs in the transaction to those sent to the multisig address.
		// Also check the transactions redeemScript, and that the public keys match, in parse_input()
		$valid = TRUE;
		foreach($decode['vin'] as $v_in => $input) {
			if(!in_array($input['txid'], $allowed_inputs))
				$valid = FALSE;
				
			if(BitcoinLib::validate_partially_signed_input($input, $address_info['redeemScript']) == FALSE)
				$valid = FALSE;
		}
		return $valid;
	}

}
