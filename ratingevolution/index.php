<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
			<LINK href="style.css?version=<?php echo round(rand(1, 10000));?>" rel="stylesheet" type="text/css">
			<!--<link rel="icon" href="favicon.gif"  type="image/gif"/>-->
			<link rel="icon" href="favicon.png" />
			<title>Rating evolution</title>
		</meta>
		<script>
			function getStatsFromDocument(){
				getStats(parseFloat(document.getElementById('records').value), 
						document.getElementById('nameFilter').value);
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
				http.open("GET", "calculator.php?namefilter="+nameFilter+"&records="+records+"", true);
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
							
					const bounds = jsonData.bounds;
					
					let d = new Date(0); // The 0 there is the key, which sets the date to the epoch
					d.setUTCSeconds(lowTime);
					lowTime = d.toDateString();
					d = new Date(0);
					d.setUTCSeconds(highTime);
					highTime = d.toDateString();
					
					let ofUser = " of user "+jsonData.meta.nameFilter;
					
					headerString = '<div class="metaInfo"><span class="gameAmount">Rating evolution for the last '+jsonData.meta.records+' records'+ofUser+'</span><br>	<span class="timeRange">(From '+lowTime+' to '+highTime+')</span></div>';
							
					let receiver = document.getElementById('receiverZone');
				
					if ((jsonData.meta.records <= 0)){
						receiver.innerHTML = "Empty records, invalid parameters or non-existent player.";
						console.log(str_result);
					}
					else{
						receiver.innerHTML = headerString;
						
						const keys = Object.keys(jsonData.plays);
						for (let i = 0; i < keys.length; i++){
							
							let modName = "Ladder games";
							if (keys[i] == 0){
								modName = "Other games";
							}
							
							let canvas = document.createElement('canvas');
							let p = document.createElement('p');
							p.textContent = modName;
							
							canvas.id = 'graphCanvas'+i;
							canvas.width = 1800;
							canvas.height = 256;
							canvas.style.border = '1px solid white';
							canvas.style.backgroundColor = 'rgba(0,0,0,0)';
							
							receiver.appendChild(p);
							receiver.appendChild(canvas);
						
							let ctx = canvas.getContext("2d");
							ctx.textBaseline="middle"; 
							
							/// Now let's draw points :)
							
							const labels = 5;
							
							let currentPoint = {x:0, y:0};
							let lastPoint = {x:-1, y:-1};
							const margin = {x:50, y:canvas.height/2};
							const data = jsonData.plays[keys[i]];
							
							const dataKeys = Object.keys(data);
							
							const max = bounds.highest-bounds.lowest;
							
							/// Drawing axes
							
							const guidesEvery = 500;
							const guidesRange = [-1000, 4000];
							const guideColor = "#7451A3";
							
							for (let j = guidesRange[0]; j < guidesRange[1]; j+=guidesEvery){
								const value = (j-bounds.lowest)/max;
								const line = {
									x1: margin.x,
									y1: canvas.height - (value*(canvas.height-margin.y))-margin.y/2,
									x2: canvas.width-margin.x,
									y2: canvas.height - (value*(canvas.height-margin.y))-margin.y/2
								}
								
								if (line.y1 > canvas.height-margin.y/2){
									continue;
								}
								
								ctx.textAlign="right"; 
								ctx.strokeStyle = guideColor;
								ctx.beginPath();
								ctx.moveTo(line.x1, line.y1);
								ctx.lineTo(line.x2, line.y2);
								ctx.stroke();
								ctx.font = "12px Calibri";
								
								ctx.strokeText(Math.floor(j), line.x1-4, line.y1);
								
							}						
							
							/// Drawing values
							
							const labelEvery = dataKeys.length/labels;
							
							let counter = 0;
							
							for (let j = 0; j < dataKeys.length; j++){
								const thisMean = data[dataKeys[j]];
								const value = (thisMean-bounds.lowest)/max;
								const timeValue = (parseFloat(dataKeys[j])-jsonData.time.oldest)/(jsonData.time.newest-jsonData.time.oldest);
								
								currentPoint = {
									x: margin.x + timeValue*(canvas.width-margin.x*2),
									y: canvas.height - (value*(canvas.height-margin.y))-margin.y/2
								}
								
								//console.log(currentPoint);
								
								/// Draw dot
								ctx.fillStyle = "#D5C6E9";
								ctx.beginPath();
								ctx.arc(currentPoint.x, currentPoint.y, 2, 0, 2 * Math.PI);
								ctx.fill();
								
								/// Draw line
								if (lastPoint.x > -1){
									ctx.strokeStyle = "#D5C6E9";
									ctx.beginPath();
									ctx.moveTo(lastPoint.x, lastPoint.y);
									ctx.lineTo(currentPoint.x, currentPoint.y);
									ctx.stroke();
								}
								
								/// Draw text
								if (counter > labelEvery || j == dataKeys.length-1 || j == 0){
									ctx.strokeStyle = "#D5C6E9";
									ctx.textAlign="center"; 
									const textPosition = {x: currentPoint.x, y: 30};
									const downTextPosition = {x: currentPoint.x, y: canvas.height-30};
									
									let time = new Date(0);
									time.setUTCSeconds(parseFloat(dataKeys[j]));
									const timeText = time.getDate()+"-"+(time.getMonth()+1)+"-"+time.getFullYear();
									
									ctx.font = "12px Calibri";
									ctx.strokeText(Math.floor(thisMean), textPosition.x, textPosition.y);
									
									ctx.strokeStyle = "#6F8F95";
									ctx.beginPath();
									ctx.moveTo(textPosition.x, textPosition.y+20);
									ctx.lineTo(currentPoint.x, currentPoint.y-5);
									ctx.moveTo(currentPoint.x, currentPoint.y+5);
									ctx.lineTo(downTextPosition.x, downTextPosition.y-20);
									ctx.stroke();
									
									ctx.strokeStyle = "#D5C6E9";
									ctx.font = "12px Calibri";
									ctx.strokeText(timeText, downTextPosition.x, downTextPosition.y);
									
									counter = 0;
								}
								
								lastPoint = currentPoint;
								counter++;
							}
							
						
						}
						
						
						/*
						let p = document.createElement('p');
						p.textContent = "Various stats for user "+jsonData.meta.nameFilter+"";
						p.textContent += '<ul>'
										+'<li>Highest rating ever : <b>'+bounds.highest+'</b></li>'
										+'<li>Lowest rating ever : <b>'+bounds.lowest+'</b></li>'
										+'</ul>';
						
						receiver.appendChild(p);
						*/
					}
				
				} catch(e){
					displayString = "Error "+e+" - report this to rackover@racknet.noip.me";
					console.log(e);
				}
			}
			
		</script>
	</head>
	
	<?php 
		$records = 3000;
		$nameFilter = "";
		$top = 20;
		$args = false;
		
		if (isset($_GET['records'])){
			$records = $_GET['records'];
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
				Rating evolution
			</h2>
			<p>
				<i>Now THAT tool may be useful</i>
			</p>
			<p>
				<b> Name filter : </b>
				<input type="text" placeholder="Enter a name..." id="nameFilter"></input>
			</p>
			<p>
				<b> Records : </b>
				<input type="text" placeholder="(optional)" id="records" value="3000"></input>
			</p>
			<p>
				<button onclick="getStatsFromDocument()">
					Tell me!
				</button>
			</p>
			
		</div>
		
		<div id="loadingZone" style="text-align:center;display:none;">
			<p id="loadingZoneDescription">
				Loading records...
			</p>
			<img src="vapo-loading.gif" alt="loading..."><br>
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