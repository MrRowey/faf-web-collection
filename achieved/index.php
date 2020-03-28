<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
			<LINK href="style.css?version=<?php echo round(rand(1, 10000));?>" rel="stylesheet" type="text/css">
			<!--<link rel="icon" href="favicon.gif"  type="image/gif"/>-->
			<link rel="icon" href="favicon.png" />
			<title>Achievements</title>
		</meta>
		<script>
			function getStatsFromDocument(){
				getStats(document.getElementById('playerName').value);
			}
			
			function getStats(playerName){
				
				document.getElementById('description').style.display = "none";
				document.getElementById('loadingZone').style.display = "block";
				
				document.getElementById('loadingZoneDescription').innerHTML = "Loading "+playerName+"'s achievements...";
				
				let http = new XMLHttpRequest();
				http.open("GET", "calculator.php?playername="+playerName+"", true);
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
					
					displayString = "";
					
					let unlocked = 0;
					let achievementList = "";
					for (let i = 0; i < jsonData.achievements.length; i++){
						const thisAch = jsonData.achievements[i];
						let completion = '';
						if (thisAch.totalSteps > 0){
							completion = thisAch.currentSteps+'/'+thisAch.totalSteps;
						}
						let thisAchDisplay = ''
						+'<div class="achievementContainer">'
							+'<div class="tooltip">'
								+'<img src="'+thisAch.icon+'" alt="X" class="achievementIcon achievement'+thisAch.state+'">'
								+'<span class="tooltiptext"><b>'+thisAch.name+'</b><br>'+thisAch.description+'</span>'
								+'<span class="achievementCompletion">'+completion+'</span>'
							+'</div>'
						+'</div>';
						
						if (thisAch.state == "UNLOCKED"){
							unlocked ++;
							achievementList = thisAchDisplay+achievementList;
						}
						else{
							achievementList += thisAchDisplay;
						}	
					}
										
					const totalAchievements = jsonData.meta.achievements;
					const playerName = jsonData.meta.playerName;
					const completionAmount = unlocked/jsonData.achievements.length;
					
					displayString = ''
					+'<div class="metaInfo">'
						+'<b>Found '+totalAchievements+' achievements for player '+playerName+'</b> ('+Math.floor(completionAmount*100)+'% of completion)'
					+'</div>'
					+'<div class="achievementsList">'
						+achievementList
					+'</div>';
				
				} catch(e){
					displayString = "Error "+e+" - report this to rackover@racknet.noip.me";
					console.log(e);
				}
				document.getElementById('receiverZone').innerHTML = displayString;
			}
			
		</script>
	</head>
	
	<?php 
	
		$playerName = '';
		$args = false;
		
		if (isset($_GET['playername'])){
			$playerName = $_GET['playername'];
			$args = true;
		}
	?>
	<body <?php if ($args) echo 'onload="getStats(\''.$playerName.'\')"';?> >
		<div id="description" style="text-align:center;<?php if ($args) echo 'display:none;';?> " >
			<h2>
				See my achievements
			</h2>
			<p>
				<b> Player : </b>
				<input type="text"  id="playerName"></input>
			</p>
			<p>
				<button onclick="getStatsFromDocument()">
					Show me!
				</button>
			</p>	
			
		</div>
		
		<div id="loadingZone" style="text-align:center;display:none;">
			<p id="loadingZoneDescription">
				Loading achievements...
			</p>
			<img src="/loading.gif" alt="loading..."><br>
			This can take up to one minute!
		</div>
		
		<div id="receiverZone">
		
		</div>
		
	</body>
</html>