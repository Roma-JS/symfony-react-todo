var webpack = require('webpack');

module.exports = {
    entry: './src/js/index.js',
    devServer: {
        hot: true,
        contentBase: __dirname + '/src',
    },
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
            loader: 'style-loader!css!less'
        }, {
            test: /\.(ttf|eot|svg|woff(2)?)(\?[a-z0-9=&.]+)?$/,
            loader: 'file-loader'
        }, {
            test: /\.png$/,
            loader: 'url-loader?mimetype=image/png'
        }]
    },
    plugins: [
        new webpack.HotModuleReplacementPlugin()
    ],
    output: {
        filename: 'bundle.js',
        path: __dirname + '/../web/assets'
    },
};
