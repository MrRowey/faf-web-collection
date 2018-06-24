<?php
	
	if (!isset($_GET['playername'])){
		return;
	}
	
	$name = $_GET['playername'];
	
	$debug = '';
	
	$returnedString = file_get_contents("https://api.faforever.com/data/game?filter=playerStats.player.login==\"".$name."\"&sort=-endTime&fields[game]=startTime,endTime&page[number]=1");
	
	$json = json_decode($returnedString, true);
	$debug  .=  "Page 1 : ".count($json['data'])." results\n";
	$finalData = $json;
	
	if (count($json['data']) <= 0){
		echo -2;
		return;
	}
	
	$i = 2;
	while (count($json['data']) > 0){
		$string = file_get_contents("https://api.faforever.com/data/game?filter=playerStats.player.login==\"".$name."\"&sort=-endTime&fields[game]=startTime,endTime&page[number]=".$i."");
		$json = json_decode($string, true);
		if (count($json['data']) > 0){
			$finalData = array_merge_recursive($finalData, $json);
		}
		
		$debug  .=  "Page ".$i." : ".count($json['data'])." results\n";
		
		$i ++;
	}
	
	$total = 0;
	
	
	foreach($finalData['data'] as $thisGame){
		$start = $thisGame['attributes']['startTime'];
		$end = $thisGame['attributes']['endTime'];
		
		$start = strtotime($start);
		$end = strtotime($end);
		
		$result = max($end-$start, 0);
		
		$total += $result;
	}
	/*
	echo count($finalData['data'] ).' games total'."\n";
	echo $debug;
	exit;
	
	*/
	echo $total;
	
?>