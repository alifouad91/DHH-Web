const webpack = require("webpack");
const path = require("path");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const CleanWebpackPlugin = require("clean-webpack-plugin");
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const cssnano = require("cssnano");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");

module.exports = function(env, argv) {
  /*
   * boolean variable for development mode
   */
  const devMode = argv.mode === "development";

  const mods = {
    watch: devMode,
    devtool: devMode ? "source-map" : "cheap-module-source-map",
    entry: {
      app: ["./src/js/app.js", "./src/scss/main.scss"],
    },
    output: {
      path: path.join(__dirname, "dist"),
      filename: "js/[name].min.js",
    },
    module: {
      rules: [
        /*
         * ESLint
         */
        // {
        //   enforce: 'pre',
        //   test: /\.js$/,
        //   exclude: /(node_modules|dist|react)/,
        //   loader: 'eslint-loader',
        // },
        /*
         * Handle ES6 transpilation
         */
        {
          test: /jquery-mousewheel/,
          loader: "imports-loader?define=>false&this=>window",
        },
        {
          test: /malihu-custom-scrollbar-plugin/,
          loader: "imports-loader?define=>false&this=>window",
        },
        {
          test: /\.js$/,
          exclude: /(node_modules|bower_components)/,
          use: {
            loader: "babel-loader",
            options: {
              presets: ["@babel/preset-env", "@babel/preset-react"],
              plugins: [
                [
                  "import",
                  { libraryName: "antd", libraryDirectory: "es", style: "css" },
                ],
              ],
            },
          },
        },
        // {
        //   test: /\.handlebars$/,
        //   loader: `handlebars-loader?helperDirs[]=${__dirname}/src/js/helpers`,
        // },
        /*
         * Handle SCSS transpilation
         */
        {
          test: /\.(sa|sc|c)ss$/,
          use: [
            {
              loader: MiniCssExtractPlugin.loader,
            },
            {
              loader: "css-loader",
              options: {
                sourceMap: devMode,
                minimize: !devMode,
              },
            },
            {
              loader: "sass-loader",
              options: {
                sourceMap: devMode,
              },
            },
          ],
        },
        /*
         * Handle Fonts
         */
        {
          test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
          use: [
            {
              loader: "file-loader",
              options: {
                include: path.join(__dirname, "src/fonts"),
                name: "[name].[ext]",
                outputPath: "fonts",
                publicPath: "../fonts",
                exclude: "src/images",
              },
            },
          ],
        },
        /*
         * Handle Images Referenced in CSS
         */
        {
          test: /\.(gif|png|jpe?g|svg)$/i,
          use: [
            {
              loader: "file-loader",
              options: {
                include: path.join(__dirname, "src/images"),
                name: "[name].[ext]",
                outputPath: "images",
                publicPath: "../images",
                exclude: "src/fonts",
              },
            },
            "image-webpack-loader",
          ],
        },
        // {
        //   test: /\.css$/,
        //   use: ['style-loader', 'css-loader'],
        // },
      ],
    },
    resolve: {
      extensions: [".js", ".jsx", ".less", ".scss"],
      alias: {
        "day-picker-css": path.join(
          __dirname,
          "/node_modules/react-day-picker/lib/style.css"
        ),
      },
    },
    /*
     * NOTE: Optimization will only run on production mode
     */
    optimization: {
      /*
       * Split imported npm packages into a single file
       */
      splitChunks: {
        cacheGroups: {
          commons: {
            name: "vendors",
            test: /[\\/]node_modules[\\/]/,
            chunks: "async",
          },
        },
      },
      minimizer: [
        /*
         * Minimize javascript
         */
        new UglifyJsPlugin({
          uglifyOptions: {
            compress: {
              drop_console: !devMode,
            },
            output: {
              comments: false,
            },
          },
        }),
      ],
    },
    plugins: [
      /*
       * Automatically load jquery instead of having to import it everywhere
       */
      new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery",
        "window.jQuery": "jquery",
      }),
      /*
       * Extract app CSS and npm package CSS into two separate files
       */
      new MiniCssExtractPlugin({
        filename: "css/[name].min.css",
        chunkFilename: "css/[id].min.css",
      }),
      /*
       * copy all images to the dist folder
       */
      new CleanWebpackPlugin(["dist/images"], {
        watch: true,
      }),
      new CopyWebpackPlugin(
        [
          {
            from: "src/images",
            to: "images",
          },
        ],
        {
          copyUnmodified: true,
        }
      ),
    ],
  };
  /*
   * Minimize CSS if not devMode
   */
  if (!devMode) {
    mods.plugins.push(
      new OptimizeCSSAssetsPlugin({
        cssProcessor: cssnano,
        cssProcessorOptions: {
          discardComments: {
            removeAll: true,
          },
        },
      })
    );
  }
  return mods;
};
