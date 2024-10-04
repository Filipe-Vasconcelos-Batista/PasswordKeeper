const path = require('path');
const {WebpackManifestPlugin} = require("@symfony/webpack-encore/lib/webpack-manifest-plugin");

module.exports = {
    entry: './assets/app.js', // Entry point of your application
    output: {
        filename: 'bundle.js', // Output bundle file name
        path: path.resolve(__dirname, 'dist'), // Output directory
    },
    mode: 'development', // Set the mode to 'development' or 'production'
    plugins: [
        new WebpackManifestPlugin({
            filename: 'manifest.json',
            publicPath: '/build',
        }),
    ],
    module: {
        rules: [
            {
                test: /\.js$/, // Apply this rule to .js files
                exclude: /node_modules/, // Exclude node_modules directory
                use: {
                    loader: 'babel-loader', // Use Babel loader for transpiling JavaScript
                },
            },
            {
                test: /\.css$/, // Apply this rule to .css files
                use: ['style-loader', 'css-loader'], // Use style-loader and css-loader
            },
        ],
    },
    devServer: {
        contentBase: path.join(__dirname, 'dist'), // Serve content from the 'dist' directory
        compress: true, // Enable gzip compression
        port: 9000, // Port number for the dev server
    },
};
