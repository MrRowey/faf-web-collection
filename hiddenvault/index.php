<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
			<LINK href="style.css?version=<?php echo time();?>" rel="stylesheet" type="text/css">
			<!--<link rel="icon" href="favicon.gif"  type="image/gif"/>-->
			<link rel="icon" href="favicon.png" />
			<title>Hidden vault</title>
		</meta>
		<script>
			
			let mode = "maps";
			let searchBy = "displayName";
			let searchCriteria;
			let jsonData;
			
			function setTitleLine(){
				const receiver = document.getElementById('resourcesList');
				receiver.innerHTML = '';
					
				let div = document.createElement('div');
				div.classList.add('resourceLine');
				div.classList.add('headerLine');
				
				/// Preview
				let preDiv = document.createElement('div');
				preDiv.classList.add('resourcePreview');
				preDiv.textContent = "Preview";
				preDiv.style.height = "auto";
				
				/// Name
				let nameDiv = document.createElement('div');
				nameDiv.classList.add('resourceName');
				nameDiv.textContent = "Name";
				
				/// Author
				let authorDiv = document.createElement('div');
				authorDiv.classList.add('resourceName');
				authorDiv.textContent = "Author";
				
				/// Description
				let descDiv = document.createElement('div');
				descDiv.classList.add('resourceDescription');
				descDiv.textContent = "Description";
				
				/// Rated
				let rDiv = document.createElement('div');
				rDiv.classList.add('resourceRanked');
				rDiv.textContent = "R";
				
				/// Download button
				let dwnDiv = document.createElement('div');
				dwnDiv.classList.add('resourceDownload');
				dwnDiv.textContent = "Download";
				
				div.append(preDiv);
				div.append(nameDiv);
				div.append(authorDiv);
				div.append(descDiv);
				div.append(rDiv);
				div.append(dwnDiv);
				
				receiver.append(div);
			}
		
			function search(){
				searchCriteria = document.getElementById('searchBar').value;
				
				let radios = document.getElementsByName('radioSearch');
				for (let i = 0; i < radios.length; i++){
					if (radios[i].checked){
						mode = radios[i].value;
						break;
					}
				}
				
				radios = document.getElementsByName('radioCriteria');
				for (let i = 0; i < radios.length; i++){
					if (radios[i].checked){
						searchBy = radios[i].value;
						break;
					}
				}
				
				displayResults();
			}
		
			function getStatsFromDocument(){
				getStats();
			}
			
			function getStats(){
				document.getElementById('loadingZone').style.display = "block";
				document.getElementById('searchDiv').style.display = "none";
				
				document.getElementById('loadingZoneDescription').innerHTML = "Fetching data...";
				
				let http = new XMLHttpRequest();
				http.open("GET", "calculator.php", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				
				http.send();
				
				http.onload = function() {
					document.getElementById('loadingZone').style.display = "none";
					document.getElementById('searchDiv').style.display = "block";
					readResults(http.responseText, search);
				}
			}
			
			function readResults(str_result, callback=function(){}){
				try{
					jsonData = JSON.parse(str_result);
					callback();
				} 
				catch(e){
					const receiver = document.getElementById('resourcesList');
					receiver.innerHTML = "Error "+e+" - report this to rackover@racknet.noip.me";
					console.log(e);
				}
			}
			
			function displayResults(){
				const receiver = document.getElementById('resourcesList');
				setTitleLine();

				let data = jsonData[mode];
				let results = 0;
				
				for (let key in data) {
					const rscVersions = jsonData[mode][key];
					for (let i = 0; i < rscVersions.length; i++){
						resVer = rscVersions[i];
						
						if (resVer[searchBy].indexOf(searchCriteria) < 0){
							continue;
						}
						
						let div = document.createElement('div');
						div.classList.add('resourceLine');
						
						/// Preview
						let preDiv = document.createElement('div');
						preDiv.classList.add('resourcePreview');
						let img = document.createElement('img');
						img.src = resVer.thumbnailUrl;
						img.alt = "-";
						img.width = 64;
						img.height = 64;
						preDiv.append(img);
						
						/// Name
						let nameDiv = document.createElement('div');
						nameDiv.classList.add('resourceName');
						nameDiv.textContent = resVer.displayName.replace(/<\/?[\w\s="/.':;#-\/\?]+>/gi, '');;
						
						/// Author
						let authorDiv = document.createElement('div');
						authorDiv.classList.add('resourceName');
						authorDiv.textContent = resVer.author.replace(/<\/?[\w\s="/.':;#-\/\?]+>/gi, '');;
						
						/// Description
						let descDiv = document.createElement('div');
						descDiv.classList.add('resourceDescription');
						descDiv.textContent = resVer.description.replace(/<\/?[\w\s="/.':;#-\/\?]+>/gi, '');
						
						/// Rated
						let rDiv = document.createElement('div');
						rDiv.classList.add('resourceRanked');
						if (resVer.ranked == true){
							rDiv.textContent = "x";
						}
						
						/// Download button
						let dwnDiv = document.createElement('div');
						dwnDiv.classList.add('resourceDownload');
						let a = document.createElement('a');
						a.href = resVer.downloadUrl;
						a.innerHTML = "DOWNLOAD";
						dwnDiv.append(a);
						
						div.append(preDiv);
						div.append(nameDiv);
						div.append(authorDiv);
						div.append(descDiv);
						div.append(rDiv);
						div.append(dwnDiv);
						
						receiver.append(div);
						results++;
					}
				}
				
				document.getElementById('searchStats').textContent = results+ " results";
			}
			
			
		</script>
	</head>
	
	<body onload="getStats()">
		<p class="title">
			Hidden vault
		</p>
		<div id="searchDiv" class="search" style="display:none;">
			<div class="searchZone">
				<div>
					<input type="text" placeholder="Enter resource name here..." id="searchBar" />
				</div>
				<div>
					<button onclick="search()">Search</button>
				</div>
			</div>
			<div class="radioZone">
				<input type="radio" id="radioMaps" name="radioSearch" value="maps" checked>Maps
				<input type="radio" id="radioMods" name="radioSearch" value="mods">Mods
			</div>
			<div class="radioZone">
				<input type="radio" id="radioName" name="radioCriteria" value="displayName" checked>Name
				<input type="radio" id="radioAuthor" name="radioCriteria" value="author">Author
			</div>
		</div>
		<div id="searchStats">
			
		</div>
		<div id="loadingZone" class="search" style="text-align:center;">
			<p id="loadingZoneDescription"></p>
			<img src="espeon-loading.gif" alt="loading...">
		</div>
		
		<div id="resourcesList">
		
		</div>
		
	</body>
</html>