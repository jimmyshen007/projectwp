<?php
function getClientIP(){
  if(getenv('HTTP_CLIENT_IP')) { 
   $onlineip = getenv('HTTP_CLIENT_IP');
  } elseif(getenv('HTTP_X_FORWARDED_FOR')) { 
   $onlineip = getenv('HTTP_X_FORWARDED_FOR');
  } elseif(getenv('REMOTE_ADDR')) { 
   $onlineip = getenv('REMOTE_ADDR');
  } else { 
   $onlineip = $_SERVER['REMOTE_ADDR'];
  }
  return $onlineip;
}

function checkChinaIP() {
	$ip = getClientIP();
	$cIP = explode(".", $ip);
	$ipList = split("\n", file_get_contents(__DIR__ . "/china_ip"));
	foreach($ipList as $value) {
		if(!$value){
			continue;
		}
		$ips = explode(" ", $value);
		$minIP = explode(".", $ips[0]);
		$maxIP = explode(".", $ips[1]);
		$match = true;
		foreach ($cIP as $idx => $part){
			if ($part < $minIP[$idx] || $part > $maxIP[$idx]){
				$match = false;
				break;
			}
		}
		if($match){
			return true;
		}
	}
	return false;
        //return true;
}
?>
