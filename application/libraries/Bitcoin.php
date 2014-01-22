<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Bitcoin Library
 * 
 * This library is a socket for the JSON RPC interface. 
 * Configuration is loaded from ./application/config/bitcoin.php
 * The class contains functions for bitcoind and functions for 
 * bitcoind to callback in order to track information about new transactions.
 * Also contains a function to update exchange rates from the selected
 * provider.
 * 
 * @package		BitWasp
 * @subpackage	Libraries
 * @category	Bitcoin
 * @author		BitWasp
 */
class Bitcoin {
	
	public $CI;
	
	/**
	 * Config
	 * 
	 * This variable contains the bitcoin credentials for the JSON rpc
	 * interface. 
	 */
	public $config;
	
	/** 
	 * Testnet
	 * 
	 * Flag to tell the site if we are currently working in the testnet or
	 * the main bitcoin chain.
	 */
	public $testnet;
	
	/**
	 * Constructor
	 * 
	 * Load the bitcoin configuration using CodeIgniters config library.
	 * Load the jsonRPCclient library with the config, and the bitcoin 
	 * model
	 */
	public function __construct() {
		$this->CI = &get_instance();
		
		$this->CI->config->load('bitcoin', TRUE);
		$this->config = $this->CI->config->item('bitcoin');	

		$this->CI->load->library('jsonrpcclient', $this->config);
	}

	/**
	 * __call
	 * 
	 * Magic method! Checks bitcoin is enabled whenever performing something.
	 */
	public function __call($method,$arguments) {
        if(method_exists($this, $method) && $method !== 'ratenotify' && $method !== 'getinfo') {
           $info = $this->getinfo();
          if($info == NULL) 
				return FALSE;
				
            call_user_func_array(array($this,$method),$arguments);
		}
    }
	

	/**
	 * Get Account
	 * 
	 * Function to query bitcoind, to see which account owns $address.
	 * Returns a string containing the account name if successful, or
	 * an array describing the error on failure.
	 * 
	 * @param		string	$address
	 * @return		string/array
	 */
	public function getaccount($address) {
		$account = $this->CI->jsonrpcclient->getaccount($address);
		return ($account !== NULL) ? $account : FALSE;
	}

	/**
	 * Get Account Address
	 * 
	 * This function asks bitcoind for an address for the specified 
	 * account. It will return the same address each time, until funds
	 * are received at that address. This is used only to display 
	 * addresses for the admin to top up, all other requests for addresses
	 * will provide brand new addresses. 
	 * 
	 * @param	string	$account_name
	 * @return	string/FALSE
	 */
	public function getaccountaddress($account_name) {
		$address = $this->CI->jsonrpcclient->getaccountaddress($account_name);
		return ($address !== NULL) ? $address : FALSE;
	}
	
	/**
	 * Get Balance
	 * 
	 * Function to query bitcoind, to get the balance of the account $account.
	 * Returns a float in each case, whether the account exists or not. 
	 * 
	 * @param		string	$account
	 * @return		float
	 */
	public function getbalance($account) {
		return $this->CI->jsonrpcclient->getbalance($account);
	}
	
	/**
	 * Get Block
	 * 
	 * Function to query bitcoind, to get information about a block ($block_hash)
	 * Returns an array containing the account name if successful, or
	 * an array describing the error on failure.
	 * 
	 * @param		string	$block_hash
	 * @return		array
	 */
	public function getblock($block_hash) {
		return $this->CI->jsonrpcclient->getblock($block_hash);
	}
	
	/**
	 * Get Block Hash
	 * 
	 * Function to query bitcoind, to get the block hash for a particular
	 * height.
	 * Returns a string containing the block hash if successful, or an 
	 * array describing the error on failure.
	 * 
	 * @param		string	$block_no
	 * @return		string / array
	 */	
	public function getblockhash($block_no) {
		return $this->CI->jsonrpcclient->getblockhash($block_no);
	}
		
	/**
	 * Get Info
	 * 
	 * Function to query bitcoind for general information, like version,
	 * block height, balance, difficulty, 
	 * 
	 * @param		string
	 * @return		string / array
	 */		
	public function getinfo() {
		return $this->CI->jsonrpcclient->getinfo();
	}
	
	/**
	 * Get Recevied By Address
	 * 
	 * Query bitcoind to get the balance this particular address has
	 * received.
	 */
	public function getreceivedbyaddress($address) {
		return $this->CI->jsonrpcclient->getreceivedbyaddress($address);
	}
	
	/**
	 * Get Transaction
	 * 
	 * Function to query bitcoind for a transaction ($tx_id). The transaction
	 * must belong to this wallet in order read this information. Returns
	 * an array containing transaction information, or an error array
	 * on failure.
	 * 
	 * @param		string	$tx_hash
	 * @return		array
	 */		
	public function listunspent($confirmations) {
		return $this->CI->jsonrpcclient->listunspent($confirmations);
	}
	public function listreceivedbyaddress() {
		return $this->CI->jsonrpcclient->listreceivedbyaddress();
	}
	public function gettransaction($tx_hash) {
		return $this->CI->jsonrpcclient->gettransaction($tx_hash);
	}
	
	public function getrawtransaction($transaction) {
		return $this->CI->jsonrpcclient->getrawtransaction($transaction);
	}
	public function createrawtransaction($transaction) {
		return $this->CI->jsonrpcclient->createrawtransaction($transaction['inputs'], $transaction['outputs']);
	}
	public function decoderawtransaction($transaction_hex) {
		return $this->CI->jsonrpcclient->decoderawtransaction($transaction_hex);
	}
	public function signrawtransaction($transaction_hex, $inputs = NULL, $privkey = NULL) {
		if($inputs == NULL)
			return $this->CI->jsonrpcclient->signrawtransaction($transaction_hex);
		if($privkey == NULL)
			return $this->CI->jsonrpcclient->signrawtransaction($transaction_hex, $inputs);
		return $this->CI->jsonrpcclient->signrawtransaction($transaction_hex, $inputs, $privkey);
	}
	
	public function sendrawtransaction($transaction_hex){
		return $this->CI->jsonrpcclient->sendrawtransaction($transaction_hex);
	}
	public function addmultisigaddress($n, $public_keys, $account = ""){
		return $this->CI->jsonrpcclient->addmultisigaddress($n, $public_keys, $account);
	}
	public function createmultisig($n, $public_keys){
		return $this->CI->jsonrpcclient->createmultisig($n, $public_keys);
	}
	/**
	 * Import Private Key
	 * 
	 * Function to ask bitcoind to import the wallet import format private
	 * key.
	 *
	 * @param		string	$wif
	 * @param		string	$account
	 * @return		????
	 */
	public function importprivkey($wif, $account, $reindex = TRUE) {
		return $this->CI->jsonrpcclient->importprivkey("$wif", "$account", $reindex);
	}
	
	/**
	 * List Accounts
	 * 
	 * Function to query bitcoind for information about the accounts
	 * in the wallet. Displays transactions with 0 confirmations.
	 * 
	 * @param		string
	 * @return		array
	 */			
	public function listaccounts($confirmations = 6) {
		$tmp = (array)$this->CI->jsonrpcclient->listaccounts($confirmations);
		$res = array();
		foreach($tmp as $acc => $bal) {
			if(!preg_match('/\s+/', $acc) && $acc !== '')
				$res[$acc] = (float)$bal;			
		}
		return $res;
	}

	/**
	 * Move
	 * 
	 * Function to ask bitcoind to move funds from the $from account
	 * to the $to account. Will create the account if it doesn't exist.
	 * Already have error-checked this account name, we want it to exist 
	 * already. Does not broadcast a transaction to the bitcoin network.
	 * 
	 * @param		string	$from
	 * @param		string	$to
	 * @param		float	$amount
	 * @return		boolean
	 */			
	public function move($from, $to, $amount) {
		return $this->CI->jsonrpcclient->move($from, $to, (float)$amount);
	}
	
	/**
	 * ID From Input
	 * 
	 * This function accepts $input, and attempts to get the raw transaction
	 * of the transaction ID. If this succeeds, then we simply return the
	 * ID. If it fails, we will attempt to broadcast the transaction to 
	 * the network. If the transaction is already in the block chain, 
	 * we can detect this, and decode the raw transaction to obtain the 
	 * ID. If not already in the block chain, and no error results from 
	 * broadcasting the transaction, then obtain the ID, and return it.
	 * Returns FALSE if it's impossible to obtain the transaction ID, or
	 * the transaction ID if successful.
	 * 
	 * @param	string	$input
	 * @return	string/FALSE
	 */
	public function id_from_input($input) {
		$raw = $this->getrawtransaction($input);
		if(isset($raw['code']))
		{
			$send = $this->sendrawtransaction($input);
			if(isset($send['code']) && $send['message'] == 'transaction already in block chain')
			{
				$decode = $this->decoderawtransaction($input);
				$tx_id = $decode['txid'];
			} else {
				if(!isset($send['code']))
				{
					$tx_id = $send;
				}
			}
		} else {
			$tx_id = $input;
		}
	
		return (isset($tx_id) == TRUE) ? $tx_id : FALSE;
	}
	
	/**
	 * Send From
	 * 
	 * Function to ask bitcoind to send $value BTC from any $src_ac, to 
	 * $to_address. The transaction must belong to this wallet in order 
	 * read this information. Returns a transaction id ($tx_id) if the 
	 * transaction is successful, otherwise returns an error on failure.
	 * 
	 * @param		string	$src_ac
	 * @param		string	$to_address
	 * @param		float	$value
	 * @return		bool
	 */			
	public function sendfrom($src_ac, $to_address, $value) {
		return $this->CI->jsonrpcclient->sendfrom($src_ac, $to_address, (float)$value);
	}

	/**
	 * Validate Address
	 * 
	 * Function to validate a bitcoin address. Checks if there is a 
	 * base58 address, and other tests. Returns a boolean with the answer.
	 * 
	 * @param		string	$address
	 * @return		bool
	 */		
	public function validateaddress($address) {
		$valid = $this->CI->jsonrpcclient->validateaddress($address);
		return $valid['isvalid'];
	}

};
