
// ✅ Version optimisée de NotificationManager
class NotificationManager {
    constructor() {
        this.pollInterval = 60000; // Réduit à 1 minute
        this.isPolling = false;
        this.cache = new Map();
        this.debounceTimer = null;
        this.pollTimer = null;

        // Éléments DOM
        this.notificationDropdown = document.querySelector('#notificationDropdown');
        this.notificationBadge = document.querySelector('#notificationBadge');
        this.notificationsList = document.querySelector('#notificationsList');

        this.init();
    }

    init() {
        // Chargement différé
        this.setupIntersectionObserver();

        // Polling intelligent (seulement si la page est visible)
        this.setupVisibilityHandler();

        // Événements
        this.setupEventListeners();

        // Chargement initial
        this.loadNotifications();
    }

    // ✅ NOUVEAU : Gestion de la visibilité de la page
    setupVisibilityHandler() {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopPolling();
            } else {
                this.startPolling();
                this.loadNotifications(); // Refresh immédiat au retour
            }
        });
    }

    // ✅ NOUVEAU : Intersection Observer pour le chargement paresseux
    setupIntersectionObserver() {
        if (!this.notificationDropdown) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.startPolling();
                } else {
                    this.stopPolling();
                }
            });
        }, {
            threshold: 0.1 // Déclenche quand 10% visible
        });

        observer.observe(this.notificationDropdown);
    }

    startPolling() {
        if (this.isPolling) return;

        this.isPolling = true;
        this.pollTimer = setInterval(() => {
            this.loadNotifications();
        }, this.pollInterval);
    }

    stopPolling() {
        this.isPolling = false;
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
    }

    // ✅ OPTIMISÉ : Requête avec cache et debounce
    async loadNotifications() {
        // Debounce pour éviter les requêtes trop fréquentes
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }

        this.debounceTimer = setTimeout(async () => {
            try {
                // Vérifier le cache local d'abord
                const cached = this.cache.get('notifications');
                if (cached && Date.now() - cached.timestamp < 30000) { // Cache 30s
                    this.updateUI(cached.data);
                    return;
                }

                const response = await fetch('/notifications/api/liste', {
                    method: 'GET',
                    headers: {
                        'Cache-Control': 'max-age=30',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();

                // Mettre en cache
                this.cache.set('notifications', {
                    data: data,
                    timestamp: Date.now()
                });

                this.updateUI(data);

            } catch (error) {
                console.error('Erreur notifications:', error);
                // Fallback sur les données en cache si disponibles
                const cached = this.cache.get('notifications');
                if (cached) {
                    this.updateUI(cached.data);
                }
            }
        }, 100); // Debounce de 100ms
    }

    // ✅ OPTIMISÉ : Batch DOM updates
    updateUI(data) {
        // Utiliser requestAnimationFrame pour optimiser les updates DOM
        requestAnimationFrame(() => {
            this.updateBadge(data.nombreNonLues);
            this.updateNotificationsList(data.notifications);
        });
    }

    updateBadge(count) {
        if (!this.notificationBadge) return;

        // Éviter les updates inutiles
        const currentCount = parseInt(this.notificationBadge.textContent) || 0;
        if (currentCount === count) return;

        this.notificationBadge.textContent = count;
        this.notificationBadge.style.display = count > 0 ? 'inline' : 'none';
    }

    // ✅ OPTIMISÉ : DocumentFragment pour les performances DOM
    updateNotificationsList(notifications) {
        if (!this.notificationsList) return;

        // Créer un fragment pour minimiser les reflows
        const fragment = document.createDocumentFragment();

        if (notifications.length === 0) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'dropdown-item text-center text-muted';
            emptyDiv.innerHTML = '<small>Aucune notification</small>';
            fragment.appendChild(emptyDiv);
        } else {
            notifications.forEach(notification => {
                const item = this.createNotificationItem(notification);
                fragment.appendChild(item);
            });
        }

        // Une seule manipulation DOM
        this.notificationsList.innerHTML = '';
        this.notificationsList.appendChild(fragment);
    }

    createNotificationItem(notification) {
        const div = document.createElement('div');
        div.className = `dropdown-item notification-item ${notification.isLue ? 'lue' : 'non-lue'}`;
        div.innerHTML = `
            <div class="d-flex align-items-start">
                <span class="me-2">${notification.typeIcon}</span>
                <div class="flex-grow-1">
                    <p class="mb-1 small">${notification.message}</p>
                    <small class="text-muted">${notification.dateRelative}</small>
                </div>
                <div class="notification-actions">
                    ${!notification.isLue ? `
                        <button class="btn btn-sm btn-outline-primary mark-read-btn" 
                                data-id="${notification.id}" title="Marquer comme lu">
                            ✓
                        </button>
                    ` : ''}
                </div>
            </div>
        `;

        // Redirection vers le signalement
        if (notification.signalementId) {
            div.addEventListener('click', (e) => {
                if (!e.target.classList.contains('mark-read-btn')) {
                    window.location.href = `/signalements/${notification.signalementId}`;
                }
            });
        }

        return div;
    }

    setupEventListeners() {
        // ✅ OPTIMISÉ : Event delegation pour les performances
        document.addEventListener('click', async (e) => {
            if (e.target.classList.contains('mark-read-btn')) {
                e.preventDefault();
                e.stopPropagation();

                const notificationId = e.target.dataset.id;
                await this.markAsRead(notificationId);

                // Invalider le cache pour forcer le rechargement
                this.cache.delete('notifications');
                this.loadNotifications();
            }
        });

        // Marquer toutes comme lues
        const markAllBtn = document.querySelector('#markAllRead');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', async () => {
                await this.markAllAsRead();
                this.cache.delete('notifications');
                this.loadNotifications();
            });
        }
    }

    async markAsRead(notificationId) {
        try {
            await fetch(`/notifications/${notificationId}/lire`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        } catch (error) {
            console.error('Erreur marquer comme lu:', error);
        }
    }

    async markAllAsRead() {
        try {
            await fetch('/notifications/marquer-toutes-lues', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        } catch (error) {
            console.error('Erreur marquer toutes comme lues:', error);
        }
    }

    // ✅ NOUVEAU : Méthode de nettoyage
    destroy() {
        this.stopPolling();
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }
        this.cache.clear();
    }
}

// ✅ OPTIMISÉ : Initialisation avec vérification de l'état de chargement
function initNotificationManager() {
    if (document.querySelector('#notificationDropdown')) {
        new NotificationManager();
    }
}

// Initialisation optimisée
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNotificationManager);
} else {
    initNotificationManager();
}