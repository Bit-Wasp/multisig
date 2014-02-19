<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Enter 2 Pub Keys</title>

	<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body{
		margin: 0 15px 0 15px;
	}
	
	p.footer{
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}
	
	#container{
		margin: 10px;
		border: 1px solid #D0D0D0;
		-webkit-box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>

<div id="container">
	<h1>Add Public Keys</h1>

	<div id="body">
		<p>Enter two public key's in order to create the 2-of-3 signature p2sh address. Compressed, and uncompressed keys are supported. It is recommended to use a new private/public key-pair each time when creating a multisignature address. When you sign a transaction redeeming funds in a multisignature address, you expose your public key on the block chain. Repeated use of the same keypair when signing can lead to opportunistic attacks that could compromise your private key.</p>
		<?php echo form_open('multisig2of3'); ?>
			Public Key 1 : <input type='text' name='public_key1' value='' /><br />
			Public Key 2 : <input type='text' name='public_key2' value='' /><br />
			Public Key 3 : <input type='text' name='public_key3' value='<?php echo $public_key3; ?>' /><br />
			<input type='submit' name='submit_public_keys' value='Submit' />
		</form>

<h3>How to Generate a Public Key</h3>

		<b>Online:</b> Visit <?php echo anchor('https://coinb.in/multisig', "Coinb.in's multisig tool"); ?> or <?php echo anchor('http://brainwallet.org', 'Brainwallet'); ?> and to generate a new key pair. If you wish to sign using this key in Bitcoind/Bitcoin-QT, you can import it using the following snippet. This will import the key to the default account (the "" parameter), and will not trigger a re-scan of the blockchain for transactions (the "false" parameter).
<pre>> importprivkey [private WIF] "" false</pre><br />

		<b>Using Bitcoin-QT:</b>
		Click Help - Debug Window. Click on the Console tab, and run the following commands:
<pre>> getnewaddress
mhv2RQGndPeg2f2UykXeHASJYuLGyLUGXt

> validateaddress mhv2RQGndPeg2f2UykXeHASJYuLGyLUGXt
{
	"isvalid" : true,
	"address" : "mhv2RQGndPeg2f2UykXeHASJYuLGyLUGXt",
	"ismine" : true,
	"isscript" : false,
	"pubkey" : "039557a252bcf93dd2de20059582659c5736cbb19bd8af631dcc31eacc5f1928c6",
	"iscompressed" : true,
	"account" : ""
}
</pre><br />		

		<b>Using BitcoinD:</b> If you are calling bitcoind from the command-line, prefix the commands for Bitcoin-QT with bitcoind:
<pre>$ bitcoind getnewaddress
mhv2RQGndPeg2f2UykXeHASJYuLGyLUGXt

$ bitcoind validateaddress mhv2RQGndPeg2f2UykXeHASJYuLGyLUGXt
{
	"isvalid" : true,
	"address" : "mhv2RQGndPeg2f2UykXeHASJYuLGyLUGXt",
	"ismine" : true,
	"isscript" : false,
	"pubkey" : "039557a252bcf93dd2de20059582659c5736cbb19bd8af631dcc31eacc5f1928c6",
	"iscompressed" : true,
	"account" : ""
}
</pre>	

	</div>

	<p class="footer">This site should only be used for test transactions. Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>
