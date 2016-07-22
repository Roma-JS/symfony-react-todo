var ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
    entry: './src/js/index.js',
    module: {
        loaders: [{
            test: /\.js$/,
            loader: 'babel',
            query: {
                presets: ['es2015', 'react', 'stage-0']
            }
        },
        {
            test: /\.less$/,
            loader: ExtractTextPlugin.extract('style-loader', 'css!less')
        }, {
            test: /\.(ttf|eot|svg|woff(2)?)(\?[a-z0-9=&.]+)?$/,
            loader: 'file-loader'
        }, {
            test: /\.png$/,
            loader: 'url-loader?mimetype=image/png'
        }]
    },
    plugins: [
        new ExtractTextPlugin('main.css', {
            allChunks: true
        })
    ],
    output: {
        filename: 'bundle.js',
        path: __dirname + '/../web/assets'
    },
};
