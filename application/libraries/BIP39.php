<?php

class BIP39 {

	public static function seed($size = 128) {
		echo "ENT $size\n";
		$seed = bin2hex(openssl_random_pseudo_bytes($size/2));
		
		$hash = hash('sha256', $seed);
		$checksum = substr($hash, 0, $size/32);
		echo "cs $checksum\ncs length ".strlen($checksum)."\n";
		
		$seed_check = $seed.$checksum;
		$length = strlen($seed_check);
		
		$words = str_split($seed_check, 5.5);
		var_dump($words);
		
		foreach($words as &$word) {
			$word = gmp_strval(gmp_init($word, 16), 10);
		}
		print_r($words);
	}
	
};


