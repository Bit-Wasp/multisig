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
		<div wid
		The following transaction spends the transaction that paid into the multi-signature address, paying the money back to the address you specified before. <br />
		<b>Unsigned Transaction</b><br />
		<table>
			<thead>
				<th>Bitcoind</th>
				<th>Electrum</th>
			</thead>
			<tbody>
				<tr>
					<td><textarea cols='45' rows='3'>signrawtransaction <?php echo $newrawtransaction; ?></textarea></td>
					<td><textarea cols='45' rows='3'>signrawtransaction("<?php echo $newrawtransaction; ?>","<?php echo $json_input; ?>",[""])</textarea></td>
				</tr>
			</tbody>
		</table>
		
		<b>JSON</b><br />
		<textarea cols='90' rows='3'><?php echo $json; ?></textarea><Br /><br />
		
		<b>Signed Transaction</b><br />
		<textarea cols='90' rows='3'>signrawtransaction <?php echo $signedtransaction['hex']; ?></textarea><br />
		<hr />
		
		<?php echo form_open('broadcast'); ?>
			Paste your signed transaction, or the transaction ID in this box:<Br />
			<textarea name='input' cols='90' rows='3'></textarea><br />
			<input type='submit' value='Submit' />
		</form>
	</div>

	<p class="footer">This site should only be used for test transactions. Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>
