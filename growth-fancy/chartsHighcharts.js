function plotCharts(seriesArray) {
	plotWithOptionsHighChartsAbsolute(seriesArray);
	plotWithOptionsHighChartsPercent(seriesArray);
}

function plotWithOptionsHighChartsAbsolute(seriesData) {
	seriesData.reverse(); //keeps same column stack order as Flot which plots them the other way round
	
	Highcharts.chart('hc-container-absolute', {
		chart: {
			type: 'column'
		},
		title: {
			text: 'Registrations'
		},
		xAxis: {
			type: 'datetime',
			labels: {
				rotation: -45,
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Registrations'
			},
			stackLabels: {
				enabled: true,
				//rotation: -45,
				style: {
					fontWeight: 'bold',
					color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
				}
			}
		},
		legend: {
			align: 'right',
			x: -30,
			verticalAlign: 'top',
			//y: 25,
			floating: true,
			backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
			borderColor: '#CCC',
			borderWidth: 1,
			shadow: false
		},
		tooltip: {
			headerFormat: '<b>{point.key}</b><br/>',
			pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>Total: {point.stackTotal}'
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				pointPadding: 0,
				groupPadding: 0.05,
				borderWidth: 0,
				shadow: false,
				dataLabels: {
					//enabled: true,
					color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
				}
			},
		},
		series: seriesData
	});
}

function plotWithOptionsHighChartsPercent(seriesData) {
	//seriesData.reverse(); //keeps same column stack order as Flot which plots them the other way round
	
	Highcharts.chart('hc-container-percent', {
		chart: {
			type: 'column'
		},
		title: {
			text: 'Client Share'
		},
		xAxis: {
			type: 'datetime',
			labels: {
				rotation: -45,
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Percentage'
			},
			stackLabels: {
				enabled: true,
				style: {
					fontWeight: 'bold',
					color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
				}
			}
		},
		legend: {
			align: 'right',
			x: -30,
			verticalAlign: 'top',
			//y: 25,
			floating: true,
			backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
			borderColor: '#CCC',
			borderWidth: 1,
			shadow: false
		},
		tooltip: {
			headerFormat: '<b>{point.key}</b><br/>',
			pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
			shared: true
		},
		plotOptions: {
			column: {
				stacking: 'percent',
				pointPadding: 0,
				groupPadding: 0.05,
				borderWidth: 0,
				shadow: false,
				dataLabels: {
					//enabled: true,
					color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
				}
			},
		},
		series: seriesData
	});
}
