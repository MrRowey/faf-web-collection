<?php
	
	header('Content-Type: application/json');

	$players = "";
	
	if (isset($_GET['players']) && $_GET['players'] != ""){
		$players  = $_GET['players'];
	}
	else{
		exit;
	}
	
	$display = ['ladder'=>array(), 'global'=>array()];
	$display['ladder']["players"] = array();
	$display['ladder']['time'] = [
			'oldest' => -1,
			'newest' => -1
		];
	
	$playersArr = explode(',', $players);
	
	// First for ladder, then for normal
	
	/// ladder
	foreach($playersArr as $playerName){
		
		$fetcher = "https://api.faforever.com/data/gamePlayerStats?"
		."filter=player.login==".$playerName.";game.featuredMod.id==6&"
		."fields[gamePlayerStats]=beforeMean,beforeDeviation,scoreTime&"
		."page[size]=1&"
		."sort=-scoreTime";
		
		$string = file_get_contents($fetcher);
		$json = json_decode($string, true);
		
		$display['ladder']["players"][$playerName] = array(
			"mean"=>$json['data'][0]['attributes']['beforeMean'],
			"deviation"=>$json['data'][0]['attributes']['beforeDeviation']
		);
		
		$time = strtotime($json['data'][0]['attributes']['scoreTime']);
		if ($display['ladder']['time']['oldest'] == -1){
			$display['ladder']['time']['oldest'] = $time;
		}
		$display['ladder']['time']['newest'] = max($time, $display['ladder']['time']['newest']);
		$display['ladder']['time']['oldest'] = min($time, $display['ladder']['time']['oldest']);
	}
	
	$display['ladder']['meta'] = [
		'players'=>$playersArr,
		'fetcher'=>$fetcher
	];
	
	$display['global']["players"] = array();
	$display['global']['time'] = [
			'oldest' => -1,
			'newest' => -1
		];
		
	/// GLOBAL
	foreach($playersArr as $playerName){
		
		$fetcher = "https://api.faforever.com/data/gamePlayerStats?"
		."filter=player.login==".$playerName.";game.featuredMod.id!=6&"
		."fields[gamePlayerStats]=beforeMean,beforeDeviation,scoreTime&"
		."page[size]=1&"
		."sort=-scoreTime";
		
		$string = file_get_contents($fetcher);
		$json = json_decode($string, true);
		
		$display['global']["players"][$playerName] = array(
			"mean"=>$json['data'][0]['attributes']['beforeMean'],
			"deviation"=>$json['data'][0]['attributes']['beforeDeviation']
		);
		
		$time = strtotime($json['data'][0]['attributes']['scoreTime']);
		if ($display['global']['time']['oldest'] == -1){
			$display['global']['time']['oldest'] = $time;
		}
		$display['global']['time']['newest'] = max($time, $display['global']['time']['newest']);
		$display['global']['time']['oldest'] = min($time, $display['global']['time']['oldest']);
	}
	
	$display['global']['meta'] = [
		'players'=>$playersArr,
		'fetcher'=>$fetcher
	];
	
	echo json_encode($display);
	exit;
?>