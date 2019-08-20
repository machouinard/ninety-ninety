import meetingChart from '../../components/ninety-chart';
import meetingMap from '../../components/ninety-map';

window.onload = function () {

	// console.log( 'geojson', geojson );
	// console.log( 'mapOptions', mapOptions );
	let showChart;

	// Grab chart container if it's available and set showChart
	let chartContainer = document.getElementById( 'ninety-chart-container' );
	if ( null === chartContainer ) {
		showChart = false;
	} else {
		showChart = chartContainer.dataset.showChart;
	}

	// console.log( 'showChart', showChart );

	// Conditionally display Meeting Chart.
	if ( showChart ) {
		meetingChart();
	}

	// Conditionally display Meeting Map.
	if ( mapOptions ) {
		meetingMap();
	}

};
