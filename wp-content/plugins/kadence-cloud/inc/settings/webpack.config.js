const defaultConfig = require("@wordpress/scripts/config/webpack.config");
module.exports = {
	...defaultConfig,
	entry: {
		'settings': './src/index.js', // 'name' : 'path/file.ext'.
	},
	output: {
		filename: '[name].js',
		path: __dirname + '/build'
	},
	devtool: 'none',
};