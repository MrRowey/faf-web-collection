<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
			<LINK href="style.css?version=<?php echo time() ;?>" rel="stylesheet" type="text/css">
			<!--<link rel="icon" href="favicon.gif"  type="image/gif"/>-->
			<link rel="icon" href="favicon.png" />
			<title>Most popular name</title>
		</meta>
		<script>
			function getStatsFromDocument(){
				getStats(parseFloat(document.getElementById('records').value), 
						document.getElementById('nameFilter').value, 
						parseFloat(document.getElementById('top').value));
			}
			
			function getStats(records, nameFilter, top){
				document.getElementById('description').style.display = "none";
				document.getElementById('loadingZone').style.display = "block";
				
				let inNameOf = "";
				if (nameFilter != inNameOf){
					inNameOf = " for player "+nameFilter;
				}
				document.getElementById('loadingZoneDescription').innerHTML = "Loading "+records+" records"+inNameOf+"...";
				
				let http = new XMLHttpRequest();
				http.open("GET", "calculator.php?namefilter="+nameFilter+"&records="+records+"&top="+top+"", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				
				http.send();
				
				http.onload = function() {
					document.getElementById('loadingZone').style.display = "none";
					displayResults(http.responseText);
				}
			}
			
			function displayResults(str_result){
				let displayString = '';
				let jsonData;
				try{
					jsonData = JSON.parse(str_result);
					/// Time range
					let lowTime = jsonData.time.oldest;
					let highTime = jsonData.time.newest;
					
					let d = new Date(0); // The 0 there is the key, which sets the date to the epoch
					d.setUTCSeconds(lowTime);
					lowTime = d.toDateString();
					d = new Date(0);
					d.setUTCSeconds(highTime);
					highTime = d.toDateString();
					
					let ofUser = '';
					
					if (jsonData.meta.nameFilter != ''){
						ofUser = " of user "+jsonData.meta.nameFilter;
					}
					
					headerString = '<div class="metaInfo"><span class="gameAmount">Map proportions and count (top '+jsonData.meta.top+') for the last '+jsonData.meta.records+' games'+ofUser+'</span><br>	<span class="timeRange">(From '+lowTime+' to '+highTime+')</span></div>';
					
					if ((jsonData.meta.records <= 0)){
						displayString = "Empty records, invalid parameters or non-existent player.";
						console.log(str_result);
					}
					else{
						/// Games
						
						const keys = Object.keys(jsonData.plays);
						for (let i = 0; i < keys.length; i++){
							const modName = keys[i];
							const category = jsonData.plays[keys[i]];
							const gameList = category.games;
							let gameString = '';
							
							for (let j = 0; j < gameList.length; j++){
								const percent = Math.round((gameList[j].played/category.stats.totalGames)*100);
								gameString += '<div class="mapLine"><div class="map"><p class="mapName"><span class=" color-'+(i%9)+'">'+(j+1)+'.</span> '+gameList[j].gameName+'</p><span class="mapPlayCount">(played <b>'+gameList[j].played	+'</b> times)<br>(represents <b>'+percent+'</b>% of games analyzed)</span></div></div>';
							}
							
							displayString += '<div class="featuredBlock"><div class="featuredModName color-'+(i%9)+'">'+modName+'</div><div class="mapList">'+gameString+'</div></div>';
							
						}
						
						displayString = headerString+'<div class="featuredMods">'+displayString+'</div>';
					}
				
				} catch(e){
					displayString = "Error "+e+" - report this to rackover@racknet.noip.me";
					console.log(e);
				}
				document.getElementById('receiverZone').innerHTML = displayString;
			}
			
		</script>
	</head>
	
	<?php 
		$records = 5000;
		$nameFilter = "";
		$top = 20;
		$args = false;
		
		if (isset($_GET['records'])){
			$records = $_GET['records'];
			$args = true;
		}
		if (isset($_GET['top'])){
			$top = $_GET['top'];
			$args = true;
		}
		if (isset($_GET['namefilter'])){
			$nameFilter = $_GET['namefilter'];
			$args = true;
		}
	?>
	<body <?php if ($args) echo 'onload="getStats(\''.$records."','".$nameFilter."','".$top.'\')"';?> >
		<div id="description" style="text-align:center;<?php if ($args) echo 'display:none;';?> " >
			<h2>
				What are the most popular games names ?
			</h2>
			<p>
				<i>Place your bet, then click the button</i>
			</p>
			<p>
				<button onclick="getStatsFromDocument()">
					Tell me!
				</button>
			</p>
			<details>
				<summary>Custom settings</summary>
						
				<p>
					<b> Records : </b>
					<input type="text" placeholder="(optional)" id="records" value="5000"></input>
				</p>
				<p>
					<b> Top : </b>
					<input type="text" placeholder="(optional)" id="top" value="20"></input>
				</p>
				<p>
					<b> Name filter : </b>
					<input type="text" placeholder="(optional)" id="nameFilter"></input>
				</p>
			</details>
			
		</div>
		
		<div id="loadingZone" style="text-align:center;display:none;">
			<p id="loadingZoneDescription">
				Loading records...
			</p>
			<img src="victi-loading.gif" alt="loading..."><br>
			<?php 
				if ($records <= 10000){
					echo "This should take less than a minute!";
				}
				else{
					echo "This might take a while...";
				}
			?>
		</div>
		
		<div id="receiverZone">
		
		</div>
		
	</body>
</html>