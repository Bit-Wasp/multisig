multisig
========

Demonstration of multi-signature transactions on a web application.

This project is a PHP/CodeIgniter application, which allows users to test a multisignature transaction. Currently offers 2 of 2, and 2 of 3 signature p2sh addresses. 

It also includes some libraries with implementations of BIP32, electrum, and some key manipulation functions, as well as a jsonRPCclient for CodeIgniter, a Bitcoin API wrapper for this library and CodeIgniter. It also has a CodeIgniter library to assist in generating raw transactions.

Requirements:
- Bitcoind
   - Bitcoind must be maintaining a full transaction index (txindex=1 in bitcoin.conf)

Users enter 1, or 2 public keys, while the serve will generate a new keypair for the final public key. 
A multisignature address will be generated, and the user is asked to add it to their wallet.
They must then pay to the multisignature address, and enter the transaction ID for the payment, and enter a bitcoin address to send the funds to afterwards.
They will then be shown a transaction which spends the input in the multisignature address (after deducting 0.0001 BTC to pay a fee). This transaction will be signed by the server in all cases, so you only need to supply one furthe signature. 
Once the transaction is fully signed, users can either paste it directly onto the page where it will then be broadcasted, or else they can broadcast it themselves and paste the transaction ID. Either way, the server checks if this was successful or not.

This is experimental software, and has only been successfully tested using bitcoind/bitcoin-qt. Use other clients at your peril. Or, better yet, test it out ANYWAY with a small amount like 0.0002, and let me know if it works.

I feel there should be a simple utility which signs multisignature transactions using the information users are shown on the Sign Transaction page. This would ideally just require the 3 public keys (to generate the multisig address and redeemscript), raw transaction hex, and the inputs for the spending transaction in JSON, along with the private key. It could be a simple GUI application, or even a CLI app, since users are reduced to dealing with the console in the clients anyway..
Please let me know what clients this works with!

Libraries
===

BIP32 implementation library
Electrum implementation library
Bitcoin key processing library
Raw transaction crafting library for CodeIgniter


Examples
===

There is
-  a script with sample usage of the bitcoin library.
-  a script with sample usage of the electrum library.
-  a script with sample usage of the BIP32 library.
-  a script demoing BIP32 key derivation for multisig.

Licence
===
This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a compiled
binary, for any purpose, commercial or non-commercial, and by any
means.

In jurisdictions that recognize copyright laws, the author or authors
of this software dedicate any and all copyright interest in the
software to the public domain. We make this dedication for the benefit
of the public at large and to the detriment of our heirs and
successors. We intend this dedication to be an overt act of
relinquishment in perpetuity of all present and future rights to this
software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to <http://unlicense.org/>

