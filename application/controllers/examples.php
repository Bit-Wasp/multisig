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
		echo "Words: $string\n";
		echo "Seed:  $seed\n";
		
		$secexp = Electrum::stretch_seed($seed);
		$secexp = $secexp['seed'];
		echo "Secret Exponent: $secexp\n";
		
		$mpk = Electrum::generate_mpk($seed);
		echo "MPK: $mpk\n";
		for($i = 0; $i < 5; $i++) {
			$privkey = Electrum::generate_private_key($seed, $i, 0);
			echo "Private key: $privkey\n";
			echo "Private WIF: ".BitcoinLib::private_key_to_WIF($privkey, $magic_byte)."\n";

			$public_key = Electrum::public_key_from_mpk($mpk, $i);
			echo "Public Key: $public_key\n";
			$address = BitcoinLib::public_key_to_address($public_key, $magic_byte);
			echo "Public derivation: $address.\n";
			$address = BitcoinLib::private_key_to_address($privkey, $magic_byte);
			echo "Private derivation: $address.\n";
			echo "-----------\n";
		}


	}

	public function multisig()
	{
		$this->load->library('BIP32');
		$this->load->library('BitcoinLib');
		$this->load->library('Bitcoin');

		echo "Lets start off by generating a wallet for each of the 'users'.\n";
		echo "This will be stored on their machine.\n";
		$wallet[0] = BIP32::master_key('b861e093a58718e145b9791af35fb111');
		$wallet[1] = BIP32::master_key('b861e093a58718e145b9791af35fb222');
		$wallet[2] = BIP32::master_key('b861e093a58718e145b9791af35fb333');
		print_r($wallet);

		echo "Now we will generate a m/0' extended key. These will yield a private key\n";
		$user[0] = BIP32::build_key($wallet[0][0], '0');
		$user[1] = BIP32::build_key($wallet[1][0], '0');
		$user[2] = BIP32::build_key($wallet[2][0], '0');
		print_r($user);

		// As the previous is a private key, we should convert to the corresponding
		// public key: M/0'
		echo "As the previous is a private key, we should convert it to the corresponding\n";
		echo "public key: M/0' \n";
		$pub[0] = BIP32::extended_private_to_public($user[0]);
		$pub[1] = BIP32::extended_private_to_public($user[1]);
		$pub[2] = BIP32::extended_private_to_public($user[2]);
		print_r($pub);	

		echo "This is the key you will ask your users for. For repeated transactions\n";
		echo "BIP32 allows you to deterministically generate public keys, meaning less\n";
		echo "effort for everyone involved\n\n";
		echo "Now we can generate many multisignature addresses from what we have here: \n";
		$addr = array();
		for($i = 0; $i < 3; $i++) {
			$bip32key[0] = BIP32::build_key($pub[0], "0/{$i}");
			$bip32key[1] = BIP32::build_key($pub[1], "0/{$i}");
			$bip32key[2] = BIP32::build_key($pub[2], "0/{$i}");
			print_r($bip32key);
			$pubkey[0] = BIP32::extract_public_key($bip32key[0]);
			$pubkey[1] = BIP32::extract_public_key($bip32key[1]);
			$pubkey[2] = BIP32::extract_public_key($bip32key[2]);

			$addr[$i]= $this->bitcoin->createmultisig(2, $pubkey);
			print_r($pubkey);
		}
		echo "We now have 3 multisig addresses for these users to transact on!/n";
		print_r($addr);

	}
};

/* End of File : examples.php */
