function plotCharts(seriesArray) {
	plotWithOptions(seriesArray);
}

function plotWithOptions(series) {
	appendToLoadingZoneDesc(" Creating chart.");
	console.log(series);
	
	let stack = true;
	let bars = true;//false,
	let lines = false;//true,
	let steps = false;//true;

	//hours * minutes * seconds * milliseconds
	let millisPerDay = 24 * 60 * 60 * 1000;
	//minutes * seconds * milliseconds
	let millisPerHour = 60 * 60 * 1000;
	
	let viewportWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
	let viewportHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
	
	let width = 0.98 * viewportWidth;
	let height = viewportHeight - descriptionElem.offsetHeight;
	console.log(width + "; " + height);
	flotContainerElem.setAttribute("style","width:" + width + "px; height:" + height + "px");
	
	$.plot("#flot-placeholder",
		series,
		{
		xaxis: {
			mode: "time",
			minTickSize: [1, "day"]
		},
		yaxis: {
			tickSize: 10
		},
		series: {
			stack: 0,//stack,
			lines: {
				show: lines,
				fill: true,
				steps: steps
			},
			bars: {
				show: bars,
				barWidth: millisPerDay - millisPerHour
			},
		},
		colors: ["#993300", "#4854B0", "#BEA910"],
		grid: {
			hoverable: true
		},
		legend: {
			backgroundColor: "#2E2100",
			backgroundOpacity: 0.5,
			sorted: "descending"
		}
	});
	
	$("<div id='flot-tooltip'></div>").css({
		position: "absolute",
		display: "none"
	}).appendTo("body");
	
	$("#flot-placeholder").bind("plothover", function (event, pos, item) {
		var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
		$("#hoverdata").text(str);

		if (item) {
			var x = item.datapoint[0];
			var y = item.datapoint[1];
			
			var ySerie = item.series.data[item.dataIndex][1];

			//$("#flot-tooltip").html(item.series.label + " of " + x + " = " + y + "; " + ySerie)
			$("#flot-tooltip").html(item.series.label + ": " + ySerie)
				.css({top: item.pageY+5, left: item.pageX+5}).fadeIn(200);
		} else {
			$("#flot-tooltip").hide();
		}
	});
}
