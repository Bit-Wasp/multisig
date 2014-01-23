<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Escrow Testing</title>

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
	<h1>Escrow Testing</h1>

	<div id="body">
		This site demonstrates 2 of 2, and 2 of 3 signature transactions in a simple way. You will be asked to send funds to a multisignature address, where the funds can only be released by signing the transaction to the address you chose. When done in this way, funds are not stored on a live wallet, and cannot be compromised by hacking this server.<Br />
		<Br />
		<b>2 of 2 addresses</b><br />
		These transaction's can be used as a two-factor bitcoin topup system. By sending funds two a 2 of 2 address, where the other key is held by a website, sending funds on to a final destination would require the signature from your local bitcoin client. This is a far greater solution to site's which just have a bitcoin topup feature, which has lead to several high profile heists. Multi-signature addresses can mitigate this risk by not requiring a live wallet.<br />
		<?php echo anchor('multisig2of2', 'Test a 2-of-2 signature transaction'); ?><Br />
		
		<br />
		<b>2 of 3 addresses</b><br />
		These transactions are used as escrow transactions with a trusted third party. Funds spent from a 2 of 3 address require 2 of the 3 signatories to sign before releasing the funds. In cases where mediation may be required, between a buyer, seller, and third party, a successful transaction will result in the buyer and seller signing the payment to the seller. If there are problems, a new transaction can be drafted paying either the buyer/seller the funds. If either the buyer or seller disappears, then the third party can step in and sign the transaction.<Br />
		<?php echo anchor('multisig2of3', 'Test a 2-of-3 signature transaction'); ?><Br />
		
	</div>

	<p class="footer">This site should only be used for test transactions. Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>
