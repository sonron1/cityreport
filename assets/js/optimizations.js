// ✅ Optimisations globales de performance
class PerformanceOptimizer {
    constructor() {
        this.debounceTimers = new Map();
        this.throttleTimers = new Map();

        this.init();
    }

    init() {
        this.optimizeScrollEvents();
        this.optimizeResizeEvents();
        this.preloadCriticalResources();
        this.setupConnectionOptimizations();
    }

    // ✅ Debounce générique
    debounce(func, wait, key) {
        return (...args) => {
            if (this.debounceTimers.has(key)) {
                clearTimeout(this.debounceTimers.get(key));
            }

            const timer = setTimeout(() => {
                func.apply(this, args);
                this.debounceTimers.delete(key);
            }, wait);

            this.debounceTimers.set(key, timer);
        };
    }

    // ✅ Throttle générique
    throttle(func, limit, key) {
        return (...args) => {
            if (!this.throttleTimers.has(key)) {
                func.apply(this, args);
                this.throttleTimers.set(key, true);

                setTimeout(() => {
                    this.throttleTimers.delete(key);
                }, limit);
            }
        };
    }

    optimizeScrollEvents() {
        // Optimiser tous les événements scroll
        const scrollHandler = this.throttle(() => {
            // Logique de scroll
        }, 16, 'scroll'); // 60fps

        window.addEventListener('scroll', scrollHandler, { passive: true });
    }

    optimizeResizeEvents() {
        // Optimiser les événements resize
        const resizeHandler = this.debounce(() => {
            // Logique de resize
        }, 250, 'resize');

        window.addEventListener('resize', resizeHandler);
    }

    preloadCriticalResources() {
        // Précharger les ressources critiques
        const criticalResources = [
            '/api/categories',
            '/api/villes'
        ];

        criticalResources.forEach(url => {
            fetch(url, {
                method: 'GET',
                headers: { 'Cache-Control': 'max-age=300' }
            }).catch(() => {
                // Ignorer les erreurs de préchargement
            });
        });
    }

    setupConnectionOptimizations() {
        // Optimisations basées sur la connexion
        if ('connection' in navigator) {
            const connection = navigator.connection;

            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                // Réduire la fréquence de polling pour les connexions lentes
                document.dispatchEvent(new CustomEvent('slow-connection'));
            }
        }
    }
}

// Initialisation globale
window.performanceOptimizer = new PerformanceOptimizer();