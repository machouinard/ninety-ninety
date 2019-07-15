import meetingChart from '../../components/ninety-chart';
import meetingMap from '../../components/ninety-map';

window.onload = function () {

	// console.log( 'geojson', geojson );
	// console.log( 'mapOptions', mapOptions );

	// Conditionally display Meeting Chart.
	if ( geojson.showChart ) {
		meetingChart();
	}

	// Conditionally display Meeting Map.
	if ( mapOptions ) {
		meetingMap();
	}

};
