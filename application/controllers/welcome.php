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
	
	public function mpk(){
		$this->load->library('bitcoin/BitcoinLib.php');
		$this->load->library('electrum', array('magic_byte' => '00'));
		$mpk[0] = '';
		$mpk[1] = '';
		$mpk[2] = '';
		$iteration = 0;
		
		$address = array();
		foreach($mpk as $key => $mpk_hex){
			$public_key = $this->electrum->public_key_from_mpk($mpk_hex,$iteration);
			echo $public_key." ".BitcoinLib::public_key_to_address($public_key, '00')."<br />";
		}
		echo '<br />';
		/*
		for($i = 0; $i < 6; $i++){
			$pubkey = $this->electrum->public_key_from_mpk($mpk[0],$i);
			echo $pubkey."<Br />";
			echo BitcoinLib::public_key_to_address($pubkey,'00').'<br /><br />';
		}*/
	}
};
