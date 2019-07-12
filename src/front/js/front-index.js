console.log( 'geojson', geojson );
console.log( 'mapOptions', mapOptions );

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

L.tileLayer( mapOptions.tileServer, {
	attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
	maxZoom: 18,
	id: 'mapbox.streets',
	accessToken: mapOptions.apiKey,
} ).addTo( map );

function onEachFeature( feature, layer ) {
	let popupContent = '<h3><a href="' + feature.properties.link + '" >' + feature.properties.title + '</a></h3>';
	popupContent += '<p>' + feature.properties.address + '</p>';
	popupContent += '<p>' + feature.properties.count + '</p>';
	popupContent += '<p>' + feature.properties.description + '</p>';
	layer.bindPopup( popupContent );
}

L.geoJSON( geojson.features, {
	onEachFeature,
	pointToLayer( feature, latlng ) {
		return L.circleMarker( latlng, geojsonMarkerOptions );
	},
} ).addTo( map );
