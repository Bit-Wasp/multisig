multisig
========

Demonstration of multi-signature transactions on a web application.

This project is a PHP/CodeIgniter application, which allows users to test a multisignature transaction. Currently offers 2 of 2, and 2 of 3 signature p2sh addresses. 

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


