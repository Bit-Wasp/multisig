<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Sign Transaction</title>

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
	<h1>Address: <?php echo $address; ?></h1>
	
	<div id="body">
		<p>The following transaction spends the funds sent to the multi-signature address - sending BTC <?php echo ($value-0.0001); ?> from <?php echo $address; ?> to <?php echo $destination; ?>. Once the transaction is broadcast and mined, it will be found by the site and marked as complete.</p>
		<p><?php if($n == 3) echo "As this is a 2-of-3 address, once you have partially signed the transaction, you need to communicate it to the other person you're transacting with and so it can be signed and broadcast."; ?></p>
		
		<table>
			<tbody>
				<tr>
					<td>Unsigned Transaction</td>
					<td><textarea cols='70' rows='3'>signrawtransaction <?php echo $newrawtransaction; ?> '<?php echo $json_inputs; ?>'</textarea></td>
				</tr>
				<tr>
					<td>Signed Transaction </td>
					<td><textarea cols='70' rows='3'>signrawtransaction <?php echo $signedtransaction['hex']; ?></textarea></td>
				</tr>
			</tbody>
		</table>
	</div>

        <p class="footer">This site should only be used for test transactions. Page rendered in <strong>{elapsed_time}</strong> seconds. 
        <?php echo anchor('https://github.com/Bit-Wasp/multisig', 'Download this code.'); ?></p>

</div>

</body>
</html>
