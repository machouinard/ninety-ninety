console.log( 'geojson', geojson );

const geojsonMarkerOptions = {
	radius: 8,
	fillColor: '#ff7800',
	color: '#000',
	weight: 1,
	opacity: 1,
	fillOpacity: 0.8,
};
//center: { lat: 38.5816, lng: -121.4944 } // Sacramento
const map = L.map( 'ninety-map' ).setView( [ 38.5816, -121.4944 ], 10 );

L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
} ).addTo( map );

function onEachFeature( feature, layer ) {
	let popupContent = '<p>dsfasdfffasdf</p>';

	layer.bindPopup( popupContent );
}

L.geoJSON( geojson.features, {
	onEachFeature,
	pointToLayer: function( feature, latlng ) {
		return L.circleMarker( latlng, geojsonMarkerOptions );
	},
} ).addTo( map );
