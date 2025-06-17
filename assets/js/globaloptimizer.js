// ✅ Optimiseur global ultra-moderne pour CityFlow
class GlobalOptimizer {
    constructor() {
        this.performance = {
            metrics: new Map(),
            observers: new Map(),
            cache: new Map()
        };

        this.config = {
            lazy: {
                imageMargin: '50px',
                contentMargin: '100px'
            },
            debounce: {
                scroll: 16,     // 60fps
                resize: 250,
                input: 300
            },
            cache: {
                ttl: 300000,    // 5 minutes
                maxSize: 100
            }
        };

        this.init();
    }

    init() {
        this.measurePerformance();
        this.setupLazyLoading();
        this.optimizeEventListeners();
        this.setupSmartCache();
        this.optimizeImages();
        this.preloadCriticalResources();
        this.setupConnectionAwareness();
    }

    // 📊 Mesure de performance automatique
    measurePerformance() {
        // Web Vitals
        if ('web-vital' in window) {
            import('web-vitals').then(({ getCLS, getFID, getFCP, getLCP, getTTFB }) => {
                getCLS(this.logMetric.bind(this, 'CLS'));
                getFID(this.logMetric.bind(this, 'FID'));
                getFCP(this.logMetric.bind(this, 'FCP'));
                getLCP(this.logMetric.bind(this, 'LCP'));
                getTTFB(this.logMetric.bind(this, 'TTFB'));
            });
        }

        // Navigation Timing
        window.addEventListener('load', () => {
            const navigation = performance.getEntriesByType('navigation')[0];
            this.logMetric('LoadTime', { value: navigation.loadEventEnd - navigation.loadEventStart });
        });
    }

    logMetric(name, metric) {
        this.performance.metrics.set(name, metric);
        console.log(`📊 ${name}:`, metric.value);
    }

    // 🖼️ Lazy Loading Ultra-Intelligent
    setupLazyLoading() {
        // Images avec placeholder élégant
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    imageObserver.unobserve(entry.target);
                }
            });
        }, { rootMargin: this.config.lazy.imageMargin });

        // Observer tous les éléments avec data-src
        document.querySelectorAll('img[data-src], [data-background]').forEach(el => {
            imageObserver.observe(el);
        });

        // Contenu lourd (cartes, graphiques, etc.)
        const contentObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadHeavyContent(entry.target);
                    contentObserver.unobserve(entry.target);
                }
            });
        }, { rootMargin: this.config.lazy.contentMargin });

        document.querySelectorAll('[data-lazy]').forEach(el => {
            contentObserver.observe(el);
        });

        this.performance.observers.set('images', imageObserver);
        this.performance.observers.set('content', contentObserver);
    }

    loadImage(element) {
        const src = element.dataset.src || element.dataset.background;
        if (!src) return;

        // Créer une image temporaire pour le préchargement
        const img = new Image();
        img.onload = () => {
            if (element.tagName === 'IMG') {
                element.src = src;
            } else {
                element.style.backgroundImage = `url(${src})`;
            }

            element.classList.add('loaded');
            element.removeAttribute('data-src');
            element.removeAttribute('data-background');
        };

        img.onerror = () => {
            element.classList.add('error');
        };

        img.src = src;
    }

    loadHeavyContent(element) {
        const type = element.dataset.lazy;

        switch (type) {
            case 'map':
                this.loadMapComponent(element);
                break;
            case 'chart':
                this.loadChartComponent(element);
                break;
            case 'table':
                this.loadTableComponent(element);
                break;
            default:
                this.loadGenericComponent(element);
        }
    }

    // 🗺️ Chargement intelligent des cartes
    async loadMapComponent(element) {
        try {
            const { default: L } = await import('leaflet');

            const mapId = element.id || 'map-' + Date.now();
            const lat = element.dataset.lat || 6.3703;
            const lng = element.dataset.lng || 2.3912;
            const zoom = element.dataset.zoom || 13;

            const map = L.map(mapId).setView([lat, lng], zoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            element.classList.add('loaded');
            console.log('🗺️ Carte chargée paresseusement');
        } catch (error) {
            console.error('Erreur lors du chargement de la carte:', error);
            element.classList.add('error');
        }
    }

    // 📈 Chargement des graphiques
    async loadChartComponent(element) {
        try {
            // Charger Chart.js dynamiquement
            const { default: Chart } = await import('chart.js/auto');

            const ctx = element.getContext('2d');
            const config = JSON.parse(element.dataset.config || '{}');

            new Chart(ctx, config);
            element.classList.add('loaded');
            console.log('📈 Graphique chargé paresseusement');
        } catch (error) {
            console.error('Erreur lors du chargement du graphique:', error);
            element.classList.add('error');
        }
    }

    // 📊 Chargement des tableaux lourds
    async loadTableComponent(element) {
        try {
            const url = element.dataset.url;
            const response = await this.fetchWithCache(url);

            element.innerHTML = response;
            element.classList.add('loaded');
            console.log('📊 Tableau chargé paresseusement');
        } catch (error) {
            console.error('Erreur lors du chargement du tableau:', error);
            element.classList.add('error');
        }
    }

    loadGenericComponent(element) {
        const script = element.dataset.script;
        if (script && window[script] && typeof window[script] === 'function') {
            window[script](element);
            element.classList.add('loaded');
        }
    }

    // ⚡ Optimisation des événements
    optimizeEventListeners() {
        // Scroll optimisé
        let scrollTimer;
        window.addEventListener('scroll', () => {
            if (scrollTimer) return;

            scrollTimer = setTimeout(() => {
                document.dispatchEvent(new CustomEvent('optimized-scroll'));
                scrollTimer = null;
            }, this.config.debounce.scroll);
        }, { passive: true });

        // Resize optimisé
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                document.dispatchEvent(new CustomEvent('optimized-resize'));
            }, this.config.debounce.resize);
        });

        // Input optimisé
        this.optimizeInputs();
    }

    optimizeInputs() {
        const inputs = document.querySelectorAll('input, textarea, select');

        inputs.forEach(input => {
            let inputTimer;

            input.addEventListener('input', () => {
                clearTimeout(inputTimer);
                inputTimer = setTimeout(() => {
                    input.dispatchEvent(new CustomEvent('optimized-input', {
                        detail: { value: input.value }
                    }));
                }, this.config.debounce.input);
            });
        });
    }

    // 💾 Cache intelligent
    setupSmartCache() {
        this.performance.cache = new Map();
    }

    async fetchWithCache(url, options = {}) {
        const cacheKey = url + JSON.stringify(options);
        const cached = this.performance.cache.get(cacheKey);

        if (cached && Date.now() - cached.timestamp < this.config.cache.ttl) {
            return cached.data;
        }

        try {
            const response = await fetch(url, options);
            const data = await response.text();

            // Nettoyer le cache si trop gros
            if (this.performance.cache.size >= this.config.cache.maxSize) {
                const firstKey = this.performance.cache.keys().next().value;
                this.performance.cache.delete(firstKey);
            }

            this.performance.cache.set(cacheKey, {
                data,
                timestamp: Date.now()
            });

            return data;
        } catch (error) {
            console.error('Erreur de cache:', error);
            throw error;
        }
    }

    // 🖼️ Optimisation des images
    optimizeImages() {
        // Formats modernes supportés
        const supportsWebP = this.supportsFormat('webp');
        const supportsAVIF = this.supportsFormat('avif');

        document.querySelectorAll('img[data-optimize]').forEach(img => {
            const baseSrc = img.dataset.optimize;

            if (supportsAVIF) {
                img.src = baseSrc.replace(/\.(jpg|jpeg|png)$/, '.avif');
            } else if (supportsWebP) {
                img.src = baseSrc.replace(/\.(jpg|jpeg|png)$/, '.webp');
            } else {
                img.src = baseSrc;
            }
        });
    }

    supportsFormat(format) {
        const canvas = document.createElement('canvas');
        canvas.width = 1;
        canvas.height = 1;
        return canvas.toDataURL(`image/${format}`).indexOf(`data:image/${format}`) === 0;
    }

    // 🚀 Préchargement intelligent
    preloadCriticalResources() {
        const criticalUrls = [
            '/api/categories',
            '/api/villes',
            '/api/user/notifications'
        ];

        // Précharger uniquement si la connexion est bonne
        if (this.isGoodConnection()) {
            criticalUrls.forEach(url => {
                this.fetchWithCache(url).catch(() => {});
            });
        }
    }

    // 📡 Conscience de la connexion
    setupConnectionAwareness() {
        if ('connection' in navigator) {
            const connection = navigator.connection;

            this.adaptToConnection(connection);

            connection.addEventListener('change', () => {
                this.adaptToConnection(connection);
            });
        }
    }

    adaptToConnection(connection) {
        const isSlowConnection = connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g';

        document.body.classList.toggle('slow-connection', isSlowConnection);

        if (isSlowConnection) {
            // Réduire la qualité des images
            document.querySelectorAll('img[data-quality]').forEach(img => {
                img.src = img.src.replace(/quality=\d+/, 'quality=50');
            });

            // Désactiver certaines animations
            document.body.style.setProperty('--animation-duration', '0s');
        }
    }

    isGoodConnection() {
        if ('connection' in navigator) {
            const connection = navigator.connection;
            return !['slow-2g', '2g'].includes(connection.effectiveType);
        }
        return true;
    }

    // 🧹 Nettoyage
    destroy() {
        this.performance.observers.forEach(observer => observer.disconnect());
        this.performance.cache.clear();
    }

    // 📊 Rapport de performance
    getPerformanceReport() {
        return {
            metrics: Object.fromEntries(this.performance.metrics),
            cacheSize: this.performance.cache.size,
            observersCount: this.performance.observers.size
        };
    }
}

// 🎯 Initialisation globale
document.addEventListener('DOMContentLoaded', () => {
    window.globalOptimizer = new GlobalOptimizer();

    // Exposer pour debug
    window.performanceReport = () => {
        console.table(window.globalOptimizer.getPerformanceReport());
    };
});

export default GlobalOptimizer;