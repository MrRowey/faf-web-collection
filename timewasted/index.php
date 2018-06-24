<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
			<LINK href="style.css?version=<?php echo round(rand(1, 100));?>" rel="stylesheet" type="text/css">
			<!--<link rel="icon" href="favicon.gif"  type="image/gif"/>-->
			<link rel="icon" href="favicon.png" />
			<title>Hours wasted</title>
		</meta>
		<style>
			body{
				font-family:Calibri;
			}
		</style>
		<script>
			function checkPlayerName(){
				document.getElementById('loadingZone').style.visibility = "visible";
				let http = new XMLHttpRequest();
				const playerName = document.getElementById("playerName").value;
				http.open("GET", "calculator.php?playername="+playerName+"", true);
				http.send();
				
				http.onload = function() {
					document.getElementById('loadingZone').style.visibility = "hidden";
					displayResults(http.responseText);
				}
			}
			
			function displayResults(str_result){
				//console.log(str_result);
				const number = parseInt(str_result);
				
				let text = "";
				if (isNaN(number)){
					console.log(str_result);
					text = "Internal error - report this to rackover@racknet.noip.me";
				}
				else if (number <= 0){
					text = "Could not find the given playername - or this player didn't play any games.";
				}
				else{
					const hours   = Math.floor(number / 3600);
					const minutes = Math.floor(number / 60) % 60;
					const seconds = number % 60;
					
					text = "You wasted a total of "+hours+" hours, "+minutes+" minutes, and "+seconds+" seconds ingame";
				}
				document.getElementById('receiverZone').innerHTML = text;
			}
			
		</script>
	</head>
	
	<div style="text-align:center;">
		<p>
			Enter your FAForever playername :
		</p>
		
		<p>
			<input id="playerName" type="text">
			
			</input>
		</p>
		<p>
			<button id="submitter" onClick="checkPlayerName()">
				Then click here
			</button>
		</p>
	</div>
	
	<div id="loadingZone" style="text-align:center;visibility:hidden;">
		Loading...<br>
		<img src="loading.gif" alt="loading...">
	</div>
	
	<div id="receiverZone" style="text-align:center;">
	
	</div>
	
	
</html>