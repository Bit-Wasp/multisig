<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Pay to Multisig Address</title>

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
	<h1>Pay to Multisig Address: <?php echo $multisig['address']; ?></h1>

	<div id="body">
		<b>Address:</b> <?php echo $multisig['address']; ?><br />
		<b>Redeem Script:</b> <?php echo $multisig['redeemScript']; ?><br /><br />

		Now you need to pay to the multisignature address. Send a small amount, 0.0002 for example to the above multisignature address.<br />
		
		<b>Add Multisig Address</b><br />
		You will need to tell your client about the multisignature address so it can reconstruct the redeemScript later on when spending funds.
		<table>
			<thead>
				<th>Bitcoind / Bitcoin-QT</th>
				<th></th>
			</thead>
			<tbody>
				<tr>
					<?php 	$pubkey_str = '';
							foreach($pubkeys as $pubkey){
								$pubkey_str .= "\"$pubkey\",";
							}
							$pubkey_str = substr($pubkey_str, 0, (strlen($pubkey_str)-1));
					?>
					<td><textarea cols='45' rows='3'><?php echo "addmultisigaddress 2 '[{$pubkey_str}]'"; ?></textarea></td>
				</tr>
			</tbody>
		</table>
		<br />

		<?php echo form_open('multisig2of'.$n.'/pay'.$n.'address'); ?>
			Enter the transaction ID for the payment to this address: <br />
			<input type='text' name='txid' value='' /><br />

			Enter an address to pay the money back to:<br />
			<input type='text' name='destination' value='' /><br />
			<input type='submit' value='Submit' />
		</form>

	</div>

	<p class="footer">This site should only be used for test transactions. Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>
