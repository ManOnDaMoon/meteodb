
/**
 *  Draw chart for daily data - using specific date format showin only hours
 * 
 *  TODO : add value series as parameter in div attribute
 */
function drawDailyChart(chartId, value, valueName, valueUnit, valueMin = null, valueMax = null) {
	dailyGraph = document.getElementById(chartId);
	if (dailyGraph != null) {
		fetch(dailyGraph.getAttribute('data-url'), { 
		  method: 'GET'
		})
		.then(function(response) { return response.json(); })
		.then(function(jsonData) {
			chartOptions = {
			    data: {
			        x: 'Time',
					xFormat: '%Y-%m-%d %H:%M:%S',
			        json: jsonData,
					keys: {
					        x: 'hour',
					        value: [value, 'min' + value, 'max' + value]
					      },
					axes: {
						[value]: 'y',
						['min' + value] : 'y',
						['max' + value] : 'y'
					},
					names: {
						[value]: 'Moy',
						['min' + value]: 'Min',
						['max' + value]: 'Max'
					},
					type: 'spline'
			    },
			    axis: {
			        x: {
			            type: 'timeseries',
			            tick: {
			                format: '%Hh'
			            }
			        },
					y: {
						label: valueUnit,
						min: valueMin,
						max: valueMax
					}
			    },
				grid: {
					y: {
						show: true
					}	
				},
				tooltip: {
			        format: {
						value: function (v, id, i, j) { return v + '°C'; } // apply this format to both y and y2
	        		}
				}
			};
			chartOptions['color'] = {
				pattern: ['#8B0000', '#A94141', '#5C0000']
			};
			chartOptions['bindto'] = '#' + chartId;
			var chart = c3.generate(chartOptions);
		});
	};	
};

/**
 *  Refresh button activation timeout
 */
function refreshButtonSetTimeout(refreshButtonId, timeout) {
	setTimeout(
		function() {
			$refresh = document.getElementById(refreshButtonId);
    		$refresh.addEventListener('click', function() { location.reload() }, false);
    		$refresh.removeAttribute('disabled');
		},
		timeout
	);
}


// On Document load
document.addEventListener(
"DOMContentLoaded",
function() {
	refreshButtonSetTimeout('refresh', 6000);
	
	drawDailyChart('daily_chart', 'tempc', 'Température', '°C');

}); 