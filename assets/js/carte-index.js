// assets/js/carte-index.js

import { 
    BENIN_CENTER_LAT, 
    BENIN_CENTER_LNG, 
    BENIN_DEFAULT_ZOOM,
    DEFAULT_TILE_LAYER,
    DEFAULT_ATTRIBUTION
} from './map-config.js';

document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si l'élément map existe avant de l'initialiser
    const mapElement = document.getElementById('map');
    
    if (mapElement) {
        // Centrer la carte sur le Bénin avec les constantes importées
        var map = L.map('map').setView([BENIN_CENTER_LAT, BENIN_CENTER_LNG], BENIN_DEFAULT_ZOOM);

        // Ajouter le fond de carte OpenStreetMap
        L.tileLayer(DEFAULT_TILE_LAYER, {
            attribution: DEFAULT_ATTRIBUTION
        }).addTo(map);

        // Code pour charger les signalements via AJAX...
        
        // Fonction pour filtrer les signalements
        const filterMarkers = function() {
            // Logique de filtrage...
        };
        
        // Configuration des écouteurs d'événements pour les filtres
        document.querySelectorAll('.filter-ville, .filter-categorie').forEach(checkbox => {
            checkbox.addEventListener('change', filterMarkers);
        });
        
        console.log('Carte initialisée avec succès');
    } else {
        console.warn('Élément #map non trouvé dans le DOM');
    }
});