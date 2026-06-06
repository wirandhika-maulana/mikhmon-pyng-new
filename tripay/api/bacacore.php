<?php
$fcore="TriPay.Log";
if (!file_exists($fcore)) {
    echo "file web hook tidak ditemukan, ".$fcore." \n";
    exit(0);
}
$fdata=explode("|#",file_get_contents($fcore));
$tul="";
for ($x=0;$x < count($fdata)-1;$x++) {
    $tul    .=$fdata[$x]."# \n";
}
echo $tul;
?>