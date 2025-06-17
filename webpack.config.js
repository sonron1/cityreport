const Encore = require('@symfony/webpack-encore');
const WorkboxPlugin = require('workbox-webpack-plugin');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // ✅ Configuration de base
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // ✅ Points d'entrée optimisés
    .addEntry('app', './assets/app.js')
    .addEntry('global-optimizer', './assets/js/global-optimizer.js')
    .addEntry('critical', './assets/js/critical.js')

    // ✅ Chargement conditionnel
    .addEntry('admin', './assets/js/admin.js')
    .addEntry('maps', './assets/js/maps.js')
    .addEntry('charts', './assets/js/charts.js')

    // ✅ Styles optimisés
    .addStyleEntry('app-styles', './assets/styles/app.scss')
    .addStyleEntry('performance-styles', './assets/styles/performance.scss')
    .addStyleEntry('critical-styles', './assets/styles/critical.scss')

    // ✅ Optimisations avancées
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    // ✅ Loaders
    .enableSassLoader()
    .enablePostCssLoader()

    // ✅ Copie optimisée des assets
    .copyFiles([
        {
            from: './node_modules/leaflet/dist/images',
            to: 'images/leaflet/[path][name].[ext]'
        },
        {
            from: './assets/images',
            to: 'images/[path][name].[hash:8].[ext]',
            pattern: /\.(png|jpg|jpeg|svg|gif|webp|avif)$/
        },
        {
            from: './assets/videos',
            to: 'videos/[path][name].[hash:8].[ext]',
            pattern: /\.(mp4|webm|ogg)$/
        }
    ])

    // ✅ Babel optimisé
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
        config.targets = '> 0.25%, not dead';
    })

    // ✅ Compression ultra-optimisée
    .configureTerserPlugin((options) => {
        options.parallel = true;
        options.terserOptions = {
            compress: {
                drop_console: Encore.isProduction(),
                drop_debugger: Encore.isProduction(),
                pure_funcs: Encore.isProduction() ? ['console.log', 'console.info'] : [],
            },
            mangle: {
                safari10: true,
            },
            output: {
                safari10: true,
            },
        };
    })

    // ✅ CSS optimisé
    .configureCssLoader((config) => {
        if (Encore.isProduction()) {
            config.minimize = true;
        }
    })

    // ✅ Dev server optimisé
    .configureDevServerOptions(options => {
        options.client = {
            overlay: {
                warnings: false,
                errors: true
            }
        };
        options.hot = true;
        options.liveReload = false;
    })
;

// ✅ Configuration avancée pour la production
const config = Encore.getWebpackConfig();

if (Encore.isProduction()) {
    // Service Worker pour le cache
    config.plugins.push(
        new WorkboxPlugin.GenerateSW({
            clientsClaim: true,
            skipWaiting: true,
            runtimeCaching: [
                {
                    urlPattern: /^https:\/\/fonts\.googleapis\.com/,
                    handler: 'StaleWhileRevalidate',
                    options: {
                        cacheName: 'google-fonts-stylesheets',
                    },
                },
                {
                    urlPattern: /^https:\/\/fonts\.gstatic\.com/,
                    handler: 'CacheFirst',
                    options: {
                        cacheName: 'google-fonts-webfonts',
                        expiration: {
                            maxEntries: 30,
                            maxAgeSeconds: 60 * 60 * 24 * 365, // 1 an
                        },
                    },
                },
                {
                    urlPattern: /\.(?:png|jpg|jpeg|svg|gif|webp|avif)$/,
                    handler: 'CacheFirst',
                    options: {
                        cacheName: 'images',
                        expiration: {
                            maxEntries: 100,
                            maxAgeSeconds: 60 * 60 * 24 * 30, // 30 jours
                        },
                    },
                },
                {
                    urlPattern: /\/api\//,
                    handler: 'NetworkFirst',
                    options: {
                        cacheName: 'api-cache',
                        expiration: {
                            maxEntries: 50,
                            maxAgeSeconds: 60 * 5, // 5 minutes
                        },
                    },
                },
            ],
        })
    );

    // Optimisations supplémentaires
    config.optimization = {
        ...config.optimization,
        splitChunks: {
            chunks: 'all',
            cacheGroups: {
                vendor: {
                    test: /[\\/]node_modules[\\/]/,
                    name: 'vendors',
                    priority: 10,
                    chunks: 'all',
                },
                common: {
                    minChunks: 2,
                    priority: 5,
                    reuseExistingChunk: true,
                },
            },
        },
    };
}

module.exports = config;