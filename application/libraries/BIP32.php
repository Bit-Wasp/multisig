<?php

require_once "/home/afk/vps/application/libraries/ecc-lib/auto_load.php";
require_once "/home/afk/vps/application/libraries/bitcoin/BitcoinLib.php";

class Extended_Key {
	protected $type;
	protected $k;
	protected $c;

	public function __construct($key, $chain) {
		$this->type = $type;
		$this->c = $chain;
		$this->k = $key;
		$this->id = $id;
	}
	
	public function export(){
		
	}
	
}

class BIP32 {
	
	public static function master_key($seed) {
		$g = SECcurve::generator_secp256k1();
		$n = $g->getOrder();

		$seed = pack("H*", $seed);
		$I = hash_hmac('sha512', $seed, "Bitcoin seed");
		$I_l = substr($I, 0, 64);
		
		$g_l = gmp_init($I_l, 16);
		$_equal_zero = gmp_cmp($g_l, gmp_init(0,10));
		$_GE_n = gmp_cmp($g_l, $n);
		if(	$_equal_zero == 0 ||
			$_GE_n == 1 ||
			$_GE_n == 0 )		
			return FALSE;
			
		$I_r = substr($I, 64, 64);
		return array('k' => $I_l,
					 'c' => $I_r);
	}

	public static function build_address($master, $i_0, $i_1, $i_2) {
		$p_0 = FALSE;
		$p_1 = FALSE;
		$p_2 = FALSE;
		
		if(strpos($i_0, "'") !== 0){
			$i_0 = str_replace($i_0, "'", '');
			$p_0 = TRUE;
		}
		if(strpos($i_1, "'") !== 0){
			$i_1 = str_replace($i_1, "'", '');
			$p_1 = TRUE;
		}
		if(strpos($i_2, "'") !== 0){
			$i_2 = str_replace($i_2, "'", '');
			$p_2 = TRUE;
		}
		$i_0 = 0x80000000+dechex($i_0);
		$i_1 = 0x80000000+dechex($i_1);
		$i_2 = 0x80000000+dechex($i_2);
		return BIP32::CKD(BIP32::CKD(BIP32::CKD($master, $i_2), $i_1), $i_0);
	}

	/**
	 * CKD
	 * 
	 * Child Key Derivation
	 * 
	 * @param	array	$parent
	 * @param	int	$i
	 * @param	boolean	$is_prime
	 * @return	array
	 */
	public static function CKD($parent, $i, $is_prime = FALSE, $priv_deriv = TRUE) {
	
		$key = $parent['k'];
		$chain = $parent['c'];
		
		if($highest == 1) {	
			if($is_prime == TRUE)
				return FALSE;
				
			$data = '00'.$key.$i;
		} else if($highest == 0) {
			$public_key = BitcoinLib::private_key_to_public_key($key, TRUE);
			$data = $public_key.$i;
		} else {
			return FALSE;
		}
		$I = hash_hmac('sha512', $data, $chain);
		$I_l = substr($I, 0, 64);
		$I_r = substr($I, 64, 64);
		
		$g = SECcurve::generator_secp256k1();
		$n = $g->getOrder();
		
		if($priv_deriv == TRUE){
			$k = gmp_strval(
					gmp_Utils::gmp_mod2(
						gmp_add(
							gmp_init($I_l, 16),
							gmp_init($key, 16)
						),
						$n	
					),
					16
				);
		} else {
			$curve = SECcurve::curve_secp256k1();
			
			// $I_l * G
			$offset = Point::mul($I_l, $g);
			
			$decompressed = BitcoinLib::decompress_public_key($key);
			$public_key_point = new Point($curve, $decompressed['x'], $decompressed['y'], $n);
			// Add offset 
			$new_point = Point::add($offset, $public_key_point);
			$k = BitcoinLib::compress_public_key('04'.$new_point->getX().$new_point->getY());
		}
		return array('k' => $k, 
					 'c' => $I_r);
	}
	// LONG UNSIGNED INTEGER - N
	public static function _subkey($parent, $i, $is_prime = FALSE, $as_private = FALSE) {
		$i_32 = pack("N", $i);
		
		print_r($i_32); echo "\n\n--";
		print_r(bin2hex($i_32)); echo "\n\n--";
	}
	public function pubckd($parent, $i) {
		
		$I = hash_hmac('sha512', $data, $chain);
		$I_l = substr($I, 0, 64);
		$I_r = substr($I, 64, 64);
		
		$g = SECcurve::generator_secp256k1();
		$n = $g->getOrder();

			$curve = SECcurve::curve_secp256k1();
			
			// $I_l * G
			$offset = Point::mul($I_l, $g);
			
			$decompressed = BitcoinLib::decompress_public_key($key);
			$public_key_point = new Point($curve, $decompressed['x'], $decompressed['y'], $n);
			// Add offset 
			$new_point = Point::add($offset, $public_key_point);
			$k = BitcoinLib::compress_public_key('04'.$new_point->getX().$new_point->getY());
		return array('k' => $k, 
					 'c' => $I_r);
	}


	public static function CKD_pub($master, $i) {
		$public_key = $master['k'];
		$chain = $master['c'];
		
		$highest == '0';
		if($highest == 1) {
			return FALSE;
		} else if($highest == 0) {
			$data = $public_key.$i;
		} else {
			return FALSE;
		}
		
		$curve = SECcurve::curve_secp256k1();
		$generator = SECcurve::generator_secp256k1();
		$n = $generator->getOrder();		
		
		$I = hash_hmac('sha512', $data, $chain);
		$I_l = substr($I, 0, 64);
		$I_r = substr($I, 64, 64);
		$offset = BitcoinLib::private_key_to_public_key($I_l, TRUE);
		$offset_d = BitcoinLib::decompress_public_key($fo);
		$offset_point = new Point($curve, $offset_d['x'], $offset_d['y'], $n);
		
		$decompressed = BitcoinLib::decompress_public_key($public_key);
		$public_key_point = new Point($curve, $decompressed['x'], $decompressed['y'], $n);

		$new_point = Point::add($new_key_point, $public_key_point);
		$new_point = BitcoinLib::compress_public_key('04'.$new_point->getX().$new_point->getY());
		return array('k' => $new_point,
					 'c' => $I_r);
	}
};
