import Chart from 'chart.js';

const meetingChart = () => {
	const ctx = document.getElementById( 'ninety-chart' );
	const meetingCount = geojson.meetingCount;
	const remaining = 90 - meetingCount;

	console.log( 'from ninety-chart.js' );

	const data = {
		labels: [ 'done', 'remaining' ],
		datasets: [ {
			label: 'Meeting Count',
			data: [ meetingCount, remaining ],
			backgroundColor: [ 'green', 'yellow' ],
		} ],
	};

	const legendDisplay = 'pie' === geojson.chartType ? true : false;

	const options = {
		responsive: true,
		legend: {
			display: legendDisplay
		}
	};

	new Chart( ctx, {
		type: geojson.chartType,
		data: data,
		options: options
	} );
};

/**
 * Add percentage to center of Doughnut chart.
 *
 * @see https://stackoverflow.com/a/34947440/774793
 */
if ( 'doughnut' === geojson.chartType ) {
	Chart.pluginService.register(
		{
			beforeDraw: function ( chart ) {
				var width = chart.chart.width,
					height = chart.chart.height,
					ctx = chart.chart.ctx;

				ctx.restore();
				var fontSize = ( height / 114 ).toFixed( 2 );
				ctx.font = fontSize + "em sans-serif";
				ctx.textBaseline = "middle";

				var text = ( ( geojson.meetingCount / 90 ) * 100 ).toFixed( 1 ) + "%",
					textX = Math.round( ( width - ctx.measureText( text ).width ) / 2 ),
					textY = height / 2;

				ctx.fillText( text, textX, textY );
				ctx.save();
			}
		}
	);
}

export default meetingChart;
