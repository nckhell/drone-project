const CleanWebpackPlugin = require('clean-webpack-plugin')
const merge = require('webpack-merge')
const path = require('path')
const Dotenv = require('dotenv-webpack')

const common = require('./webpack.common.js')

module.exports = merge.smart(common, {
  devtool: 'source-map',
  mode: 'production',
  plugins: [new CleanWebpackPlugin(), new Dotenv()],
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'dist'),
    publicPath: '/'
  }
})
