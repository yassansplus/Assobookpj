var Encore = require("@symfony/webpack-encore");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath("public/build/")
    // public path used by the web server to access the output path
    .setPublicPath("/build")
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry("app", "./assets/app.js")

    .addStyleEntry("global", "./assets/styles/global.scss")
    .addStyleEntry('globalConnect', './assets/styles/globalConnect.scss')
    .addStyleEntry('profile', './assets/styles/profile.scss')
    .addStyleEntry("formAuth", "./assets/styles/formAuth.scss")
    .addStyleEntry("index", "./assets/styles/index.scss")
    .addStyleEntry("amsify.suggestags", "./assets/styles/amsify.suggestags.scss")
    .addStyleEntry("wawFront", "./assets/styles/wawFront.scss")
    .addStyleEntry("hiwFront", "./assets/styles/hiwFront.scss")
    .addStyleEntry("publication", "./assets/styles/publication.scss")
    .addStyleEntry("banner", "./assets/styles/component/banner.scss")
    .addStyleEntry("sidebar", "./assets/styles/component/sidebar.scss")
    .addStyleEntry("follower", "./assets/styles/component/follower.scss")
    .addStyleEntry("pagination", "./assets/styles/component/pagination.scss")
    .addStyleEntry("stripecss", "./assets/styles/stripe.scss")
    .addStyleEntry("carte", "./assets/styles/carte.scss")
    .addStyleEntry("homePublication", "./assets/styles/homePublication.scss")
    .addStyleEntry("chat", "./assets/styles/chat.scss")
  //Add javascript

    .addEntry("script", "./assets/javascript/script.js")
    .addEntry("counter", "./assets/javascript/counter.js")
    .addEntry("bannerjs", "./assets/javascript/banner.js")
    .addEntry("scrlrvl", "./assets/javascript/scrollreveal.js")
    .addEntry("pwdreset", "./assets/javascript/pwdreset.js")
    .addEntry("suggestions", "./assets/javascript/suggestions.js")
    .addEntry('autocomplete', './assets/javascript/autocomplete.js')
    .addEntry('profilejs', './assets/javascript/profile.js')
    .addEntry('readmore', './assets/javascript/readmore.js')
    .addEntry('cart', './assets/javascript/cart.js')
    .addEntry('stripejs', './assets/javascript/stripe.js')
    .addEntry('home', './assets/javascript/home.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge("./assets/controllers.json")
    .addEntry('chatScript', './assets/javascript/chat.js')
    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge("./assets/controllers.json")

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push("@babel/plugin-proposal-class-properties");
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = "usage";
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader();

// uncomment if you use TypeScript
//.enableTypeScriptLoader()

// uncomment if you use React
//.enableReactPreset()

// uncomment to get integrity="..." attributes on your script & link tags
// requires WebpackEncoreBundle 1.4 or higher
//.enableIntegrityHashes(Encore.isProduction())

// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()

module.exports = Encore.getWebpackConfig();
