<?php
	
	header('Content-Type: application/json');

	/// Don't touch
	$limit = 100000;
	$pageSize = 500;
	
	$records = 5000;
	$nameFilter = "";
	$top = 20;
	
	if (isset($_GET['records'])){
		$records = min($limit, $_GET['records']);
		$pageSize = min($pageSize, $records);
	}
	if (isset($_GET['top'])){
		$top = $_GET['top'];
	}
	if (isset($_GET['namefilter']) && $_GET['namefilter'] != ""){
		$nameFilter = "filter=playerStats.player.login==".$_GET['namefilter']."&";
	}
	
	$json = [];
	
	$page = 1;
	
	$games = [];
	$includes = [];
	$topNames = [];
	
	$display = [
		'time' => [
			'oldest' => -1,
			'newest' => -1
		]
	];
	
	$firstLoop = true;
	while (($json['data'] || $firstLoop) && $page*$pageSize <= $records){
		
		$firstLoop = false;
		
		$fetcher = "https://api.faforever.com/data/game?"
		."include=featuredMod,playerStats,playerStats.player&"
		."fields[game]=name,startTime,featuredMod&"
		."fields[player]=login&"
		."fields[featuredMod]=displayName&"
		."fields[map]=displayName&"
		.$nameFilter
		."sort=-startTime&"
		."page[size]=".$pageSize."&"
		."page[number]=".$page."";
		
		$string = file_get_contents($fetcher);
	
		$json = json_decode($string, true);
		
		foreach($json['data'] as $thisGame){
			$games["All modes"][] = $thisGame;
		}
		foreach($json['included'] as $thisInclude){
			$includes[$thisInclude['type']][$thisInclude['id']] = $thisInclude;	/// map ["featuredMod"][0] = object
		}
		
		$page++;
	}
		
	/// 1 : Sorting games
	foreach($games["All modes"] as $thisGame){
		$modId = $thisGame['relationships']['featuredMod']['data']['id'];
		$modName = $includes['featuredMod'][$modId]['attributes']['displayName'];
		
		$games[$modName][] = $thisGame;
		
		$timeStart = strtotime($thisGame['attributes']['startTime']);
		if ($display['time']['oldest'] == -1){
			$display['time']['oldest'] = $timeStart;
		}
		$display['time']['newest'] = max($timeStart, $display['time']['newest']);
		$display['time']['oldest'] = min($timeStart, $display['time']['oldest']);
	}
	
	
	// 2 : Top maps
	foreach($games as $category=>$featuredMod){
		foreach($featuredMod as $featuredModId=>$thisGame){
			$featuredModName = $category;
			$name = $thisGame['attributes']['name'];
			if (strlen($name) <= 0){
				continue;
			}
			
			if (!array_key_exists($featuredModName, $topNames)){
				$topNames[$featuredModName] = [];
			}
			if (!array_key_exists($name, $topNames[$featuredModName])){
				$topNames[$featuredModName][$name] = 0;
			}
			$topNames[$featuredModName][$name]++;
		}
	}
	
	// 3 : Ordering maps and trimming to $top, then display
	foreach($topNames as $featuredModName=>$featuredMod){
		
		$display['plays'][$featuredModName]['stats'] = [
			'totalGames'=>array_sum($featuredMod)			
		];
		arsort($featuredMod);
		$featuredMod = array_slice($featuredMod, 0, $top, true);
		
		foreach($featuredMod as $gameName=>$occurences){
			
			$plays = $occurences;
			$display['plays'][$featuredModName]['games'][] = [
				"gameName"=>$gameName,
				"played"=>$plays
			];
		}
	}

	$display['meta'] = [
		'records'=>count($games['All modes']),
		'top'=>$top,
		'nameFilter'=>$_GET['namefilter'],
		'fetcher'=>$fetcher
	];
	
	if (array_key_exists('errors', $json)){
		$display['meta']['errors'] = $json['errors'][0];
	}
	
	echo json_encode($display);
	exit;
?>