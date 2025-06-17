import './styles/app.scss';

// Import de Bootstrap
import 'bootstrap';

// Import Leaflet
import './js/leaflet-setup';

// Fonction pour mettre à jour les arrondissements
function updateArrondissements() {
    const villeSelect = document.getElementById('ville-select'); // Assurez-vous que l'ID correspond
    const arrondissementSelect = document.getElementById('arrondissement-select'); // Assurez-vous que l'ID correspond
    
    if (!villeSelect || !arrondissementSelect) return;
    
    const villeId = villeSelect.value;
    
    // Désactiver le select d'arrondissement pendant le chargement
    arrondissementSelect.disabled = true;
    
    if (villeId) {
        // Faire une requête AJAX pour obtenir les arrondissements de la ville sélectionnée
        fetch(`/api/arrondissements-by-ville/${villeId}`)
            .then(response => response.json())
            .then(data => {
                // Vider le select d'arrondissement
                arrondissementSelect.innerHTML = '<option value="">Sélectionnez un arrondissement (optionnel)</option>';
                
                // Ajouter les nouveaux arrondissements
                data.forEach(arrondissement => {
                    const option = document.createElement('option');
                    option.value = arrondissement.id;
                    option.textContent = arrondissement.nom;
                    arrondissementSelect.appendChild(option);
                });
                
                // Réactiver le select d'arrondissement
                arrondissementSelect.disabled = false;
            });
    } else {
        // Si aucune ville n'est sélectionnée, vider le select d'arrondissement
        arrondissementSelect.innerHTML = '<option value="">Sélectionnez d\'abord une ville</option>';
        arrondissementSelect.disabled = true;
    }
}

// Ajouter un écouteur d'événement au select de ville
document.addEventListener('DOMContentLoaded', function() {
    const villeSelect = document.getElementById('ville-select');
    if (villeSelect) {
        villeSelect.addEventListener('change', updateArrondissements);
    }
});

// Activation des fonctionnalités Bootstrap
document.addEventListener('DOMContentLoaded', () => {
    // Activation des tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Activation des popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Gestion des alertes avec fermeture automatique
    const alerts = document.querySelectorAll('.alert-auto-close');
    alerts.forEach(alert => {
        setTimeout(() => {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        }, 5000);
    });


    //Arrondissement

    const villeSelect = document.querySelector('[name$="[ville]"]');
    const arrondissementSelect = document.querySelector('[name$="[arrondissement]"]');

    if (villeSelect && arrondissementSelect) {
        // Fonction pour mettre à jour les arrondissements en fonction de la ville sélectionnée
        const updateArrondissements = function() {
            const villeId = villeSelect.value;

            // Désactiver le select d'arrondissement pendant le chargement
            arrondissementSelect.disabled = true;

            if (villeId) {
                // Faire une requête AJAX pour obtenir les arrondissements de la ville sélectionnée
                fetch(`/api/arrondissements-by-ville/${villeId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Vider le select d'arrondissement
                        arrondissementSelect.innerHTML = '<option value="">Sélectionnez un arrondissement (optionnel)</option>';

                        // Ajouter les nouveaux arrondissements
                        data.forEach(arrondissement => {
                            const option = document.createElement('option');
                            option.value = arrondissement.id;
                            option.textContent = arrondissement.nom;
                            arrondissementSelect.appendChild(option);
                        });

                        // Réactiver le select d'arrondissement
                        arrondissementSelect.disabled = false;
                    });
            } else {
                // Si aucune ville n'est sélectionnée, vider le select d'arrondissement
                arrondissementSelect.innerHTML = '<option value="">Sélectionnez d\'abord une ville</option>';
                arrondissementSelect.disabled = true;
            }
        };

        // Mettre à jour les arrondissements lors du chargement de la page
        updateArrondissements();

        // Mettre à jour les arrondissements lorsque la ville change
        villeSelect.addEventListener('change', updateArrondissements);
    }

});

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

console.log('App.js loaded successfully');