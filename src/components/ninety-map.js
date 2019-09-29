/**
 * Meeting map component
 */
const meetingMap = ( zoom ) => {
	// center: { lat: 38.5816, lng: -121.4944 } // Sacramento.
	const features = geojson.features,
		markers = [];
	const lMapOptions = {
		center: L.latLng( mapOptions.mapCenter ),
		zoom: zoom,
		maxZoom: 18,
		tapTolerance: 45,
	};

	// Create Map.
	const map = L.map( 'ninety-map', lMapOptions );
	// Create Marker Cluster Group.
	const markerClusterGroup = L.markerClusterGroup();
	// Tile Layer Options.
	const tilelayerOptions = {
		attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		maxZoom: 18,
		id: 'mapbox.streets',
		accessToken: mapOptions.apiKey,
	};
	// Create Tile Layer.
	const tiles = L.tileLayer( mapOptions.tileServer, tilelayerOptions );

	features.forEach(
		( feature ) => {
			const g = feature.geometry.coordinates,
				p = feature.properties;

			// CircleMarker Options.
			const circleMarkerOptions = {
				title: p.counts,
				riseOnHover: true,
				radius: 8,
				fillColor: '#ff7800',
				color: '#000',
				weight: 1,
				opacity: 1,
				fillOpacity: 0.8,
			};

			// Marker Options.
			const markerOptions = {
				title: p.counts,
				riseOnHover: true,
			};

			let meetingString = p.count > 1 ? 'meetings' : 'meeting';

			// Create popup content for Marker.
			let popupContent = `<h4><a href="${ p.link }" alt="Meeging title">${ p.title }</a></h4>`;
			popupContent += `<p>${ p.address }</p>`;
			popupContent += `<p>${ p.description }</p>`;
			popupContent += `<p>${ p.count + ' ' + meetingString} </p>`;
			// Create Circle Marker - Believe me, if markers aren't showing up don't waste time, just swap lat/lng!!!!!
			const marker = L.circleMarker( L.latLng( g[ 1 ], g[ 0 ] ), circleMarkerOptions );
			// Create Marker
			// const marker = L.marker( new L.latLng( g[ 1 ], g[ 0 ] ), markerOptions );
			// Bind tooltip to Marker
			marker.bindTooltip( p.title + '&nbsp;(' + parseInt( p.count ) + ')' );
			// Bind popup to Marker
			marker.bindPopup( popupContent );
			// Add Marker to array for later
			markers.push( marker );
		} );
// Add Tile Layer to the Map
	tiles.addTo( map );
// Add Markers to Cluster Group
	markerClusterGroup.addLayers( markers );
// Add Cluster Group to Map
	map.addLayer( markerClusterGroup );
};

export default meetingMap;
