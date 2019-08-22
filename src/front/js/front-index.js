import meetingChart from '../../components/ninety-chart';
import meetingMap from '../../components/ninety-map';

window.onload = function () {

	// console.log( 'geojson', geojson );
	// console.log( 'mapOptions', mapOptions );

	let showChart, zoom;

	// Grab chart container if it's available and set showChart.
	let chartContainer = document.getElementById( 'ninety-chart-container' );

	if ( null === chartContainer ) {
		showChart = false;
	} else {
		showChart = chartContainer.dataset.showChart;
	}

	let mapContainer = document.getElementById( 'ninety-map' );

	if ( null === mapContainer ) {
		mapOptions = false;
	} else {
		zoom = mapContainer.dataset.zoom;
		meetingMap( zoom );
	}

	// Conditionally display Meeting Chart.
	if ( showChart ) {
		meetingChart();
	}

};
