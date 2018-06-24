<?php
	
	header('Content-Type: application/json');
	
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	$limit = 100000;
	$pageSize = 500;
	
	$records = 5000;
	$nameFilter = "";
	
	if (isset($_GET['records'])){
		$records = min($limit, $_GET['records']);
		$pageSize = min($pageSize, $records);
	}
	
	$json = [];
	
	$page = 1;
	
	$players = [];
	
	$display = [
		'time' => [
			'oldest' => -1,
			'newest' => -1
		],
		'bounds' => [
			'lowest' => -1,
			'highest' => -1
		]
	];
	
	$firstLoop = true;
	while (((array_key_exists("data", $json) && $json['data']) || $firstLoop) && $page*$pageSize <= $records){
		
		$fetcher = "https://api.faforever.com/data/player?"
		."fields[player]=login,userAgent,createTime,id&"
		.$nameFilter
		."sort=-createTime&"
		."page[size]=".$pageSize."&"
		."page[number]=".$page."";
		
		$string = file_get_contents($fetcher);
	
		$json = json_decode($string, true);
		
		/// 1 : Sorting players
		foreach($json['data'] as $thisPlayer){
			$timeStart = strtotime($thisPlayer['attributes']['createTime']);
			if ($display['time']['oldest'] == -1){
				$display['time']['oldest'] = $timeStart;
			}
			$display['time']['newest'] = max($timeStart, $display['time']['newest']);
			$display['time']['oldest'] = min($timeStart, $display['time']['oldest']);
			
			$time = strtotime($thisPlayer['attributes']['createTime']);
			$day = floor($time/86400);
			
			$players[$day][] = array(
				"id"=>$thisPlayer['id'],
				"login"=>$thisPlayer['attributes']['login'],
				"userAgent"=>$thisPlayer['attributes']['userAgent'],
				"createTime"=>$time,
			);
		}
		
		$page++;
	}
	
	foreach($players as $day){
		if (count($day) > $display['bounds']['highest']){
			$display['bounds']['highest'] = count($day);
		}
		if ($display['bounds']['lowest'] == -1 || count($day) < $display['bounds']['lowest']){
			$display['bounds']['lowest'] = count($day);
		}
	}
	
	$display['players'] = $players;
	ksort($display['players']);
	
	$records = 0;
	foreach($display['players'] as $plays){
		$records += count($plays);
	}
		
	
	$display['meta'] = [
		'records'=>$records,
		'fetcher'=>$fetcher
	];
	
	if (array_key_exists('errors', $json)){
		$display['meta']['errors'] = $json['errors'][0];
	}
	
	echo json_encode($display);
	exit;
?>