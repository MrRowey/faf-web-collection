<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
			<LINK href="style.css?version=<?php echo round(rand(1, 10000));?>" rel="stylesheet" type="text/css">
			<!--<link rel="icon" href="favicon.gif"  type="image/gif"/>-->
			<link rel="icon" href="favicon.png" />
			<title>FAF growth</title>
		</meta>
		<script>
			function getStatsFromDocument(){
				getStats(parseFloat(document.getElementById('records').value));
			}
			
			function getStats(records){
				document.getElementById('description').style.display = "none";
				document.getElementById('loadingZone').style.display = "block";
				
				document.getElementById('loadingZoneDescription').innerHTML = "Loading "+records+" records...";
				
				let http = new XMLHttpRequest();
				http.open("GET", "calculator.php?records="+records+"", true);
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
					let lowTimestamp = jsonData.time.oldest;
					let highTimestamp = jsonData.time.newest;
							
					const bounds = jsonData.bounds;
					
					let d = new Date(0); // The 0 there is the key, which sets the date to the epoch
					d.setUTCSeconds(lowTimestamp);
					lowTime = d.toDateString();
					d = new Date(0);
					d.setUTCSeconds(highTimestamp);
					highTime = d.toDateString();
					
					headerString = '<div class="metaInfo"><span class="gameAmount">Latest '+jsonData.meta.records+' new player records</span> - The <span class="gameAmount">FIRST DOT</span> and the <span class="gameAmount">LAST DOT</span> might be generated from truncated data and therefore erroneous. <br>	<span class="timeRange">(From '+lowTime+' to '+highTime+')</span></div>';
							
					let receiver = document.getElementById('receiverZone');
				
					if ((jsonData.meta.records <= 0)){
						receiver.innerHTML = "Empty records or invalid parameters";
						console.log(str_result);
					}
					else{
						receiver.innerHTML = headerString;
						const players = jsonData.players;
					
						/// Graph init
						
						let canvas = document.createElement('canvas');
						
						canvas.id = 'graphCanvas';
						canvas.width = 1800;
						canvas.height = 256;
						canvas.style.border = '1px solid white';
						canvas.style.backgroundColor = 'rgba(0,0,0,0)';
						
						receiver.appendChild(canvas);
					
						let ctx = canvas.getContext("2d");
						ctx.textBaseline="middle"; 
						
						/// Now let's draw points :)
						
						const margin = {x:50, y:canvas.height/2};
						
						const timeBounds = {
							"lowest": Math.floor(lowTimestamp/86400)*86400,
							"highest": Math.ceil(highTimestamp/86400)*86400
						}
						let records = {
							"bestDay":'',
							"worstDay":''
						}
						let clients = {};
						
						/// Drawing axes
						
						const guidesEvery = 86400;
						const guidesRange = [lowTimestamp, highTimestamp];
						const guideColor = "#FFCF52";
						let currentPoint = {x:0, y:0};
						let lastPoint = {x:-1, y:-1};
						let i =0;
						for (let j = guidesRange[0]; j < guidesRange[1]; j+=guidesEvery){
							const value = (j-timeBounds.lowest)/(timeBounds.highest-timeBounds.lowest);
							const playerAmount = jsonData.players[Math.floor(j/86400)].length;
							const data = (playerAmount - bounds.lowest) / (bounds.highest - bounds.lowest);
							
							const currentPoint = {
								x: canvas.width - (value*(canvas.width-margin.x)) - margin.x/2,
								y: canvas.height - (data*(canvas.height-margin.y))-margin.y/2
							}
							const line = {
								x1: currentPoint.x,
								y1: currentPoint.y,
								x2: currentPoint.x,
								y2: canvas.height-margin.y/4
							}
							
							if (line.x1 > canvas.width-margin.x/2){
								continue;
							}
							
							ctx.textAlign="center"; 
							ctx.strokeStyle = guideColor;
							ctx.beginPath();
							ctx.moveTo(line.x1, line.y1);
							ctx.lineTo(line.x2, line.y2);
							ctx.stroke();
							
							/// Draw dot
							ctx.fillStyle = "#FFCF52";
							ctx.beginPath();
							ctx.arc(currentPoint.x, currentPoint.y, 2, 0, 2 * Math.PI);
							ctx.fill();
							
							ctx.font = "12px Calibri";
							
							d = new Date(0);
							d.setUTCSeconds(j);
							time = d.getDate()+"/"+(d.getMonth()+1);
							
							if (playerAmount >= bounds.highest){
								records.bestDay = time;
							}
							if (playerAmount <= bounds.lowest){
								records.worstDay = time;
							}
					
							ctx.strokeText(time, line.x1, canvas.height-7 - 14*(i%2));
							ctx.strokeText(playerAmount, line.x1, currentPoint.y - 10);
							
							/// Connect to the last dot
							if (lastPoint.x > -1){
								ctx.strokeStyle = "#FFCF52";
								ctx.beginPath();
								ctx.moveTo(lastPoint.x, lastPoint.y);
								ctx.lineTo(currentPoint.x, currentPoint.y);
								ctx.stroke();
							}
							i++;
							lastPoint = currentPoint;
						}						
						
						/// Other stats
						for (let k in jsonData.players){
							let playerRecords = jsonData.players[k];
							for (let i = 0; i < playerRecords.length; i++){
								let player = playerRecords[i];
								if (clients[player.userAgent]  == undefined){
									clients[player.userAgent]  = 0;
								}
								clients[player.userAgent] ++;
							}
						}
						
						let clientP = document.createElement('p');
						clientP.textContent = "Client stats";
						let clientList = document.createElement('ul');
												
						for (k in clients){
							let name = k;
							if (k == 'null'){
								name = "Other";
							}
							let li = document.createElement('li');
							li.textContent = name+': '+clients[k]+' users ('+Math.floor((clients[k]/jsonData.meta.records)*100)+'%)';
							clientList.appendChild(li);
						}
						clientP.appendChild(clientList);
						
						let otherP = document.createElement('p');
						otherP.textContent = "Other stats";
						let otherList = document.createElement('ul');
					
						let bestLi = document.createElement('li');
						bestLi.textContent = 'Best day : '+records.bestDay+' ('+bounds.highest+' new users)';
						otherList.appendChild(bestLi);
						
						let worstLi = document.createElement('li');
						worstLi.textContent = 'Worst day : '+records.worstDay+' ('+bounds.lowest+' new users)';
						otherList.appendChild(worstLi);
						
						otherP.appendChild(otherList);
						
						receiver.appendChild(clientP);
						receiver.appendChild(otherP);
						
					}
				
				} catch(e){
					displayString = "Error "+e+" - report this to rackover@racknet.noip.me";
					console.log(e);
				}
			}
			
		</script>
	</head>
	
	<?php 
		$records = 5000;
		$args = false;
		
		if (isset($_GET['records'])){
			$records = $_GET['records'];
			$args = true;
		}
	?>
	<body <?php if ($args) echo 'onload="getStats(\''.$records."','".$nameFilter."','".$top.'\')"';?> >
		<div id="description" style="text-align:center;<?php if ($args) echo 'display:none;';?> " >
			<h2>
				FAF growth
			</h2>
			<p>
				<b> Records : </b>
				<input type="text" placeholder="(optional)" id="records" value="3000"></input>
			</p>
			<p>
				<button onclick="getStatsFromDocument()">
					Let's see
				</button>
			</p>
			
		</div>
		
		<div id="loadingZone" style="text-align:center;display:none;">
			<p id="loadingZoneDescription">
				Loading records...
			</p>
			<img src="raichu-loading.gif" alt="loading..."><br>
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