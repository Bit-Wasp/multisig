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
		<p>Now you need to import and pay to the multisignature address. Send a small amount, 0.0002 BTC for example to the address. Verify the redeemScript corresponds to these to public keys yourself!</p>
		<p>You should bookmark <?php echo anchor('welcome/id/'.$multisig['unique_id'], 'this url'); ?> in case you want to visit this page later.</p>
		
		<b>Address:</b> <?php echo $multisig['address']; ?><br />
		<b>Redeem Script:</b> <?php echo $multisig['redeemScript']; ?><br /><br />

		<b>Add Multisig Address</b><br />
		You will need to tell your client about the multisignature address so it can reconstruct the redeemScript later on when spending funds.
		<table>
			<thead>
				<th>Bitcoind / Bitcoin-QT</th>
				<th></th>
			</thead>
			<tbody>
				<tr>
					<?php
					$pubkey_str = "";
					foreach($pubkeys as $pubkey){
						$pubkey_str .= "\"{$pubkey}\",";
					}
					$pubkey_str = substr($pubkey_str, 0, (strlen($pubkey_str)-1));
					?>
					<td><textarea cols='45' rows='3'><?php echo "addmultisigaddress 2 '[{$pubkey_str}]'"; ?></textarea></td>
				</tr>
			</tbody>
		</table>
		<br />

		<?php echo form_open('multisig2of'.$n.'/pay'.$n.'address'); ?>
			<p>Now pay to the multi-signature address. Transactions are displayed once mined in a block, but you can only spend them once <?php echo $minimum_confirmations; ?> confirmations are reached for all transactions.</p>
			Enter an address to pay the money back to:	<br />
			<input type='text' name='destination' value='' /><br />
			
			<?php if($payments !== FALSE) {
				$display_button = TRUE;
				echo '<pre>';
				foreach($payments as $pmt) {
					$confirmations = $current_block-$pmt['block_height']+1;
					echo "BTC {$pmt['value']}   (block {$pmt['block_height']})   {$pmt['tx_id']} \n";
					echo "    $confirmations confirmations\n\n";
					if($confirmations < $minimum_confirmations)
						$display_button = FALSE;
				}
				echo "</pre>\n";
				if($display_button == TRUE)
					echo "<input type=\"submit\" value=\"Submit\" />\n";
					
		} else {
			echo '<br />Make payment to the address to proceed.';
		}?>
		</form>

	</div>

	<p class="footer">This site should only be used for test transactions. Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>

