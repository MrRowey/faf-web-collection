<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
			<LINK href="style.css?version=<?php echo time() ;?>" rel="stylesheet" type="text/css">
			<!--<link rel="icon" href="favicon.gif"  type="image/gif"/>-->
			<link rel="icon" href="favicon.png" />
			<title>Normal rating distribution</title>
		</meta>
		<script>
		
		/// Mine
		
		function getStatsFromDocument(){
			getStats(document.getElementById('players').value);
		}
		
		function getStats(players){
			document.getElementById('description').style.display = "none";
			document.getElementById('loadingZone').style.display = "block";
			
			document.getElementById('loadingZoneDescription').innerHTML = "Fetching data...";
			
			let http = new XMLHttpRequest();
			http.open("GET", "calculator.php?players="+players+"", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			
			http.send();
			
			http.onload = function() {
				document.getElementById('loadingZone').style.display = "none";
			document.getElementById('description').style.display = "block";
				displayResults(http.responseText);
			}
		}
		
		function displayResults(str_result){
			let parsedData;
			let receiver = document.getElementById('receiverZone');
			try{
				parsedData = JSON.parse(str_result);
				
				if ((parsedData.ladder.meta.players.length <= 0 || parsedData.global.meta.players.length <= 0)){
					receiver.textContent = "Empty records, invalid parameters or non-existent players.";
					console.log(str_result);
				}
				
				else{
					
					const categories = ['global', 'ladder'];
						
					headerString = '<div class="metaInfo"><span class="gameAmount">Rating distribution for the latest games of players '+parsedData.global.meta.players.join(', ')+'</span><br>	<span class="timeRange"></span></div>';
					receiver.innerHTML = headerString;
					
					for (let h = 0; h < categories.length; h++){
						
						const jsonData = parsedData[categories[h]];
						
						let lowTime = jsonData.time.oldest;
						let highTime = jsonData.time.newest;
						
						let d = new Date(0); // The 0 there is the key, which sets the date to the epoch
						d.setUTCSeconds(lowTime);
						lowTime = d.toDateString();
						d = new Date(0);
						d.setUTCSeconds(highTime);
						highTime = d.toDateString();
						
						graphDiv = document.createElement('div');
						graphDiv.classList.add('graphZone');
						
						const playerList = jsonData.meta.players;
						let bounds = {lowest:0, highest:0};
						
						for (let i = 0; i < playerList.length; i++){
							const player = jsonData.players[playerList[i]];
							if (i == 0){
								bounds = {lowest: player.mean, highest: player.mean};
							}
							else{
								if (bounds.lowest > player.mean){
									bounds.lowest = player.mean;
								}
								if (bounds.highest < player.mean){
									bounds.highest = player.mean;
								}
							}
						}
							
						let canvas = document.createElement('canvas');
						canvas.id = 'graphCanvas';
						canvas.width = 1280;
						canvas.height = 720;
						canvas.style.border = '1px solid #B4F37C';
						canvas.style.backgroundColor = 'rgba(0,0,0,0)';
						
						const margin = {x:50, y:canvas.height/10};
						const range = [bounds.lowest-500, bounds.highest+500];
						const precision = 10;
						const xEvery = 250;
						const yEvery = 0.1;
					
						graphDiv.appendChild(canvas);
					
						let ctx = canvas.getContext("2d");
						ctx.textBaseline="middle"; 
						
						/// Guides
						// X
						ctx.strokeStyle = "#92D359";
						ctx.beginPath();
						ctx.moveTo(margin.x, canvas.height-margin.y);
						ctx.lineTo(canvas.width-margin.x, canvas.height-margin.y);
						ctx.stroke();
						for (let i = Math.floor(range[0]); i < range[1]; i+=1){
							if (Math.floor(i/xEvery) == Math.floor(i)/xEvery){
								ctx.strokeStyle = "#92D359";
								ctx.textAlign = "center";
								ctx.font = "12px Calibri";
								ctx.strokeText(i, margin.x+(i/range[1])*(canvas.width-margin.x), canvas.height-margin.y+25);
								
								ctx.strokeStyle = "#385D17";
								ctx.beginPath();
								ctx.moveTo(margin.x+(i/range[1])*(canvas.width-margin.x), margin.y);
								ctx.lineTo(margin.x+(i/range[1])*(canvas.width-margin.x), canvas.height-margin.y);
								ctx.stroke();
								
							}
						}
						ctx.strokeStyle = "#B4F37C";
						ctx.textAlign = "center";
						ctx.font = "12px Calibri";
						ctx.strokeText("Rating", canvas.width/2, canvas.height-30);
								
						// Y
						ctx.strokeStyle = "#92D359";
						ctx.beginPath();
						ctx.moveTo(margin.x, canvas.height-margin.y);
						ctx.lineTo(margin.x, margin.y);
						ctx.stroke();
						let multiplier = 0;
						for (let i = 0; i < 1; i+=0.1){
							if (i > yEvery*multiplier){
								multiplier++;
								let y = canvas.height-margin.y - i*(canvas.height-margin.y);
								if (y < margin.y){
									continue;
								}
								ctx.strokeStyle = "#92D359";
								ctx.textAlign = "center";
								ctx.font = "12px Calibri";
								ctx.strokeText((i/100).toFixed(3), margin.x-25, y);
							}
						}
						
						/// Data
						for (let i = 0; i < playerList.length; i++){
							const player = jsonData.players[playerList[i]];
							const color = RandomColor();
							let lastPoint = {};
							
							for (var j = Math.floor(range[0]); j < range[1]; j+=precision){
								const point = {
									xVal: (j-range[0])/(range[1]-range[0]),
									yVal: NormalDensityZx( j, player.mean , player.deviation )*100
								}
								
								if (point.yVal < 0.0001){
									continue;
								}
								const currentPoint = {
									x: margin.x + point.xVal*(canvas.width-margin.x*2),
									y: canvas.height - point.yVal*(canvas.height-margin.y)-margin.y,
								}
								
								/// Line
								if (j > 0){
									ctx.strokeStyle = (color);
									ctx.lineWidth = 2;
									
									ctx.beginPath();
									ctx.moveTo(lastPoint.x, lastPoint.y);
									ctx.lineTo(currentPoint.x, currentPoint.y);
									ctx.stroke();
									ctx.lineWidth = 1;
								}
								
								/// Dot
								ctx.fillStyle = color;
								ctx.beginPath();
								ctx.arc(currentPoint.x, currentPoint.y, 3, 0, 2 * Math.PI);
								ctx.fill();
								
								lastPoint = currentPoint;
							}
							
							/// Rating
							ctx.strokeStyle = (color);
							ctx.beginPath();
							let x = margin.x + (player.mean-player.deviation*3-range[0])/(range[1]-range[0])*(canvas.width-margin.x*2)
							ctx.moveTo(x, margin.y*4);
							ctx.lineTo(x, canvas.height-margin.y);
							ctx.stroke();
							ctx.textAlign = "center";
							ctx.font = "12px Calibri";
							ctx.strokeText(playerList[i], x, (margin.y*4) - 45);
							ctx.strokeText(Math.floor(player.mean-player.deviation*3), x, (margin.y*4) - 25);
						}
						let p = document.createElement('p');
						p.classList.add('graphName');
						p.textContent='Rating for '+categories[h]+' games';
						receiver.append(p);
						receiver.append(graphDiv);
					}
				}
				
			}catch(e){
				receiver.textContent = "Error "+e+" - report this to rackover@racknet.noip.me";
				console.log(e);
			}
		}
			
		/// Not mine
		function LightenColor(color, percent) {
			var num = parseInt(color,16),
				amt = Math.round(2.55 * percent),
				R = (num >> 16) + amt,
				B = (num >> 8 & 0x00FF) + amt,
				G = (num & 0x0000FF) + amt;

				return ("#"+(0x1000000 + (R<255?R<1?0:R:255)*0x10000 + (B<255?B<1?0:B:255)*0x100 + (G<255?G<1?0:G:255)).toString(16).slice(1)).toUpperCase();
		};
				
		function RandomColor() {
			var letters = '6789AB'.split('');
			var color = '#';
			for (var i = 0; i < 6; i++ ) {
				color += letters[Math.floor(Math.random() * letters.length)];
			}
			return color;
		}
		function NormalDensityZx( x, Mean, StdDev ) {
			var a = x - Mean;
			return Math.exp( -( a * a ) / ( 2 * StdDev * StdDev ) ) / ( Math.sqrt( 2 * Math.PI ) * StdDev );
		}
		function StandardNormalQx( x ) {
			if ( x === 0 ) // no approximation necessary for 0
			return 0.50;

			var t1, t2, t3, t4, t5, qx;
			var negative = false;
			if ( x < 0 ) {
			x = -x;
			negative = true;
			}
			t1 = 1 / ( 1 + ( 0.2316419 * x ) );
			t2 = t1 * t1;
			t3 = t2 * t1;
			t4 = t3 * t1
			t5 = t4 * t1;
			qx = NormalDensityZx( x, 0, 1 ) * ( ( 0.319381530 * t1 ) + ( -0.356563782 * t2 ) +( 1.781477937 * t3 ) + ( -1.821255978 * t4 ) + ( 1.330274429 * t5 ) );

			if ( negative == true )
			qx = 1 - qx;
			return qx;
		}
			//----------------------------------------------------------------------------------------------
			// Calculates P(x), the left tail area under the Standard Normal Curve, which is 1 - Q(x). 
		function StandardNormalPx( x ) {
			return 1 - StandardNormalQx( x );
		}
			//----------------------------------------------------------------------------------------------
			// Calculates A(x), the area under the Standard Normal Curve between +x and -x
		function StandardNormalAx( x ) {
			return 1 - ( 2 * StandardNormalQx( Math.abs( x ) ) );
		}
			
		</script>
		
	</head>
	
	<?php 
		$players = "";
		$args = false;
		
		if (isset($_GET['players'])){
			$players = $_GET['players'];
			$args = true;
		}
	?>
	<body <?php if ($args) echo 'onload="getStats(\''.$players.'\')"';?> >
		<div id="description" style="text-align:center;<?php if ($args) echo 'display:none;';?> " >
			<h2>
				See the rating distribution
			</h2>
			<p>
				Separate multiple names using a comma [<b>,</b>]. No spaces between names.
			</p>
			<p>
				<b> Players : </b>
				<input type="text" placeholder="Name1,Name2,Name3" id="players"></input>
			</p>
			<p>
				<button onclick="getStatsFromDocument()">
					Show me!
				</button>
			</p>
			
		</div>
		
		<div id="loadingZone" style="text-align:center;display:none;">
			<p id="loadingZoneDescription">
				Loading records...
			</p>
			<img src="flygon-loading.gif" alt="loading..."><br>
			This should take a second!
		</div>
		
		<div id="receiverZone">
		
		</div>
	</body>
</html>