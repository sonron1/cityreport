// assets/js/map-config.js

// Configuration des coordonnées pour le Bénin
export const BENIN_CENTER_LAT = 9.3217;
export const BENIN_CENTER_LNG = 2.3100;
export const BENIN_DEFAULT_ZOOM = 7;

// Limites géographiques du Bénin (pour la restriction de navigation)
export const BENIN_BOUNDS = {
  southWest: { lat: 5.5, lng: 0.5 },  // Sud-ouest
  northEast: { lat: 12.5, lng: 4.0 }  // Nord-est
};

// Autres configurations communes
export const DEFAULT_TILE_LAYER = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
export const DEFAULT_ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';