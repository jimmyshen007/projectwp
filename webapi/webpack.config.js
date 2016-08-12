var path = require('path');
var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var nib = require('nib');

var config = require('config');

var isDev = (process.env.NODE_ENV === 'development');

var defineEnvPlugin = new webpack.DefinePlugin({
  __DEV__: isDev
});

var entryScripts = [ ];
var output = {
  path: path.join(__dirname, [ '/', config.get('buildDirectory') ].join('')),
  filename: 'bundle.js'
};

var plugins = [
  defineEnvPlugin,
  new ExtractTextPlugin('style.css'),
  new webpack.NoErrorsPlugin()
];

var moduleLoaders = [
  {
    test: /\.js$/,
    loaders: [ 'babel' ],
    exclude: /node_modules/,
    include: __dirname
  }, {
    test: /\.css?$/,
    loaders: [ ExtractTextPlugin.extract('style-loader', 'css-loader'), 'raw' ],
    include: __dirname
  }, {
    test: /\.styl?$/,
    loader: ExtractTextPlugin.extract('style-loader', 'css-loader!stylus-loader'),
    include: __dirname
  }
];

if (isDev) {
  output.publicPath = 'http://localhost:3001/';
  plugins.push(new webpack.HotModuleReplacementPlugin());
  entryScripts = [
    'webpack-dev-server/client?http://localhost:3001',
    'webpack/hot/only-dev-server',
  ];

  moduleLoaders = [
    {
      test: /\.css?$/,
      loaders: [ 'style-loader', 'css-loader' ],
      include: __dirname
    }, {
      test: /\.styl?$/,
      loaders: [ 'style-loader', 'css-loader', 'stylus-loader' ],
      include: __dirname
    }
  ];
}

module.exports = {
  devtool: 'eval',
  entry: entryScripts,
  output: output,
  plugins: plugins,
  module: {
    loaders: moduleLoaders
  },
  stylus: {
    use: [ nib() ]
  }
};
