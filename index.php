<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
			<LINK href="style.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <!--<link rel="icon" href="favicon.gif"  type="image/gif"/>-->
			<link rel="icon" href="./favicon.ico" />
			<title>FAF Tools</title>
		</meta>
	</head>
	<body>
        <nav class="navbar navbar bg-light">
            <h2 class="navbar-brand">FAF tools - A collection of tools that visualize statistics.</h2>
        </nav>
		<div>
            <h3 class="m-3">The tools we have to offer</h3>
			<div class="list-group m-3">
			<?php
				
				$dir = scandir('.');
				foreach($dir as $thisDir){
					if (strpos($thisDir, ".") !== false || !is_dir($thisDir)){
						continue;
					}
					echo '<a href="'.$thisDir.'" class="list-group-item list-group-item-action text-primary">'.getTitle($thisDir).' <span class="text-secondary">'.getTranslation($thisDir).'</span></a>';
				}
				
				function getTranslation($dir){
					switch($dir){
						default:
							return "";
							break;
						
						case "achieved":
							return "Take a look at your achievements (or someone elses!)";
							break;
						case "popularnames":
							return "What are the currently most used names for games ?";
							break;
						case "mostplayed":
							return "What are the currently most played maps for each gamemode ?";
							break;
                        case "maps played":
							return "See what maps you and your friends played the most";
							break;
						case "timewasted":
							return "How much time did you waste ingame ?";
							break;
						case "ratingevolution":
							return "See the evolution of your rating (ladder and global) over time";
							break;
						case "unit-db":
							return "Up to date SCFAF unit database";
							break;
						case "hiddenvault":
							return "Access hidden content from the map/mod vault!";
							break;
						case "ratingdistribution":
							return "Visualize the normal distribution curve of your current rating and compare it to others";
							break;
						case "growth":
							return "See how many users joined FAF in the past few days";
							break;
						case "growth-fancy":
							return "See how many users joined FAF in the past few days including the client they use now";
							break;
					}
				}

				function getTitle($dir){
					switch($dir){
						default:
							return $dir;
							break;
                        case "growth-fancy":
                            return "Growth fancy";
                            break;
						case "achieved":
							return "Your achievements";
							break;
						case "popularnames":
							return "Popular game titles";
							break;
						case "mostplayed":
							return "Most played maps";
							break;
                        case "maps played":
							return "Most payed maps by player";
							break;
						case "timewasted":
							return "In game time";
							break;
						case "ratingevolution":
							return "Rating evolution";
							break;
						case "unit-db":
							return "Unit db";
							break;
						case "hiddenvault":
							return "Hidden vault";
							break;
						case "ratingdistribution":
							return "Rating distribution";
							break;
						case "growth":
							return "Growth";
							break;
					}
				}

			?>
			</div>
		</div>
        <nav class="navbar navbar-expand-sm bg-light fixed-bottom">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="https://github.com/FAForever/faf-web-collection">Github</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/LICENSE">License</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://faforever.com">FAF</a>
                </li>
            </ul>
        </nav>
	</body>
</html>