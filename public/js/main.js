
/**
 *  Draw chart for daily data - using specific date format showin only hours
 * 
 */
function drawEvolutionChart(chartId, value, valueName, valueUnit,
		valueMin = null, valueMax = null, colorScheme = null,
		xKey = 'hour', xTickFormat = '%Hh') {
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
			chartOptions['data']['keys']['x'] = xKey;
			
			if (Array.isArray(value)) {
				chartOptions['data']['keys']['value'] = value;
			} else {
				chartOptions['data']['keys']['value'] = [value]
			}

			// Chart type (spline = curved path)
			chartOptions['data']['type'] = 'spline';

			// Axes options
			chartOptions['data']['axes'] = {};
			if (Array.isArray(value)) {
				value.forEach(function (item, index, arr) {
					chartOptions['data']['axes'][item] = 'y';
					});
			} else {
				chartOptions['data']['axes'][value] = 'y';
			}

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
			chartOptions['axis']['x']['tick']['format'] = xTickFormat; // "24h" format
			
			//Y-Axis
			chartOptions['axis']['y']['label'] = valueUnit;
			chartOptions['axis']['y']['min'] = valueMin;
			chartOptions['axis']['y']['max'] = valueMax;
			
			// Grid
			chartOptions['grid'] = {};
			chartOptions['grid']['y'] = {};
			chartOptions['grid']['y']['show'] = true;
			chartOptions['grid']['y']['lines'] = {};
			chartOptions['grid']['y']['lines'] = [{value : 0, text: "Zero", position: 'start'}];
			
			// Tooltip shown on hover
			chartOptions['tooltip'] = {};
			chartOptions['tooltip']['format'] = {};
			chartOptions['tooltip']['format']['value'] = (function (v) { return v + valueUnit; });
			
			// Tooltip shown on hover
			chartOptions['line'] = {};
			chartOptions['line']['connectNull'] = false;
			
			// Color scheme
			chartOptions['color'] = {};
			if (colorScheme != null) {
				chartOptions['color']['pattern'] = colorScheme;
			} else {
				chartOptions['color']['pattern'] = ['#8B0000', '#A94141', '#5C0000'];
			}
			
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
		drawEvolutionChart('daily_temp_chart',
			['tempc', 'mintempc', 'maxtempc'],
			['Moy', 'Min', 'Max'],
			'°C'
		);
		drawEvolutionChart('daily_press_chart',
			['barohpa', 'minbarohpa', 'maxbarohpa'],
			['Moy', 'Min', 'Max'],
			'hPa'
		);
		drawEvolutionChart('daily_humid_chart',
			['humidity', 'minhumidity', 'maxhumidity'],
			['Moy', 'Min', 'Max'],
			'%', 10, 90,
			['Blue', 'LightBlue', 'DarkBlue']
		);
		drawEvolutionChart('daily_indoortemp_chart',
			['indoortempc', 'minindoortempc', 'maxindoortempc'],
			['Moy', 'Min', 'Max'],
			'°C'
		);
	}
	
	chartContainer = document.getElementById('weekly_chart_container');
	if (chartContainer != null) {
		drawEvolutionChart('weekly_temp_chart',
			['tempc', 'mintempc', 'maxtempc'],
			['Moy', 'Min', 'Max'],
			'°C', null, null, null, 'hour', '%d/%m %Hh'
		);
		drawEvolutionChart('weekly_press_chart',
			['barohpa', 'minbarohpa', 'maxbarohpa'],
			['Moy', 'Min', 'Max'],
			'hPa', null, null, null, 'hour', '%d/%m %Hh'
		);
		drawEvolutionChart('weekly_humid_chart',
			['humidity', 'minhumidity', 'maxhumidity'],
			['Moy', 'Min', 'Max'],
			'%', 10, 90,
			['Blue', 'LightBlue', 'DarkBlue'], 'hour', '%d/%m %Hh'
		);
		drawEvolutionChart('weekly_indoortemp_chart',
			['indoortempc', 'minindoortempc', 'maxindoortempc'],
			['Moy', 'Min', 'Max'],
			'°C', null, null, null, 'hour', '%d/%m %Hh'
		);
	}

}); 