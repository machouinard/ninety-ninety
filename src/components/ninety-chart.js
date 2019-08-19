import Chart from 'chart.js';

const meetingChart = () => {
	const ctx = document.getElementById( 'ninety-chart' );
	let meetingCount = geojson.meetingCount;
	let remaining = 90 - meetingCount;
	let legendDisplay;

	switch ( geojson.chartType ) {
		case 'pie':
			legendDisplay = true;
			break;
		case 'bar':
			legendDisplay = false;
			break;
		default:
			legendDisplay = false;

	}

	if ( 'bar' === geojson.chartType ) {

		const barData = {
			labels: ['Goal', 'Completed'],
			datasets: [{
				label: '',
				data: [90, meetingCount],
				backgroundColor: [ geojson.colors.done, geojson.colors.remaining ],
			} ],
		};

		new Chart( ctx, {
			type: 'bar',
			data: barData,
			options: {
				responsive: true,
				legend: {
					display: legendDisplay
				},
				scales: {
					xAxes: [{
						stacked: true
					}],
					yAxes: [{
						stacked: true
					}]
				}
			}
		} );

	} else {

		if ( 0 > remaining ) {
			remaining = 0;
		}

		const data = {
			labels: [ 'Done', 'To go' ],
			datasets: [ {
				label: 'Meeting Count',
				data: [ meetingCount, remaining ],
				backgroundColor: [ geojson.colors.done, geojson.colors.remaining ],
			} ],
		};

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

	}

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
				var fontSize = ( height / 132 ).toFixed( 2 );// Was 114.  Increased to fit numbers over 100
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
