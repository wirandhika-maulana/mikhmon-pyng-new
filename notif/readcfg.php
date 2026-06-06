<?php
	$fcon = "../include/config.php";	
	$fses = "session.log";	
	if (file_exists($fcon)) {
		include $fcon;		
		if (file_exists($fses)) {
			$sess	=ltrim(file_get_contents('session.log'));
		}else{
			$sess	="No Setting";
		}
		$user	=explode('@|@',$data[$sess][2])[1];
		$pass	=decrypt(explode('#|#',$data[$sess][3])[1]);
		$vpn	=explode(':',explode('!',$data[$sess][1])[1])[0];
		$port	=explode(':',explode('!',$data[$sess][1])[1])[1];
		$dns1	=explode('^',$data[$sess][5])[1];
		$dns2	=explode('%',$data[$sess][4])[1];
		$dns	=$sess;
	}else{
		$hasil="file tidak ada\n".$fcon;
		return $hasil;
	}
?>
