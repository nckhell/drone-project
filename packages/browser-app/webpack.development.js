const CleanWebpackPlugin = require('clean-webpack-plugin')
const merge = require('webpack-merge')
const path = require('path')
const webpack = require('webpack')
const Dotenv = require('dotenv-webpack')

const common = require('./webpack.common.js')

module.exports = merge.smart(common, {
  devtool: 'inline-source-map',
  mode: 'development',
  plugins: [
    new CleanWebpackPlugin(),
    new webpack.HotModuleReplacementPlugin(),
    new Dotenv({
      path: '.env.development'
    })
  ],
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'dist'),
    publicPath: '/'
  },
  watch: true,
  devServer: {
    historyApiFallback: true
  }
})
