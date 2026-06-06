<?php
// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
//	header("Location:../admin.php?id=login");
} else {
/*		$getNet = $API->comm("/tool/netwatch/print");
		$TotalReg = count($getNet);
		$countNet = $API->comm("/tool/netwatch/print", array(
			"count-only" => ""
		));
*/
	echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";
//redirect to netwatch
}
echo "<script>window.location='../?interface=netwatch&session=".$_POST['session']."'</script>";

?>
