let recordLimit = 10000;

let descriptionElem;
let loadingIndicatorElem;
let loadingZoneDescElem;
let errorDescElem;
let flotContainerElem;

function handleError(err) {
	let displayString = "Error: '" + err + "' - report this to rackover@racknet.noip.me";
	console.log(err);
	errorDescElem.innerHTML = displayString;
	loadingIndicatorElem.style.display = "none";
}

function setLoadingZoneDesc(str) {
	loadingZoneDescElem.innerHTML = str;
}

function appendToLoadingZoneDesc(str) {
	//I don't like creating a new elements/fiddling with the dom for new messages,
	//so I just update the string content of the message element.
	/*
	let newChild = document.createElement('span')
	newChild.innerHTML = str;
	loadingZoneDescElem.appendChild(newChild);
	*/
	loadingZoneDescElem.innerHTML = loadingZoneDescElem.innerHTML + str;
}

function showLoading() {
	flotContainerElem.style.display = "none";
	loadingIndicatorElem.style.display = "inline";
}

function showDone() {
	loadingIndicatorElem.style.display = "none";
	flotContainerElem.style.display = "block";
	appendToLoadingZoneDesc(" Done.");
}

function initDocElemVars() {
	descriptionElem = document.getElementById('description');
	loadingIndicatorElem = document.getElementById('loadingIndicator');
	loadingZoneDescElem = document.getElementById('loadingZoneDescription');
	errorDescElem = document.getElementById('errorDescription');
	flotContainerElem = document.getElementById('flot-container');
}

function getStatsFromDocument(){
	try {
		initDocElemVars();
		showLoading();
		let numberOfDays = parseFloat(document.getElementById('records').value) || 0;
		setLoadingZoneDesc("Requesting " + numberOfDays + " days from server.");
		//createChartWithTestData();
		createChart(numberOfDays);
	} catch(err){
		handleError(err);
	}
}

function getDateStringFrom(numberOfDays) {
	let dateStringToday = new Date().toISOString();
	let dateStringFromTemp = dateStringToday.substring(0, 10) + "T00:00:00.000Z";
	let dateFrom = new Date(dateStringFromTemp);
	dateFrom.setDate(dateFrom.getDate() - numberOfDays);
	let dateStringFrom = dateFrom.toISOString();
	console.log(dateStringFrom);
	return dateStringFrom;
}

function createChartWithTestData(){
	let request = new XMLHttpRequest();
	request.addEventListener('load', function(event) {
		if (request.status >= 200 && request.status < 300) {
			displayResults(this.responseText)
		} else {
			handleError(request.status + " - " + request.statusText + " - " + request.responseText);
		}
	});
	request.overrideMimeType("application/json");
	request.open("GET", "testDataBig.json", true);
	request.send();
}

function createChart(numberOfDays){
	let request = new XMLHttpRequest();
	request.addEventListener('load', function(event) {
		if (request.status >= 200 && request.status < 300) {
			displayResults(this.responseText)
		} else {
			handleError(request.status + " - " + request.statusText + " - " + request.responseText);
		}
	});
	request.open("GET", "https://api.faforever.com/data/player?fields[player]=userAgent,createTime&filter[player]=createTime>" + getDateStringFrom(numberOfDays) /*2018-07-05T00:00Z*/ + "&page[limit]=" + recordLimit + "&page[totals]", true);
	request.setRequestHeader("Content-type","application/vnd.api+json");
	request.send();
}

function createSeriesObject(jsonData) {
	let dates = {};
	let series = {};
	
	for (let userIndex in jsonData.data){
		let user = jsonData.data[userIndex];
		let userAgent = user.attributes.userAgent;
		let dateTime = user.attributes.createTime;
		let date = new Date(dateTime.substring(0, 10)).getTime();
		//create object for date to hold user agent entries if non-existant
		dates[date] = dates[date] || {};
		//increment user agent entry or create with 1 as value
		dates[date][userAgent] = dates[date][userAgent] + 1 || 1;
		
		series[userAgent] = series[userAgent] || {};
		series[userAgent][date] = series[userAgent][date] + 1 || 1;
	}
	//}
	console.log(dates);
	console.log(series);
	
	return series;
}

function createSeriesArray(seriesObject) {
	appendToLoadingZoneDesc(" Creating chart series.");
	//convert parsed objects to nested arrays (see testArray)
	let seriesArray = [];
	for (let serieIndex in seriesObject){
		let serie = seriesObject[serieIndex];
		
		let serieArray = [];
		for (let dateIndex in serie){
			let count = serie[dateIndex];
			
			//+dateIndex converts the milliseconds string into a number (removes the double quotes), otherwise HighCharts labels the x axis incorrectly
			serieArray.push([+dateIndex, count]);
		}
		let serieLabel = serieIndex === "null" ? "not logged in yet" : serieIndex;
		let serieObject = {label: serieLabel, name: serieLabel, data: serieArray};
		seriesArray.push(serieObject);
	}
	seriesArray.sort(function(so1, so2) {return so1.label.localeCompare(so2.label);}); //descending
	return seriesArray;
}

function displayResults(str_result){
	try {
		appendToLoadingZoneDesc(" Parsing " + str_result.length + " characters to json.");
		
		//https://elide.io/pages/guide/10-jsonapi.html
		//curl -g -X GET --header 'Accept: application/vnd.api+json' 'https://api.faforever.com/data/player?fields[player]=login,userAgent,createTime,id&filter[player]=createTime>2018-06-01T00:00Z&page[limit]=10000&page[totals]' > testData.json
		//curl -g -X GET --header 'Accept: application/vnd.api+json' 'https://api.faforever.com/data/player?fields[player]=userAgent,createTime&filter[player]=createTime>2018-05-01T00:00Z&page[limit]=10000&page[totals]' > testData2.json
		//let jsonData = {"data":[{"type":"player","id":"258776","attributes":{"createTime":"2018-07-05T12:09:40Z","login":"iEnderL0rdz","userAgent":"null"}},{"type":"player","id":"258777","attributes":{"createTime":"2018-07-05T12:47:47Z","login":"pdimon54","userAgent":"faf-client"}},{"type":"player","id":"258778","attributes":{"createTime":"2018-07-04T12:49:10Z","login":"Linsterot","userAgent":"faf-client"}},{"type":"player","id":"258779","attributes":{"createTime":"2018-07-05T13:09:52Z","login":"south_korea","userAgent":"faf-client"}},{"type":"player","id":"258780","attributes":{"createTime":"2018-07-05T13:27:49Z","login":"TexasVet","userAgent":"faf-client"}},{"type":"player","id":"258781","attributes":{"createTime":"2018-07-05T13:33:40Z","login":"Pyrhus","userAgent":"faf-client"}},{"type":"player","id":"258782","attributes":{"createTime":"2018-07-05T13:52:06Z","login":"Erlandior","userAgent":"downlords-faf-client"}},{"type":"player","id":"258783","attributes":{"createTime":"2018-07-05T13:55:24Z","login":"Astils","userAgent":"faf-client"}},{"type":"player","id":"258784","attributes":{"createTime":"2018-07-05T14:06:54Z","login":"Kirito9724","userAgent":"faf-client"}},{"type":"player","id":"258785","attributes":{"createTime":"2018-07-05T14:13:51Z","login":"Grineau","userAgent":"faf-client"}}]};
		let jsonData = JSON.parse(str_result);
		//console.log(jsonData.length());
		let numberOfRecords = jsonData.data.length;
		appendToLoadingZoneDesc(" Parsing " + numberOfRecords + " records");
		if (numberOfRecords >= recordLimit) {
			appendToLoadingZoneDesc(" (limit reached)");
		}
		appendToLoadingZoneDesc(".");

		let seriesObject = createSeriesObject(jsonData);
		let seriesArray = createSeriesArray(seriesObject);
		
		//This is how a seriesArray needs to look like to be able to show it in a chart.
		let testArray = [
			//seriesArray
			[
				/*serie1*/
				[/*serie1DataPoint1*/	1, 1],
				[/*serie1DataPoint2*/	2, 2],
				[/*serie1DataPoint2*/	3, 3]
			], [
				/*serie2*/
				[/*serie2DataPoint1*/	1, 3],
				[/*serie2DataPoint2*/	2, 2],
				[/*serie2DataPoint2*/	3, 1]
			]
		]
		
		plotCharts(seriesArray);
		showDone();
	} catch(err){
		handleError(err);
	}
}


