<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
			<LINK href="style.css?version=<?php echo round(rand(1, 10000));?>" rel="stylesheet" type="text/css">
			<!--<link rel="icon" href="favicon.gif"  type="image/gif"/>-->
			<link rel="icon" href="favicon.png" />
			<title>faf tools!</title>
		</meta>
	</head>
	<body>
		<div>
			faf tools!
		</div>
		<div>
			<ul>
			<?php
				
				$dir = scandir('.');
				foreach($dir as $thisDir){
					if (substr($thisDir, 0, 1) == '.'
						|| strstr($thisDir, ".") != false){
						continue;
					}
					echo '<li><b><a href="'.$thisDir.'">'.$thisDir.'</a></b> - '.getTranslation($thisDir).'</li>';
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

			?>
			</ul>
		</div>
	</body>
</html>