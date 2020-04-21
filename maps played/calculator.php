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
	
	$mapVersions = [];
	$maps = [];

	$fetcher = "https://api.faforever.com/data/game?sort=-endTime&filter=playerStats.player.login==". $playerName .
	"&include=mapVersion,mapVersion.map&fields[game]=mapVersion&fields[mapVersion]=map,thumbnailUrlLarge&fields[map]=displayName&page[limit]=10000";
	
	$string = file_get_contents($fetcher);

    function getHttpCode($http_response_header)
    {
        if(is_array($http_response_header))
        {
            $parts=explode(' ',$http_response_header[0]);
            if(count($parts)>1) //HTTP/1.0 <code> <text>
                return intval($parts[1]); //Get code
        }
        return 0;
    }


    $code=getHttpCode($http_response_header);

    if($code != 200){
        http_response_code($code);
        exit;
    }

	$json = json_decode($string, true);
	
	foreach($json['data'] as $thisGame){
        $data = $thisGame['relationships']['mapVersion']['data'];
        if(!isset($data)){
            continue;
        }
        $mapVersionId = $data['id'];
		if(!isset($mapVersionId)){
		    continue;
        }
		if(!array_key_exists( $mapVersionId, $mapVersions)){
		    $mapVersions[$mapVersionId] = 1;
        }else{
            $mapVersions[$mapVersionId]++;
        }
	}
	foreach($json['included'] as $thisInclude){
		$id = $thisInclude['id'];
		$type = $thisInclude['type'];

        if($type == 'mapVersion'){
            $mapId = $thisInclude['relationships']['map']['data']['id'];
            $imageUrl = $thisInclude['attributes']['thumbnailUrlLarge'];
            if(!array_key_exists($mapId, $maps)){
                $maps[$mapId] =[
                    "count" => $mapVersions[$id],
                    "imageUrl" => $imageUrl
                ];
            }else {
                $maps[$mapId]["count"] += $mapVersions[$id];
            }
            continue;
        }

        if($type == 'map'){
            $displayName = $thisInclude['attributes']['displayName'];
            $maps[$id]["name"] = $displayName;
        }

	}

	usort($maps, function($a, $b){
        return $b["count"] -$a["count"];
    });

	echo json_encode($maps);
	exit;
