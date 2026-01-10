fetch('/station/AVON/daily', { 
  method: 'GET'
})
.then(function(response) { return response.json(); })
.then(function(jsonData) {
	var chart = c3.generate({
	    data: {
	        x: 'Time',
			xFormat: '%Y-%m-%d %H:%M:%S',
	        json: jsonData,
			keys: {
			        x: 'hour',
			        value: ['tempc', 'humidity']
			      },
			axes: {
				tempc: 'y',
				humidity: 'y2'
			}
	    },
	    axis: {
	        x: {
	            type: 'timeseries',
	            tick: {
	                format: '%H:00'
	            }
	        },
			y: {
				
			},
			y2: {
				show: true
			}
	    }
	});
});