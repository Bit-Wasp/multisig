<?php

class Examples extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function bip32()
	{

	}

	public function electrum()
	{
		$this->load->library('BitcoinLib');
		$this->load->library('Electrum');
		$magic_byte = '00';
		$string = trim('teach start paradise collect blade chill gay childhood creek picture creator branch');

		$seed = Electrum::decode_mnemonic($string);
		echo "Words: $string<br />";
		echo "Seed:  $seed<br />";
		
		$secexp = Electrum::stretch_seed($seed);
		$secexp = $secexp['seed'];
		echo "Secret Exponent: $secexp<br />";
		
		$mpk = Electrum::generate_mpk($seed);
		echo "MPK: $mpk<br />";
		for($i = 0; $i < 5; $i++) {
			$privkey = Electrum::generate_private_key($seed, $i, 0);
			echo "Private key: $privkey<br />";
			echo "Private WIF: ".BitcoinLib::private_key_to_WIF($privkey, $magic_byte)."<br />";

			$public_key = Electrum::public_key_from_mpk($mpk, $i);
			echo "Public Key: $public_key<br />";
			$address = BitcoinLib::public_key_to_address($public_key, $magic_byte);
			echo "Public derivation: $address.<br />";
			$address = BitcoinLib::private_key_to_address($privkey, $magic_byte);
			echo "Private derivation: $address.<br />";
			echo "-----------<br />";
		}


	}

	public function multisig()
	{
		$this->load->library('BIP32');
		$this->load->library('BitcoinLib');
		$this->load->library('Bitcoin');

		echo "Lets start off by generating a wallet for each of the 'users'.<br />";
		echo "This will be stored on their machine.<br />";
		$wallet[0] = BIP32::master_key('b861e093a58718e145b9791af35fb111');
		$wallet[1] = BIP32::master_key('b861e093a58718e145b9791af35fb222');
		$wallet[2] = BIP32::master_key('b861e093a58718e145b9791af35fb333');
		echo '<pre>';print_r($wallet);echo '</pre>';

		echo "Now we will generate a m/0' extended key. These will yield a private key<br />";
		$user[0] = BIP32::build_key($wallet[0][0], '0');
		$user[1] = BIP32::build_key($wallet[1][0], '0');
		$user[2] = BIP32::build_key($wallet[2][0], '0');
		echo '<pre>';print_r($user);echo '</pre>';

		// As the previous is a private key, we should convert to the corresponding
		// public key: M/0'
		echo "As the previous is a private key, we should convert it to the corresponding<br />";
		echo "public key: M/0' <br />";
		$pub[0] = BIP32::extended_private_to_public($user[0]);
		$pub[1] = BIP32::extended_private_to_public($user[1]);
		$pub[2] = BIP32::extended_private_to_public($user[2]);
		echo '<pre>';print_r($pub);	echo '</pre>';

		echo "This is the key you will ask your users for. For repeated transactions<br />";
		echo "BIP32 allows you to deterministically generate public keys, meaning less<br />";
		echo "effort for everyone involved<br /><br />";
		echo "Now we can generate many multisignature addresses from what we have here: <br />";
		$addr = array();
		for($i = 0; $i < 3; $i++) {
			$bip32key[0] = BIP32::build_key($pub[0], "0/{$i}");
			$bip32key[1] = BIP32::build_key($pub[1], "0/{$i}");
			$bip32key[2] = BIP32::build_key($pub[2], "0/{$i}");
			echo '<pre>';print_r($bip32key);echo '</pre>';
			$pubkey[0] = BIP32::extract_public_key($bip32key[0]);
			$pubkey[1] = BIP32::extract_public_key($bip32key[1]);
			$pubkey[2] = BIP32::extract_public_key($bip32key[2]);

			$addr[$i]= $this->bitcoin->createmultisig(2, $pubkey);
			echo '<pre>';print_r($pubkey);echo '</pre>';
		}
		echo "We now have 3 multisig addresses for these users to transact on!\n";
		echo '<pre>';print_r($addr);echo '</pre>';

	}
};

/* End of File : examples.php */
