<?php
	
	header('Content-Type: application/json');
	/*
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	*/
	$limit = 100000;
	$pageSize = 500;
	
	$records = 3000;
	$nameFilter = "";
	
	if (isset($_GET['records'])){
		$records = min($limit, $_GET['records']);
		$pageSize = min($pageSize, $records);
	}
	if (isset($_GET['namefilter']) && $_GET['namefilter'] != ""){
		$nameFilter = "filter=playerStats.player.login==".$_GET['namefilter']."&";
	}
	else{
		exit;
	}
	
	$json = [];
	$playerId = -1;
	
	$nbOfGames = $records;
	$page = 1;
	
	$games = [];
	
	$display = [
		'time' => [
			'oldest' => -1,
			'newest' => -1
		],
		'bounds' => [
			'lowest' => 9999,
			'highest' => -9999
		]
	];
	
	$firstLoop = true;
	while (((array_key_exists("data", $json) && $json['data']) || $firstLoop) && $page*$pageSize <= $records){
		
		$fetcher = "https://api.faforever.com/data/game?"
		."include=playerStats,playerStats.player&"
		."fields[gamePlayerStats]=afterMean,beforeMean,beforeDeviation,afterDeviation,game,player&"
		."fields[game]=startTime,validity,featuredMod,endTime&"
		."fields[player]=login&"
		.$nameFilter
		."sort=-startTime&"
		."page[size]=".$pageSize."&"
		."page[number]=".$page."";
		
		$string = file_get_contents($fetcher);
	
		$json = json_decode($string, true);
		
		/// 0 : Correlating rating with player
		foreach($json['included'] as $include){
			if ($include["type"] == "player" && $include['attributes']['login'] == $_GET['namefilter']){
				$playerId = $include["id"];
			}
		}
		
		/// 1 : Sorting games
		foreach($json['data'] as $thisGame){
			$timeStart = strtotime($thisGame['attributes']['startTime']);
			if ($display['time']['oldest'] == -1){
				$display['time']['oldest'] = $timeStart;
			}
			$display['time']['newest'] = max($timeStart, $display['time']['newest']);
			$display['time']['oldest'] = min($timeStart, $display['time']['oldest']);
		}
		
		
		// 2 : : Correlating rating with player
		foreach($json['data'] as $include){
			if ($include["type"] == "game"){
				$time = strtotime($include['attributes']['startTime']);
				$featuredId = $include['relationships']['featuredMod']['data']['id'];
				if ($include['attributes']['endTime'] == NULL || $include['attributes']['validity'] != "VALID"){
					continue;
				}
				if ($featuredId != 6){
					$featuredId = 0;
				}
				$games[$featuredId][$time] = $include['id'];
			}
		}		
		
		// 2.5 : and sorting gamePlayerStats by game ID
		foreach($json['included'] as $include){
			if ($include["type"] == "gamePlayerStats"){
				$id = $include["relationships"]["game"]["data"]["id"];
				$pId = $include["relationships"]["player"]["data"]["id"];
				if ($pId == $playerId){
					$rating = $include["attributes"]["afterMean"] - $include["attributes"]["afterDeviation"]*3;
					if ($rating != 0){
						$stats[$id] = $rating;
						$display['bounds']['highest'] = max($rating, $display['bounds']['highest']);
						$display['bounds']['lowest'] = min($rating, $display['bounds']['lowest']);
					}
				}
			}
		}
		$page++;
	}
		// 3 : Sorting rating by time
	foreach($games as $featuredId=>$gameList){
		foreach($gameList as $time=>$gameId){
			if (!array_key_exists($gameId, $stats)){
				unset($gameList[$time]);
				continue;
			}
			$gameList[$time] = $stats[$gameId];
		}
		$games[$featuredId] = $gameList;
	}
	$display['plays'] = $games;
	ksort($display['plays']);
	
	$records = 0;
	foreach($display['plays'] as $plays){
		$records += count($plays);
	}
	
	$display['meta'] = [
		'records'=>$records,
		'nameFilter'=>$_GET['namefilter'],
		'fetcher'=>$fetcher
	];
	
	if (array_key_exists('errors', $json)){
		$display['meta']['errors'] = $json['errors'][0];
	}
	
	echo json_encode($display);
	exit;
?>