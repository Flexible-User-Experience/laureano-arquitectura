const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .copyFiles([
        {from: './assets/images', to: 'images/[path][name].[ext]'},
        {from: './assets/vectors', to: 'vectors/[path][name].[ext]'},
    ])
    /*
     * ENTRY CONFIG
     */
    .addEntry('admin', './assets/admin.js')
    .addEntry('web', './assets/web.js')
    .addEntry('project_detail', './assets/project_detail.js')
    .splitEntryChunks()
    .enableStimulusBridge('./assets/controllers.json')
    .enableSingleRuntimeChunk()
    /*
     * FEATURE CONFIG
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })
    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
