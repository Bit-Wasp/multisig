<?php

require_once(dirname(__FILE__).'/ecc-lib/auto_load.php');

/**
 * BitcoinLib
 * 
 * This library is largely a rewrite of theymos' bitcoin library, 
 * along with some more functions for key manipulation.
 * 
 * It depends on php-ecc, written by Mathyas Danter.
 * 
 * Thomas Kerin
 */

class BitcoinLib {
	
	/**
	 * HexChars
	 * 
	 * This is a string containing the allowed characters in base16.
	 */
	private static $hexchars = "0123456789ABCDEF";
	
	/**
	 * Base58Chars
	 * 
	 * This is a string containing the allowed characters in base58.
	 */
	private static $base58chars = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
	
	/**
	 * Hex Encode 
	 * 
	 * Encodes a decimal $number into a hexadecimal string.
	 * 
	 * @param	int	$number
	 * @return	string
	 */
	public static function hex_encode($number) {
		return gmp_strval(gmp_init($number, 10), 16);
	}
	
	/**
	 * Hex Decode
	 * 
	 * Decodes a hexadecimal $hex string into a decimal number.
	 * 
	 * @param	string	$hex
	 * @return	int
	 */
	public static function hex_decode($hex) {
		return gmp_strval(gmp_init($hex, 16), 10);
	}

	/**
	 * Base58 Decode
	 * 
	 * This function accepts a base58 encoded string, and decodes the 
	 * string into a number, which is converted to hexadecimal. It is then
	 * padded with zero's.
	 * 
	 * @param	string	$base58
	 * @return	string
	 */
	public static function base58_decode($base58) {
		$origbase58 = $base58;
		$return = "0";
		
		for($i = 0; $i < strlen($base58); $i++) {
			// return = return*58 + current position of $base58[i]in self::$base58chars
			$return = gmp_add(gmp_mul($return, 58), strpos(self::$base58chars, $base58[$i]));
		}
		$return = gmp_strval($return, 16);
		for($i = 0; $i < strlen($origbase58) && $origbase58[$i] == "1"; $i++) {
			$return = "00".$return;
		}
		if(strlen($return) %2 != 0) {
			$return = "0".$return;
		}
		return $return;
	}


	/**
	 * Base58 Encode
	 * 
	 * Encodes a $hex string in base58 format. Borrowed from prusnaks
	 * addrgen code: https://github.com/prusnak/addrgen/blob/master/php/addrgen.php 
	 * 
	 * @param	string	$hex
	 * @return	string
	 * @author	Pavel Rusnak
	 */
	public static function base58_encode($hex) {
		$num = gmp_strval(gmp_init($hex, 16), 58);
		$num = strtr($num
		, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuv'
		, self::$base58chars);

		$pad = ''; $n = 0;
		while ($hex[$n] == '0' && $hex[$n+1] == '0') {
			$pad .= '1';
			$n += 2;
		}

		return $pad . $num;
	}
	
	
	/**
	 * Base58 Encode Checksum
	 * 
	 * This function takes a checksum of the input $hex data, concatenates
	 * it with the input, and returns a base58 encoded string with checksum.
	 * 
	 * @param	string	$hex
	 * @return	string
	 */
	public static function base58_encode_checksum($hex) {
		$checksum = self::dhash_string($hex);
		$checksum = substr($checksum, 0, 8);
		$hash = $hex.$checksum;
		return self::base58_encode($hash);
	}

	/**
	 * DHash String
	 * 
	 * Takes a sha256(sha256()) hash of the $hash_string.
	 * 
	 * @param	string	$hex_string
	 * @return  string
	 */
	public static function dhash_string($hash_string) {
		$hash = @pack("H*", $hash_string);
		return hash("sha256", hash("sha256", $hash, true));
	}

	/**
	 * Hash160
	 * 
	 * Takes $data as input and returns a ripemd160(sha256()) hash of $data.
	 * 
	 * @param	string	$data
	 * @return	string
	 */
	public static function hash160($data) {
		$data = @pack("H*", $data);
		return hash("ripemd160", hash("sha256", $data, true));
	}

	/**
	 * Hash160 To Address
	 * 
	 * This function accepts an $address_version (used to specify the 
	 * protocol or the purpose of the address) which is concatenated with
	 * the $hash160 string, and converted to the basee58 encoded format
	 * (with a checksum)
	 * 
	 * @param	string	$hash160
	 * @param	string	$address_version
	 * @return	string
	 */
	public static function hash160_to_address($hash160, $address_version) {
		$hash160 = $address_version . $hash160;
		return self::base58_encode_checksum($hash160);
	}
	
	/**
	 * Public Key To Address
	 * 
	 * This function accepts the $public_key, and $address_version (used
	 * to specify the protocol or purpose for the address) as input, and 
	 * returns a bitcoin address by taking the hash160 of the $public_key,
	 * and converting this to a base_
	 * 
	 * @param	string	$public_key
	 * @param	string	$address_version
	 * @return	string
	 */
	public static function public_key_to_address($public_key, $address_version) {
		$hash160 = self::hash160($public_key);
		return self::hash160_to_address($hash160, $address_version);
	}

	/**
	 * Get New Private Key
	 * 
	 * This function generates a new private key, a number from 1 to $n. 
	 * Once it finds an acceptable value, it will encode it in hex, pad it, 
	 * and return the private key.
	 * 
	 * @return	string
	 */
	public static function get_new_private_key() {
		$g = SECcurve::generator_secp256k1();
		$n = $g->getOrder();

		$privKey = gmp_strval(gmp_init(bin2hex(openssl_random_pseudo_bytes(32)),16));
		while($privKey >= $n) {
			$privKey = gmp_strval(gmp_init(bin2hex(openssl_random_pseudo_bytes(32)),16));
		}
		$privKeyHex = self::hex_encode($privKey);
		return str_pad($privKeyHex, 64, '0', STR_PAD_LEFT);
	}

	/**
	 * Private Key To Public Key
	 * 
	 * Accepts a $privKey as input, and does EC multiplication to obtain
	 * a new point along the curve. The X and Y coordinates are the public
	 * key, which are returned as a hexadecimal string in uncompressed
	 * format.
	 * 
	 * @param	string	$privKey
	 * @return	string
	 */
	public static function private_key_to_public_key($privKey, $compressed = FALSE) {
		$g = SECcurve::generator_secp256k1();
    
		$privKey = self::hex_decode($privKey);  
		$secretG = Point::mul($privKey, $g);
	
		$xHex = self::hex_encode($secretG->getX());  
		$yHex = self::hex_encode($secretG->getY());

		$xHex = str_pad($xHex, 64, '0', STR_PAD_LEFT);
		$yHex = str_pad($yHex, 64, '0', STR_PAD_LEFT);
		$public_key = '04'.$xHex.$yHex;
		
		return ($compressed == TRUE) ? BitcoinLib::compress_public_key($public_key) : $public_key;
	}

	/**
	 * Private Key To Address
	 * 
	 * Converts a $privKey to the corresponding public key, and then 
	 * converts to the bitcoin address, using the $address_version.
	 * 
	 * @param	string	$private_key
	 * @param	string	$address_versionh
	 * @return	string
	 */
	public static function private_key_to_address($private_key, $address_version) {
		$public_key = self::private_key_to_public_key($private_key);
		return self::public_key_to_address($public_key, $address_version);
	}

	/**
	 * Get New Key Pair
	 * 
	 * Generate a new private key, and convert to an uncompressed public key.
	 * 
	 * @return array
	 */
	public static function get_new_key_pair() {
		$private_key = self::get_new_private_key();
		$public_key = self::private_key_to_public_key($private_key);
		
		return array('privKey' => $private_key,
					 'pubKey' => $public_key);
	}
	
	/**
	 * Get New Key Set
	 * 
	 * This function requires the $address_version to be supplied in order
	 * to generate the correct privateWIF and pubAddress. It returns an 
	 * array containing the hex private key, WIF private key, public key,
	 * and bitcoin address
	 * 
	 * @param	string	$address_version
	 * @return	array
	 */
	public static function get_new_key_set($address_version) {
		do {
			$key_pair = self::get_new_key_pair();
			$private_WIF = self::private_key_to_WIF($key_pair['privKey'], $address_version);
			$public_address = self::public_key_to_address($key_pair['pubKey'], $address_version);
		} while (!self::check_address($public_address, $address_version));

		return array('privKey' => $key_pair['privKey'],
					 'pubKey' => $key_pair['pubKey'],
					 'privWIF' => $private_WIF,
					 'pubAdd' => $public_address);
	}
	
	/**
	 * Get Private Address Version
	 * 
	 * This function 
	 * Generates a private key address version (the prefix) from the 
	 * supplied public key address version, by adding 0x80 to the number.
	 * 
	 * @param	string	$address_version
	 * @return	string
	 */
	public static function get_private_key_address_version($address_version) {
		return gmp_strval(
					gmp_add(
						gmp_init($address_version, 16),
						gmp_init('80',16)
					),
					16
				);
	}
	
	/**
	 * Private Key To WIF
	 * 
	 * Converts a hexadecimal $privKey to an address, using the $address_version.
	 * 
	 * @return string
	 */
	public static function private_key_to_WIF($privKey, $address_version) {
		return self::hash160_to_address($privKey, self::get_private_key_address_version($address_version));
	}
	
	/**
	 * WIF To Private Key
	 * 
	 * Convert a base58 encoded $WIF private key to a hexadecimal private key.
	 * 
	 * @param	string	$WIF
	 * @return	string
	 */
	public static function WIF_to_private_key($WIF) {
		return self::address_to_hash160($WIF);
	}
	
	/**
	 * Check Address
	 * 
	 * This function takes the base58 encoded bitcoin $address, checks
	 * the length of the decoded string is correct, that the encoded
	 * version information is allowed, and that the checksum matches.
	 * Returns TRUE for a valid $address, and FALSE on failure.
	 * 
	 * @param	string	$address
	 * @param	string	$address_version
	 * @return	boolean
	 */
	public static function check_address($address, $address_version) {
		$address = self::base58_decode($address);
		if (strlen($address) != 50) {
			return false;
		}
		$version = substr($address, 0, 2);
		if (hexdec($version) > hexdec($address_version)) {
			return false;
		}
		$check = substr($address, 0, strlen($address) - 8);
		$check = self::dhash_string($check);
		$check = substr($check, 0, 8);
		return $check == substr($address, strlen($address) - 8);
	}

	/**
	 * Import Public Key
	 * 
	 * Imports an arbitrary $public_key, and returns it untreated if the
	 * left-most bit is '04', or else decompressed the public key if the
	 * left-most bit is '02' or '03'.
	 * 
	 * @param	string	$public_key
	 * @return	string
	 */
	public static function import_public_key($public_key) {
		$first = substr($public_key, 0, 2);
		if(($first == '02' || $first == '03') && strlen($public_key)) {
			// Compressed public key, need to decompress.
			$x_coordinate = substr($public_key, 2);
			$decompressed = self::decompress_public_key($first, $x_coordinate);
			return $decompressed['public_key'];
		} else if($first == '04') {
			// Regular public key, pass back untreated.
			return $public_key;
		} else {
			// Not a valid public key
			return FALSE;
		}
	}

	/**
	 * Compress Public Key
	 * 
	 * Converts an uncompressed public key to the shorter format. These
	 * compressed public key's have a prefix of 02 or 03, indicating whether
	 * Y is odd or even. With this information, and the X coordinate, it
	 * is possible to regenerate the uncompressed key at a later stage.
	 * 
	 * @param	string	$public_key
	 * @return	string
	 */
	public static function compress_public_key($public_key) {
		$x = substr($public_key, 2, 64);
		$y = substr($public_key, 66, 64);
		$prefix = '0';
		$prefix.= ((gmp_Utils::gmp_mod2(gmp_init($y, 16), 2))==0) ? '2' : '3';
		
		return $prefix.$x;
	}

	/**
	 * Decompress Public Key
	 * 
	 * Accepts a y_byte, 02 or 03 indicating whether the Y coordinate is
	 * odd or even, and $passpoint, which is simply a hexadecimal X coordinate.
	 * Using this data, it is possible to deconstruct the original 
	 * uncompressed public key.
	 * 
	 * @param	string	$y_byte
	 * @param	string	$passpoint
	 * @return	string
	 */
	public static function decompress_public_key($key) {
		$y_byte = substr($key, 0, 2);
		$x_coordinate = substr($key, 2);
		$x = gmp_init($x_coordinate, 16);
		$curve_params = SECcurve::secp256k1_params();
		$curve = SECcurve::curve_secp256k1();
		$generator = SECcurve::generator_secp256k1();
		
		$x3 = NumberTheory::modular_exp( $x, 3, $curve_params['p'] );
		$y2 = gmp_add(
					$x3,
					$curve_params['b']
				);
		
		$y0 = NumberTheory::square_root_mod_prime(
					$y2,
					$curve_params['p']
				);
		$y1 = gmp_strval(gmp_sub($curve_params['p'], $y0), 10);
		
		if($y_byte == '02') {
			$y_coordinate = (gmp_Utils::gmp_mod2(gmp_init($y0, 10), 2) == '0') ? $y0 : $y1;
		} else if($y_byte == '03') {
			$y_coordinate = (gmp_Utils::gmp_mod2(gmp_init($y0, 10), 2) !== '0') ? $y0 : $y1;
		}
		$y_coordinate = gmp_strval($y_coordinate, 16);
		
		return array('x' => $x_coordinate, 
					 'y' => $y_coordinate,
					 'point' => new Point($curve, gmp_init($x_coordinate, 16), gmp_init($y_coordinate, 16), $generator->getOrder()),
					 'public_key' => '04'.$x_coordinate.$y_coordinate);
	}

	/**
	 * Decode Redeem Script
	 * 
	 * This recursive function extracts the m and n values for the 
	 * multisignature address, as well as the public keys.
	 * 
	 * @param	string	$redeem_script
	 * @param	array	$data(should not be set!)
	 * @return	array
	 */
	public function decode_redeem_script($redeem_script, $data = array()) {
		// If there is no more work to be done (script is fully parsed, 
		// return the array)
		if(strlen($redeem_script) == 0)
			return $data;
			
		// Fail if the redeem_script has an uneven number of characters.
		if(strlen($redeem_script) % 2 !== 0)
			return FALSE;
			
		// First step is to get m, the required number of signatures
		if(!isset($data['m']) || count($data) == 0) {
			$data['m'] = gmp_strval(gmp_sub(gmp_init(substr($redeem_script, 0, 2),16),gmp_init('50',16)),10);							
			$data['keys'] = array();
			$redeem_script = substr($redeem_script, 2);
			
		} else if(count($data['keys']) == 0 && !isset($data['next_key_charlen'])) {
			// Next is to find out the length of the following public key.
			$hex = substr($redeem_script, 0, 2);
			// Set up the length of the following key.
			$data['next_key_charlen'] = gmp_strval(gmp_mul(gmp_init('2',10),gmp_init($hex, 16)),10);
			$redeem_script = substr($redeem_script, 2);
			
		} else if(isset($data['next_key_charlen'])) {
			// Extract the key, and work out the next step for the code.
			$data['keys'][] = substr($redeem_script, 0, $data['next_key_charlen']);
			$next_op = substr($redeem_script, $data['next_key_charlen'], 2);
			$redeem_script = substr($redeem_script, ($data['next_key_charlen']+2));
			unset($data['next_key_charlen']);
			
			// If 1 <= $next_op >= 4b
			if( in_array(gmp_cmp(gmp_init($next_op, 16),gmp_init('1',16)),array('0','1')) 
			 && in_array(gmp_cmp(gmp_init($next_op, 16),gmp_init('4b', 16)),array('-1','0'))) {
				// Set the next key character length
				$data['next_key_charlen'] = gmp_strval(gmp_mul(gmp_init('2',10),gmp_init($next_op, 16)),10);
			
			// If 52 <= $next_op >= 60
			} else if( in_array(gmp_cmp(gmp_init($next_op, 16),gmp_init('52',16)),array('0','1')) 
					&& in_array(gmp_cmp(gmp_init($next_op, 16),gmp_init('60', 16)),array('-1','0'))) {
				// Finish the script.
				$data['n'] = gmp_strval(gmp_sub(gmp_init($next_op, 16),gmp_init('50',16)),10);
				$redeem_script = '';
			} else {
				// Something weird, malformed redeemScript.
				return FALSE;
			}
		} 
		return self::decode_redeem_script($redeem_script, $data);
	}

	/**
	 * Validate Input
	 * 
	 * This function accepts a decoded vin (an array), and performs a 
	 * number of checks to see if it has been signed correctly. Bitcoind 
	 * will check that redeemScript is appropriate for the signatures, if 
	 * any. 
	 * 1) Extract the signatures and redeemScript from the scriptSig
	 * 2) Check that at least one signature has been added
	 * 3) Check that the extracted redeemScript matches the one we have stored.
	 * 4) Decode the redeemScript, check this is valid.
	 * It returns an array of information about the input if it's valid,
	 * otherwise it returns FALSE for any number of reasons.
	 * 
	 * @param	array	$input
	 * @param	string	$orig_redeem_script
	 * return	array/FALSE
	 */
	public function validate_partially_signed_input($input, $orig_redeem_script) {
		$sig = explode(" ",$input['scriptSig']['asm']);
		$end_pos = count($sig)-1;
		
		// Extract signatures and redeemScript string
		$info['signatures'] = array();
		foreach($sig as $pos => $data) {
			// Ignore first position ('0')
			if($pos == 0)
				continue;
			// If it's the final position, it's the redeemScript. Set
			// this and break.
			if($pos == $end_pos) {
				$info['redeemScript'] = $data;
				break;
			}	
			
			$info['signatures'][] = $data;
		}
		
		// Check at least one signature was added.
		if(count($info['signatures']) == 0)
			return FALSE;
		
		// Check the redeem script in the transaction matches what we
		// have on record. Confirms the public keys.
		if($info['redeemScript'] !== $orig_redeem_script)
			return FALSE;
		
		// Decode redeem script
		$info['redeem_script_arr'] = self::decode_redeem_script($info['redeemScript']);
		if($info['redeem_script_arr'] == FALSE)
			return FALSE;
			
		// Check the signatures only use keys contained in the redeemScript
		return $info;
	}


};
