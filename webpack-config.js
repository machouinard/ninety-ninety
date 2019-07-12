const path = require( 'path' );

const config = {
	entry: {
		front: './src/front/js/front-index.js',
		admin: './src/admin/js/admin-index.js',
	},
	output: {
		filename: 'js/ninety-[name].js',
		path: path.resolve( __dirname, 'assets' ),
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader',
			},
		],
	},
};

module.exports = config;
