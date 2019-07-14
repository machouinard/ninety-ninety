import Chart from 'chart.js';

const meetingChart = () => {
	const ctx = document.getElementById( 'ninety-chart' );
	const meetingCount = geojson.meetingCount;
	const remaining = 90 - meetingCount;

	console.log( 'from ninety-chart.js' );

	new Chart( ctx, {
		type: 'pie',
		data: {
			labels: [ 'done', 'remaining' ],
			datasets: [ {
				label: 'Meeting Count',
				data: [ meetingCount, remaining ],
				backgroundColor: [ 'green', 'red' ],
			} ],
		},
	} );
};

export default meetingChart;
