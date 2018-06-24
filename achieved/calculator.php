<?php
	
	header('Content-Type: application/json');

	$playerName = "";
	
	if (isset($_GET['playername']) && $_GET['playername'] != ""){
		$playerName  = $_GET['playername'];
	}
	else{
		exit;
	}
	
	$json = [];
	
	$achievements = [];
	
	$display = [];
	$fetcher = "https://api.faforever.com/data/playerAchievement?"
	."filter=player.login==".$playerName."&"
	."include=achievement&"
	."fields[playerAchievement]=achievement,state,updateTime,currentSteps&"
	."fields[achievement]=description,name,unlockedIconUrl,totalSteps";
	
	$string = file_get_contents($fetcher);
	$json = json_decode($string, true);
	
	foreach($json['data'] as $thisPlayerAch){
		$achId = $thisPlayerAch['relationships']['achievement']['data']['id'];
		$paState = $thisPlayerAch['attributes']['state'];
		$paCurrentSteps = $thisPlayerAch['attributes']['currentSteps'];
		$paUpdateTime = $thisPlayerAch['attributes']['updateTime'];
		
		$achievements[$achId] = [
			"state" => $paState,
			"currentSteps" => $paCurrentSteps,
			"updateTime" => $paUpdateTime
		];
	}
	foreach($json['included'] as $thisInclude){
		$achId = $thisInclude['id'];
		$achName = $thisInclude['attributes']['name'];
		$achDescription =  $thisInclude['attributes']['description'];
		$achTotalSteps =  $thisInclude['attributes']['totalSteps'];
		$achIcon =  $thisInclude['attributes']['unlockedIconUrl'];
		
		$toMerge = [
			"name" => $achName,
			"description" => $achDescription,
			"totalSteps" => $achTotalSteps,
			"icon" => $achIcon
		];
		$achievements[$achId] = array_merge($achievements[$achId], $toMerge);
	}
	
	$achievementFetcher = "https://api.faforever.com/data/achievement?"
	."fields[achievement]=description,name,unlockedIconUrl,totalSteps";
	
	$string = file_get_contents($achievementFetcher);
	$json = json_decode($string, true);
	
	foreach($json['data'] as $thisAch){
		$achId = $thisAch['id'];
		if (!array_key_exists($achId, $achievements)){
			$achievements[$achId] = [
				"state" => "LOCKED",
				"currentSteps" => 0,
				"updateTime" => 0,
				"name" => $thisAch['attributes']['name'],
				"description" => $thisAch['attributes']['description'],
				"totalSteps" => $thisAch['attributes']['totalSteps'],
				"icon" => $thisAch['attributes']['unlockedIconUrl']
			];
		}
	}
	
	foreach($achievements as $id=>$ach){
		$ach['id']=$id;
		$display['achievements'][]= $ach;
	}
	
		
	$display['meta'] = [
		'achievements'=>count($achievements),
		'playerName'=>$_GET['playername'],
		'fetcher'=>$fetcher
	];
	
	echo json_encode($display);
	exit;
?>