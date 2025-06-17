// ✅ Utilitaire de chargement paresseux général
class LazyLoader {
    constructor() {
        this.observers = new Map();
        this.loadedElements = new WeakSet();
    }

    // ✅ Observer pour les images
    observeImages() {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.loadedElements.has(entry.target)) {
                    this.loadImage(entry.target);
                    imageObserver.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '50px' // Charger 50px avant d'être visible
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });

        this.observers.set('images', imageObserver);
    }

    // ✅ Observer pour les cartes/sections lourdes
    observeHeavyContent() {
        const contentObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadHeavyContent(entry.target);
                    contentObserver.unobserve(entry.target);
                }
            });
        });

        document.querySelectorAll('[data-lazy-load]').forEach(element => {
            contentObserver.observe(element);
        });

        this.observers.set('content', contentObserver);
    }

    loadImage(img) {
        const src = img.dataset.src;
        if (src) {
            img.src = src;
            img.classList.add('loaded');
            this.loadedElements.add(img);
        }
    }

    loadHeavyContent(element) {
        const loadType = element.dataset.lazyLoad;

        switch (loadType) {
            case 'map':
                this.loadMap(element);
                break;
            case 'chart':
                this.loadChart(element);
                break;
            case 'table':
                this.loadTable(element);
                break;
        }
    }

    loadMap(element) {
        // Charger dynamiquement Leaflet si nécessaire
        import('leaflet').then(L => {
            // Initialiser la carte
            console.log('Carte chargée paresseusement');
        });
    }

    loadChart(element) {
        // Charger Chart.js si nécessaire
        console.log('Graphique chargé paresseusement');
    }

    loadTable(element) {
        // Charger des données de tableau lourdes
        console.log('Tableau chargé paresseusement');
    }

    destroy() {
        this.observers.forEach(observer => observer.disconnect());
        this.observers.clear();
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    const lazyLoader = new LazyLoader();
    lazyLoader.observeImages();
    lazyLoader.observeHeavyContent();
});