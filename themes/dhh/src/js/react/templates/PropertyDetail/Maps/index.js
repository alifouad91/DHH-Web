import React from 'react';
import _ from 'lodash';
import { Skeleton, Icon } from 'antd';
import { GoogleApiWrapper, Map, InfoWindow, Marker } from 'google-maps-react';
import config from '../../../config';
import { Metro } from '../../../icons';

export class MapContainer extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			places: [],
			distance: null,
			metroStation: null,
			marker: null,
			finished: false,
		};
	}
	fetchPlaces = (mapProps, map) => {
		const { lat, lng } = this.props;
		const { google } = mapProps;
		const service = new google.maps.places.PlacesService(map);
		const loc = new google.maps.LatLng(lat, lng);
		service.textSearch(
			{
				query: 'attractions',
				radius: 200,
				location: loc,
			},
			(results, status) => {
				if (status === google.maps.places.PlacesServiceStatus.OK) {
					const places = _.slice(_.orderBy(results, 'rating', 'desc'), 0, 4);
					this.setState({ places });
				}
			}
		);

		service.textSearch(
			{
				query: 'nearest metro station',
				radius: 500,
				location: loc,
			},
			(results, status) => {
				if (status === google.maps.places.PlacesServiceStatus.OK) {
					const location = results[0].geometry.location;
					this.setState({ metroStation: results[0].name });

					const matrix = new google.maps.DistanceMatrixService();
					matrix.getDistanceMatrix(
						{
							origins: [new google.maps.LatLng(lat, lng)],
							destinations: [new google.maps.LatLng(location.lat(), location.lng())],
							travelMode: 'WALKING',
						},
						(results, status) => {
							if (status === 'OK') {
								this.setState({
									distance: results.rows[0].elements[0].distance.text,
									finished: true,
								});
							}
						}
					);
				}
			}
		);
	};
	render() {
		const { lat, lng } = this.props;
		const { places, finished, distance, metroStation } = this.state;

		const defaultIcon = {
			url: `${CCM_REL}/themes/dhh/dist/images/marker.png`,
			size: new google.maps.Size(150, 150),
			anchor: new google.maps.Point(75, 75),
			scaledSize: new this.props.google.maps.Size(150, 150),
		};

		return (
			<div className="page__propertydetails__map">
				<h6>Nearby Attractions</h6>
				<div className="page__propertydetails__map__features">
					<span>
						{places.length
							? _.map(places, (place) => {
									return place.name;
							  }).join(' • ')
							: null}
					</span>
					{finished ? (
						<div className="metro-distance">
							<Icon component={Metro} />
							<b>{distance}</b>
							<span> to </span>
							<b>{metroStation}</b>
							<span> station </span>
						</div>
					) : null}
				</div>
				<div className="page__propertydetails__map__google">
					<Map
						google={this.props.google}
						zoom={13}
						initialCenter={{
							lat,
							lng,
						}}
						mapCenter={{
							lat,
							lng,
						}}
						onReady={this.fetchPlaces}
					>
						<Marker icon={defaultIcon} position={new google.maps.LatLng(lat, lng)} />
					</Map>
				</div>
			</div>
		);
	}
}

const LoadingContainer = (props) => (
	<div>
		<Skeleton />
	</div>
);
export default GoogleApiWrapper({
	apiKey: config.GOOGLE_MAPS_KEY,
	LoadingContainer,
})(MapContainer);
