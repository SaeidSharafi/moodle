<?php
if($cost == "") {
  header("location:/");
  exit;
}
if(isset($_POST["acecr"])) {
	  echo '<div style="position:absolute;left:0;top:0;width:100%;height:100%;background:#F9F9F9;z-index:10000001;"></div>';
	  error_reporting(E_ALL);
	  $acecr = new enrol_acecr_plugin();
	  $acecr->startPayment($cost, $CFG->wwwroot."/enrol/acecr/callback.php?id=".$course->id);
}
else {
	$orderid = rand(100000,999999);
?>
<div align="center">
	<p><?php print_string("paymentrequired") ?></p>
	<p><b><?php echo "قیمت : {$localisedcost} ریال"; ?></b></p>
	<p><img alt="<?php print_string('acecraccepted', 'enrol_acecr') ?>" src="<?php echo "$CFG->wwwroot/enrol/acecr/pix/Acecr.png" ?>" /></p>
	<p><?php print_string("paymentinstant") ?></p>
	<form action="" method="post">
		<input type="hidden" name="cmd" value="_xclick" />
		<input type="hidden" name="charset" value="utf-8" />
		<input type="hidden" name="order_id" value="<?php p($orderid) ?>">
		<input type="submit" name="acecr" value="<?php print_string("sendpaymentbutton", "enrol_acecr") ?>" />
	</form>
</div>
<?php } ?>