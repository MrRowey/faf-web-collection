<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
			<LINK href="style.css?version=<?php echo round(rand(1, 10000));?>" rel="stylesheet" type="text/css">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
			<!--<link rel="icon" href="favicon.gif"  type="image/gif"/>-->
			<link rel="icon" href="favicon.png" />
			<title>Maps played</title>
		</meta>
		<script>
            function escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

			function getStatsFromDocument(){
				getStats(document.getElementById('playerName').value);
			}
			
			function getStats(playerName){

                document.getElementById('description').style.display = "none";
				document.getElementById('loadingZone').style.display = "block";
				
				document.getElementById('loadingZoneDescription').innerHTML = "Loading "+playerName+"'s maps played...";
				
				let http = new XMLHttpRequest();
				http.open("GET", "calculator.php?playername="+encodeURI(playerName)+"", true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				
				http.send();
				
				http.onload = function() {
					document.getElementById('loadingZone').style.display = "none";
					if(http.status !== 200){
					    alert("The server throw an error: "+ http.responseText);
					    return;
                    }
					displayResults(http.responseText);
                    document.getElementById('playerNameTextContainer').style.display = "flex";
                    document.getElementById('playerNameText').innerHTML = "Showing "+playerName+"'s maps played...";
				}
			}

            function displayMap(rootElement, map) {
                const html =`
                <div class="card m-2 position-relative shadow" style="width: 200px">
                    <img src="${encodeURI(map.imageUrl)}" alt="map image" class="card-img-top" style="width: 200px; height: 200px"/>
                    <div class="card-body">
                        <h4 class="card-text position-absolute shadow-lg" style="top: 10px; left: 10px"><span class="badge badge-info">${map.count}</span></h4>
                        <h4 class="card-title">${escapeHtml(map.name)}</h4>
                    </div>
                </div>
                `;
                rootElement.innerHTML += html;
            }

            function displayJson(jsonData) {
                let rootElement = document.getElementById("receiverZone");
                for(const map of jsonData){
                    displayMap(rootElement, map);
                }
            }

            function displayResults(str_result){
				let jsonData;
				try{
					jsonData = JSON.parse(str_result);
					displayJson(jsonData);

				} catch(e){
					alert("An error happened: "+ e.message);
					console.log(e);
				}
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
		<div id="description" style="text-align:center;<?php if ($args) echo 'display:none;';?>" class="container-fluid">
			<h2>
				See my played maps
			</h2>
            <div class="row justify-content-md-center">
                <div class="form-group col-2">
                    <label for="playerName">Player:</label>
                    <input type="text" class="form-control" placeholder="player name" id="playerName"/>
                    <button onclick="getStatsFromDocument()" type="button" class="btn btn-primary m-3">
                        Show me!
                    </button>
                </div>
            </div>
		</div>
		
		<div id="loadingZone" style="text-align:center;display:none;">
			<p id="loadingZoneDescription">
				Loading maps played...
			</p>
            <img src="/loading.gif" alt="loading gif"/>
            <p>This can take up to one minute!</p>
		</div>
		
		<div id="receiverZone" class="d-flex flex-row flex-wrap">
            <nav id="playerNameTextContainer" class="navbar navbar-expand-sm bg-light justify-content-center w-100 sticky-top" style="display: none">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <h4 id="playerNameText">Link 1</h4>
                    </li>
                </ul>
            </nav>

        </div>
		
	</body>
</html>