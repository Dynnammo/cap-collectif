// @flow
import React, { useRef, useState, useEffect } from 'react';
import { renderToString } from 'react-dom/server';
import noop from 'lodash/noop';
import { TileLayer, GeoJSON, Marker } from 'react-leaflet';
import { GestureHandling } from 'leaflet-gesture-handling';
import 'leaflet/dist/leaflet.css';
import 'leaflet-gesture-handling/dist/leaflet-gesture-handling.css';
import { useIntl } from 'react-intl';
import { connect } from 'react-redux';
import { createFragmentContainer, graphql } from 'react-relay';
import L from 'leaflet';
import { useResize } from '@liinkiing/react-hooks';
import MarkerClusterGroup from 'react-leaflet-markercluster';
import ZoomControl from './ZoomControl';
import type { State } from '~/types';
import type { MapTokens } from '~/redux/modules/user';
import ProposalMapPopover from './ProposalMapPopover';
import type { ProposalLeafletMap_proposals } from '~relay/ProposalLeafletMap_proposals.graphql';
import {
  StyledMap,
  BlankPopup,
  SliderPane,
  CLOSED_MARKER_SIZE,
  OPENED_MARKER_SIZE,
  locationMarkerCode,
  MapContainer,
} from './ProposalLeafletMap.style';
import { bootstrapGrid } from '~/utils/sizes';
import Address from '~/components/Form/Address/Address';
import type { AddressComplete } from '~/components/Form/Address/Address.type';
import ProposalMapLoaderPane from './ProposalMapLoaderPane';
import Icon, { ICON_NAME } from '~/components/Ui/Icons/Icon';
import colors from '~/utils/colors';
import { MAX_MAP_ZOOM } from '~/utils/styles/variables';

type MapCenterObject = {|
  lat: number,
  lng: number,
|};

export type MapProps = {
  flyTo: (Array<number>, ?number) => void,
  setView: (Array<number>, ?number) => void,
  panTo: (?Array<number> | null) => void,
  getPanes: () => { markerPane?: { children: Array<HTMLImageElement> } } | null,
  removeLayer: (typeof L.Marker) => void,
  on: (string, () => void) => void,
};
export type MapRef = {|
  +current: null | MapProps,
|};

export type MapOptions = {|
  center: MapCenterObject,
  zoom: number,
|};

type Style = {|
  border: {
    color: string,
    id: string,
    enabled: boolean,
    opacity: number,
    size: number,
    style_type: string,
  },
  background: {
    color: string,
    id: string,
    enabled: boolean,
    opacity: number,
    style_type: string,
  },
|};

export type GeoJson = {|
  district: string,
  style: Style,
|};

type Props = {|
  proposals: ProposalLeafletMap_proposals,
  mapTokens: MapTokens,
  geoJsons?: Array<GeoJson>,
  defaultMapOptions: MapOptions,
  visible: boolean,
  className?: string,
  hasMore: boolean,
  isLoading: boolean,
  hasError: boolean,
  retry: () => void,
  shouldDisplayPictures: boolean,
|};

const convertToGeoJsonStyle = (style: Style) => {
  const defaultDistrictStyle = {
    color: '#ff0000',
    weight: 1,
    opacity: 0.3,
  };

  if (!style.border && !style.background) {
    return defaultDistrictStyle;
  }

  const districtStyle = {};

  if (style.border) {
    districtStyle.color = style.border.color;
    districtStyle.weight = style.border.size;
    districtStyle.opacity = style.border.opacity / 100;
  }

  if (style.background) {
    districtStyle.fillColor = style.background.color;
    districtStyle.fillOpacity = style.background.opacity / 100;
  }

  return districtStyle || defaultDistrictStyle;
};

const goToPosition = (mapRef: MapRef, address: ?{| +lat: number, +lng: number |}) =>
  mapRef.current?.panTo([address?.lat || 0, address?.lng || 0]);

const locationIcon = L.divIcon({
  className: 'leaflet-control-locate-location',
  html: locationMarkerCode,
  iconSize: [48, 48],
});

let locationMarker: typeof L.Marker = {};

const flyToPosition = (mapRef: MapRef, lat: number, lng: number) => {
  if (mapRef.current) {
    mapRef.current.removeLayer(locationMarker);
  }
  if (mapRef.current) {
    mapRef.current.flyTo([lat, lng], 18);
  }
  locationMarker = L.marker([lat, lng], { icon: locationIcon }).addTo(mapRef.current);
};

const settingsSlider = {
  dots: false,
  infinite: true,
  speed: 500,
  centerPadding: '30px',
  centerMode: true,
  arrows: false,
};

let isOnCluster = false;
export const ProposalLeafletMap = ({
  geoJsons,
  defaultMapOptions,
  proposals,
  visible,
  mapTokens,
  className,
  hasMore,
  hasError,
  retry,
  shouldDisplayPictures,
}: Props) => {
  const intl = useIntl();
  const { publicToken, styleId, styleOwner } = mapTokens.MAPBOX;
  const mapRef = useRef(null);
  const slickRef = useRef(null);
  const [isMobileSliderOpen, setIsMobileSliderOpen] = useState(false);
  const [initialSlide, setInitialSlide] = useState<number | null>(null);
  const [address, setAddress] = useState(null);
  const { width } = useResize();
  const isMobile = width < bootstrapGrid.smMin;

  const markers = proposals.filter(
    proposal => !!(proposal.address && proposal.address.lat && proposal.address.lng),
  );

  useEffect(() => {
    L.Map.addInitHook('addHandler', 'gestureHandling', GestureHandling);
  }, []);

  if (!visible) {
    return null;
  }

  return (
    <MapContainer isMobile={isMobile}>
      <Address
        id="address"
        getPosition={(lat, lng) => flyToPosition(mapRef, lat, lng)}
        getAddress={(addressSelected: ?AddressComplete) =>
          addressSelected
            ? flyToPosition(
                mapRef,
                addressSelected.geometry.location.lat,
                addressSelected.geometry.location.lng,
              )
            : noop()
        }
        debounce={1200}
        value={address}
        onChange={setAddress}
        placeholder={intl.formatMessage({ id: 'proposal.map.form.placeholder' })}
      />
      <StyledMap
        whenCreated={(map: MapProps) => {
          mapRef.current = map;
          map.on('click', () => {
            setIsMobileSliderOpen(false);
            isOnCluster = false;
          });
          map.on('zoomstart', () => {
            setIsMobileSliderOpen(false);
            isOnCluster = false;
          });
        }}
        center={defaultMapOptions.center}
        zoom={defaultMapOptions.zoom}
        maxZoom={MAX_MAP_ZOOM}
        style={{
          height: isMobile ? '100vw' : '50vw',
          zIndex: 0,
        }}
        zoomControl={false}
        dragging={!L.Browser.mobile}
        tap={!L.Browser.mobile}
        className={className}
        doubleClickZoom={false}
        gestureHandling>
        <TileLayer
          attribution='&copy; <a href="https://www.mapbox.com/about/maps/">Mapbox</a> &copy; <a href="http://osm.org/copyright">OpenStreetMap</a> <a href="https://www.mapbox.com/map-feedback/#/-74.5/40/10">Improve this map</a>'
          url={`https://api.mapbox.com/styles/v1/${styleOwner}/${styleId}/tiles/256/{z}/{x}/{y}?access_token=${publicToken}`}
        />
        <MarkerClusterGroup
          spiderfyOnMaxZoom
          showCoverageOnHover={false}
          zoomToBoundsOnClick
          onClick={() => {
            isOnCluster = true;
          }}
          spiderfyDistanceMultiplier={4}
          maxClusterRadius={30}>
          {markers?.length > 0 &&
            markers.map((mark, key) => {
              const size = key === initialSlide ? OPENED_MARKER_SIZE : CLOSED_MARKER_SIZE;
              const icon = shouldDisplayPictures ? mark.category?.icon : null;
              const color = shouldDisplayPictures ? mark.category?.color || '#1E88E5' : '#1E88E5';
              return (
                <Marker
                  key={key}
                  position={[mark.address?.lat, mark.address?.lng]}
                  alt={`marker-${key}`}
                  icon={L.divIcon({
                    className: 'preview-icn',
                    html: renderToString(
                      <>
                        <Icon name={ICON_NAME.pin3} size={40} color={color} />
                        {icon && <Icon name={ICON_NAME[icon]} size={16} color={colors.white} />}
                      </>,
                    ),
                    iconSize: [size, size],
                    iconAnchor: [size / 2, size],
                    popupAnchor: [0, -size],
                  })}
                  eventHandlers={{
                    click: e => {
                      const isOpen: boolean = e.target.isPopupOpen();
                      if (!isOnCluster || isMobile) {
                        setInitialSlide(isOpen ? key : null);
                        setIsMobileSliderOpen(isOpen);
                        if (isMobile) {
                          goToPosition(mapRef, markers[key].address);
                          if (slickRef?.current) slickRef.current.slickGoTo(key);
                        }
                      }
                    },
                  }}>
                  <BlankPopup closeButton={false}>
                    <ProposalMapPopover proposal={mark} />
                  </BlankPopup>
                </Marker>
              );
            })}
        </MarkerClusterGroup>
        {geoJsons &&
          geoJsons.map((geoJson, key) => (
            <GeoJSON
              style={convertToGeoJsonStyle(geoJson.style)}
              key={key}
              data={geoJson.district}
            />
          ))}
        {!isMobile && <ZoomControl position="bottomright" />}
        {hasMore && <ProposalMapLoaderPane hasError={hasError} retry={retry} />}
      </StyledMap>
      {isMobileSliderOpen && isMobile && (
        <SliderPane
          ref={slickRef}
          {...settingsSlider}
          initialSlide={initialSlide !== null ? initialSlide : 0}
          afterChange={current => {
            setInitialSlide(current);
            // TODO find a better way
            // We need to wait leaflet to rerender the markers before moving
            setTimeout(() => goToPosition(mapRef, markers[current].address), 1);
          }}>
          {markers.map(marker => (
            <ProposalMapPopover proposal={marker} key={marker.id} isMobile />
          ))}
        </SliderPane>
      )}
    </MapContainer>
  );
};

ProposalLeafletMap.defaultProps = {
  proposals: [],
  defaultMapOptions: {
    center: { lat: 48.8586047, lng: 2.3137325 },
    zoom: 12,
    zoomControl: false,
  },
  visible: true,
};

const mapStateToProps = (state: State) => ({
  mapTokens: state.user.mapTokens,
  shouldDisplayPictures: state.default.features.display_pictures_in_depository_proposals_list,
});

const container = connect<any, any, _, _, _, _>(mapStateToProps)(ProposalLeafletMap);

export default createFragmentContainer(container, {
  proposals: graphql`
    fragment ProposalLeafletMap_proposals on Proposal @relay(plural: true) {
      address {
        lat
        lng
      }
      ...ProposalMapPopover_proposal
      category {
        color
        icon
      }
      id
    }
  `,
});
