const fs = require('fs');
const path = require('path')
const HtmlWebpackPlugin = require('html-webpack-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
  devServer: {
    headers: {
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Max-Age': '1000',
      'Access-Control-Allow-Headers':'X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding',
      'Access-Control-Allow-Methods': 'POST, GET, OPTIONS, PUT',
    },
    //https: {
    //  key: fs.readFileSync('.certs/key.pem'),
    //  cert: fs.readFileSync('.certs/cert.pem'),
    //},
    host: '0.0.0.0',
	  port:8000,
    publicPath: '/',
  },
  publicPath: '/',
  chainWebpack: (config) => {
    if (process.env.NODE_ENV === 'production') {
      config.plugin('html').tap((opts) => {
        opts[0].filename = './index.php';
        return opts;
      });
    }
  }
}
