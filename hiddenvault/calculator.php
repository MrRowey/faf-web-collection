<?php
	
	/// Hidden vault
	/// Vault for hidden mods and maps
	
	header('Content-Type: application/json');
	
	$maps = [];
	$mods = [];
	
	$display = [];
	$display ['meta'] = array();
	
	/// Fetching maps
	$mapsJson = [];
	$page = 1;
	$firstLoop = true;
	while ($mapsJson['data'] || $firstLoop){
		$firstLoop = false;
		$fetcher = "https://api.faforever.com/data/mapVersion?"
		."sort=-updateTime&"
		."filter=hidden==true&"
		."fields[mapVersion]=downloadUrl,description,createTime,ranked,width,height,map,hidden,ranked,folderName,thumbnailUrlSmall,description,updateTime&"
		."include=map,map.author&"
		."sort=-updateTime&"
		."fields[map]=displayName,author&"
		."fields[player]=login&"
		."page[number]=".$page;
		
		$string = file_get_contents($fetcher);
		$display ['meta']["mapFetcher"] = $fetcher;

		$mapsJson = json_decode($string, true);
		foreach($mapsJson['data'] as $thisMapVersion){
			$mapId = $thisMapVersion['relationships']['map']['data']['id'];
			if (!array_key_exists($mapId, $maps)){
				$maps[$mapId] = array();
			}
			$maps[$mapId][] = array(
				"createTime"=>$thisMapVersion['attributes']['createTime'],
				"description"=>$thisMapVersion['attributes']['description'],
				"downloadUrl"=>$thisMapVersion['attributes']['downloadUrl'],
				"folderName"=>$thisMapVersion['attributes']['folderName'],
				"height"=>$thisMapVersion['attributes']['height'],
				"width"=>$thisMapVersion['attributes']['width'],
				"ranked"=>$thisMapVersion['attributes']['ranked'],
				"updateTime"=>$thisMapVersion['attributes']['updateTime'],
				"thumbnailUrl"=>$thisMapVersion['attributes']['thumbnailUrlSmall'],
			);
		}
		/// Linking maps to display names
		foreach($mapsJson['included'] as $thisInclude){
			$mapId = $thisInclude['id'];
			if ($thisInclude['type'] == "player"){
				continue;
			}
			if (array_key_exists($mapId, $maps)){
				foreach($maps[$mapId] as $key => $thisMapVersion){
					$thisMapVersion['displayName'] = $thisInclude['attributes']['displayName'];
					$authorId = $thisInclude['relationships']['author']['data']['id'];
					$author = "?";
					foreach($mapsJson['included'] as $playerInclusion){
						if ($playerInclusion['type'] == "player"){
							if ($authorId == $playerInclusion['id']){
								$author = $playerInclusion['attributes']['login'];
							}
						}
					}
					$thisMapVersion['author'] = $author;
					$maps[$mapId][$key] = $thisMapVersion;
				}
			}
		}
		$page++;
	}
	
	/// Fetching mods
	$modsJson = [];
	$page = 1;
	$firstLoop = true;
	while ($modsJson['data'] || $firstLoop){
		$firstLoop = false;
		$fetcher = "https://api.faforever.com/data/modVersion?"
		."sort=-updateTime&"
		."filter=hidden==true&"
		."fields[modVersion]=downloadUrl,description,createTime,ranked,mod,hidden,ranked,filename,thumbnailUrl,type,description,updateTime&"
		."include=mod,mod.uploader&"
		."fields[mod]=displayName,uploader&"
		."fields[player]=login&"
		."page[number]=".$page;
		
		$string = file_get_contents($fetcher);
		$display ['meta']["modFetcher"] = $fetcher;

		$modsJson = json_decode($string, true);
		foreach($modsJson['data'] as $thisModVersion){
			$modId = $thisModVersion['relationships']['mod']['data']['id'];
			if (!array_key_exists($modId, $mods)){
				$mods[$modId] = array();
			}
			$mods[$modId][] = array(
				"createTime"=>$thisModVersion['attributes']['createTime'],
				"description"=>$thisModVersion['attributes']['description'],
				"downloadUrl"=>$thisModVersion['attributes']['downloadUrl'],
				"filename"=>$thisModVersion['attributes']['filename'],
				"ranked"=>$thisModVersion['attributes']['ranked'],
				"updateTime"=>$thisModVersion['attributes']['updateTime'],
				"thumbnailUrl"=>$thisModVersion['attributes']['thumbnailUrl'],
				"displayName"=>$thisModVersion['attributes']['displayName'],
			);
		}

		/// Linking mods to display names
		foreach($modsJson['included'] as $thisInclude){
			$modId = $thisInclude['id'];
			if (array_key_exists($modId, $mods)){
				foreach($mods[$modId] as $key => $thisModVersion){
					$thisModVersion['displayName'] = $thisInclude['attributes']['displayName'];
					$authorId = $thisInclude['relationships']['uploader']['data']['id'];
					$author = "?";
					foreach($modsJson['included'] as $playerInclusion){
						if ($playerInclusion['type'] == "player"){
							if ($authorId == $playerInclusion['id']){
								$author = $playerInclusion['attributes']['login'];
							}
						}
					}
					$thisModVersion['author'] = $author;
					$mods[$modId][$key] = $thisModVersion;
				}
			}
		}
		$page++;
	}
	
	$display['maps'] = array_reverse($maps);
	$display['mods'] = array_reverse($mods);
	
	$display['meta'] = array_merge($display['meta'], array(
		'mapRecords'=>count($maps),
		'modRecords'=>count($mods)
	));

	if (array_key_exists('errors', $json)){
		$display['meta']['errors'] = $json['errors'][0];
	}

	echo json_encode($display);
	exit;
?>