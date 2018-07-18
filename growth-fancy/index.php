<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
			<LINK href="style.css?version=<?php echo time();?>" rel="stylesheet" type="text/css">
			<!--<link rel="icon" href="favicon.gif"  type="image/gif"/>-->
			<link rel="icon" href="favicon.png" />
			<title>FAF growth</title>
		</meta>

		<script language="javascript" type="text/javascript" src="jquery.js"></script>
		<script language="javascript" type="text/javascript" src="jquery.flot.js"></script>
		<script language="javascript" type="text/javascript" src="jquery.flot.stack.js"></script>
		<script language="javascript" type="text/javascript" src="jquery.flot.time.js"></script>
		
		<script language="javascript" type="text/javascript" src="script.js"></script> 
	</head>
	
	<body>
		<div id="description" style="text-align:center;">
			<h2>FAF growth</h2>
			<p>
				Last <input type="text" placeholder="(optional)" id="records" value="7"></input> days.
				<button id="generateButton" onclick="getStatsFromDocument()">Generate Chart</button>
				<div id="loadingZoneDescription">Ready.</div>
				<div id="errorDescription"></div>
				<img id="loadingIndicator" src="raichu-loading.gif" alt="Loading..." style="display:none;"/>
			</p>
		</div>
		
		<div id="flot-container" class="flot-container-style" style="display:none;">
			<div id="flot-placeholder" class="flot-placeholder-style"></div>
		</div>
	</body>
</html>