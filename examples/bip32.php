<?php

require_once(dirname(__FILE__).'/../application/libraries/ecc-lib/auto_load.php');
require_once(dirname(__FILE__).'/../application/libraries/BitcoinLib.php');
require_once(dirname(__FILE__).'/../application/libraries/BIP32.php');

// Load a 128 bit key, and convert this to extended key format.
$master = BIP32::master_key('b861e093a58728ea45b9790af35fb61a');
$def = "0/0/1";

echo "Master key m : $master \n";
// Define what derivation you wish to calculate.
echo "Want m/$def \n";
$key = BIP32::build_key($master, $def);		// Build the extended key

// Display private extended key and the address that's derived from it.
echo "Generated private key: ".$key."\n";
echo BIP32::key_to_address($key)."\n\n";

// Convert the extended private key to the public key, and display the 
// address that's derived from it.
$pub = BIP32::extended_private_to_public($key);
echo "Same public key: ".$pub."\n";
echo BIP32::key_to_address($pub)."\n";
echo "\n";


