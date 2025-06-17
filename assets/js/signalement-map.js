// assets/js/signalements-map.js

import { 
    BENIN_CENTER_LAT, 
    BENIN_CENTER_LNG, 
    BENIN_DEFAULT_ZOOM,
    BENIN_BOUNDS,
    DEFAULT_TILE_LAYER,
    DEFAULT_ATTRIBUTION
} from './map-config.js';

// Utiliser les constantes importées
let defaultLat = BENIN_CENTER_LAT;
let defaultLng = BENIN_CENTER_LNG;
let defaultZoom = BENIN_DEFAULT_ZOOM;
let map, marker;

// Initialisation de la carte
document.addEventListener('DOMContentLoaded', function() {
    const mapElement = document.getElementById('map');
    
    if (mapElement) {
        initMap();
        setupListeners();
        initializeFromForm();
    } else {
        console.warn('Élément #map non trouvé dans le DOM');
    }
});

// Initialisation de la carte Leaflet
function initMap() {
    // Création de la carte avec les coordonnées du Bénin
    map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);
    
    // Ajout du layer OpenStreetMap
    L.tileLayer(DEFAULT_TILE_LAYER, {
        attribution: DEFAULT_ATTRIBUTION
    }).addTo(map);
    
    // Définir des limites de navigation
    const southWest = L.latLng(BENIN_BOUNDS.southWest.lat, BENIN_BOUNDS.southWest.lng);
    const northEast = L.latLng(BENIN_BOUNDS.northEast.lat, BENIN_BOUNDS.northEast.lng);
    const bounds = L.latLngBounds(southWest, northEast);
    
    map.setMaxBounds(bounds);
    
    // Ajout du marqueur initial
    marker = L.marker([defaultLat, defaultLng], {
        draggable: true
    }).addTo(map);
    
    // Mise à jour des coordonnées quand on déplace le marqueur
    marker.on('dragend', function(event) {
        updateCoordinates(marker.getLatLng().lat, marker.getLatLng().lng);
    });
    
    // Ajout d'un clic sur la carte pour déplacer le marqueur
    map.on('click', function(event) {
        marker.setLatLng(event.latlng);
        updateCoordinates(event.latlng.lat, event.latlng.lng);
    });
    
    // Initialiser les coordonnées avec les valeurs par défaut
    updateCoordinates(defaultLat, defaultLng);
}

// Ajouter cette fonction après initMap()
function updateCoordinates(lat, lng) {
    // Mettre à jour les champs du formulaire
    document.getElementById('signalement_type_form_latitude').value = lat;
    document.getElementById('signalement_type_form_longitude').value = lng;
    
    // Mettre à jour les champs visibles (facultatif)
    const manualLatInput = document.getElementById('manual-latitude');
    const manualLngInput = document.getElementById('manual-longitude');
    
    if (manualLatInput && manualLngInput) {
        manualLatInput.value = lat;
        manualLngInput.value = lng;
    }
    
    // Mettre à jour la position du marqueur si nécessaire
    if (marker) {
        marker.setLatLng([lat, lng]);
    }
    
    // Centrer la carte sur les nouvelles coordonnées
    if (map) {
        map.panTo([lat, lng]);
    }
}
// Ajouter cette fonction également
function setupListeners() {
    // Géolocalisation de l'utilisateur
    const geolocateBtn = document.getElementById('geolocate-btn');
    if (geolocateBtn) {
        geolocateBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        // Vérifier si les coordonnées sont dans les limites du Bénin
                        if (lat >= BENIN_BOUNDS.southWest.lat && lat <= BENIN_BOUNDS.northEast.lat &&
                            lng >= BENIN_BOUNDS.southWest.lng && lng <= BENIN_BOUNDS.northEast.lng) {
                            updateCoordinates(lat, lng);
                        } else {
                            alert('Votre position actuelle est en dehors des limites de la carte.');
                        }
                    },
                    function(error) {
                        console.error('Erreur de géolocalisation:', error);
                        alert('Impossible d\'obtenir votre position actuelle.');
                    }
                );
            } else {
                alert('La géolocalisation n\'est pas prise en charge par votre navigateur.');
            }
        });
    }
    
    // Appliquer les coordonnées manuelles
    const applyCoordsBtn = document.getElementById('apply-coords-btn');
    if (applyCoordsBtn) {
        applyCoordsBtn.addEventListener('click', function() {
            const manualLatInput = document.getElementById('manual-latitude');
            const manualLngInput = document.getElementById('manual-longitude');
            
            if (manualLatInput && manualLngInput) {
                const lat = parseFloat(manualLatInput.value);
                const lng = parseFloat(manualLngInput.value);
                
                if (!isNaN(lat) && !isNaN(lng)) {
                    updateCoordinates(lat, lng);
                } else {
                    alert('Veuillez saisir des coordonnées valides.');
                }
            }
        });
    }
}
function initializeFromForm() {
    // Récupérer les valeurs initiales depuis le formulaire s'il y en a
    const latField = document.getElementById('signalement_type_form_latitude');
    const lngField = document.getElementById('signalement_type_form_longitude');
    
    if (latField && lngField && latField.value && lngField.value) {
        const lat = parseFloat(latField.value);
        const lng = parseFloat(lngField.value);
        
        if (!isNaN(lat) && !isNaN(lng)) {
            updateCoordinates(lat, lng);
        }
    }
}
// Reste du code existant...