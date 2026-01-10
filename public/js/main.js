
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
			chartOptions = {};
			// Data options
			chartOptions['data'] = {};
			chartOptions['data']['x'] = 'Time';
			chartOptions['data']['xFormat'] = '%Y-%m-%d %H:%M:%S';
			chartOptions['data']['json'] = jsonData;
			chartOptions['data']['keys'] = {};
			chartOptions['data']['keys']['x'] = 'hour';
			
			if (Array.isArray(value)) {
				chartOptions['data']['keys']['value'] = value;
			} else {
				chartOptions['data']['keys']['value'] = [value]
			}

			// Chart type (spline = curved path)
			chartOptions['data']['type'] = 'spline';

			// Axes options
			chartOptions['data']['axes'] = {};
			chartOptions['data']['axes'][value] = 'y';
			chartOptions['data']['axes']['min' + value] = 'y';
			chartOptions['data']['axes']['max' + value] = 'y';

			// Series names
			chartOptions['data']['names'] = {};
			if (Array.isArray(value)) {
				value.forEach(function (item, index, arr) {
					chartOptions['data']['names'][item] = valueName[index];
				});
			} else {
				chartOptions['data']['names'][value] = valueName;
			}

			// Axis params
			chartOptions['axis'] = {};
			chartOptions['axis']['x'] = {};
			chartOptions['axis']['y'] = {};
			
			// X-Axis
			chartOptions['axis']['x']['type'] = 'timeseries';
			chartOptions['axis']['x']['tick'] = {};
			chartOptions['axis']['x']['tick']['format'] = '%Hh'; // "24h" format
			
			//Y-Axis
			chartOptions['axis']['y']['label'] = valueUnit;
			chartOptions['axis']['y']['min'] = valueMin;
			chartOptions['axis']['y']['max'] = valueMax;
			
			// Grid
			chartOptions['grid'] = {};
			chartOptions['grid']['y'] = {};
			chartOptions['grid']['y']['show'] = true;
			
			// Tooltip shown on hover
			chartOptions['tooltip'] = {};
			chartOptions['tooltip']['format'] = {};
			chartOptions['tooltip']['format']['value'] = (function (v) { return v + valueUnit; });
			
			// Color scheme
			chartOptions['color'] = {
				pattern: ['#8B0000', '#A94141', '#5C0000']
			};
			
			// Container to create graph in
			chartOptions['bindto'] = '#' + chartId;
			
			c3.generate(chartOptions);
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
	
	chartContainer = document.getElementById('chart_container');
	if (chartContainer != null) {
		drawDailyChart('daily_temp_chart', ['tempc', 'mintempc', 'maxtempc'], ['Moy', 'Min', 'Max'], '°C');
		drawDailyChart('daily_press_chart', ['barohpa', 'minbarohpa', 'maxbarohpa'], ['Moy', 'Min', 'Max'], 'hPa');
	}

}); 