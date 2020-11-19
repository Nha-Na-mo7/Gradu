
// https://ics.media/entry/16028/#webpack-babel-vue
// https://ocws.jp/blog/post1825/

const path = require('path');

const { VueLoaderPlugin } = require("vue-loader");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const LiveReloadPlugin = require("webpack-livereload-plugin"); //browseSyncのようにオートリロードする

// [定数] webpack の出力オプションを指定します
// 'production' か 'development' を指定
// ★ 本番前に必ずproductionに変更すること！！！
const MODE = "development";

// ソースマップの利用有無(productionのときはソースマップを利用しない)
const enabledSourceMap = MODE === "development";

module.exports = {
  // モード値を production に設定すると最適化された状態で、
  // development に設定するとソースマップ有効でJSファイルが出力される
  mode: MODE,
  // エントリポイントのapp.jsに@babel/polifillを取り込ませることで、promiseが使えるようになる。
  entry: ["@babel/polyfill", path.resolve(__dirname, 'resources/js/app.js')],
  output: {
    path: path.resolve(__dirname, '/public/js'),
    filename: 'app.js'
  },
  module :{
    rules: [
      {
        // 拡張子が.css系の場合
        test: /\.(css|scss)$/,
        use:[
          // use配列の先頭に書かれたloaderが、最後に適用されるloaderとなる
  
          // CSSファイルをjsとしてバンドルせず、外部に書き出す
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              // css-loaderによるurl()メソッドの取り込みを禁止する
              url: false,
              sourceMap: enabledSourceMap,
              importLoaders: 2
            }
          },
          {
            loader: "sass-loader",
            options: {
              sourceMap: enabledSourceMap
            }
          },
          // PostCSSの設定(ベンダプレフィックスを付与する用)
          {
            loader: "postcss-loader",
            options: {
              // PostCSS側でもソースマップを有効にする
              sourceMap: true,
              postcssOptions: {
                plugins: [
                  // Autoprefixerを有効化
                  // ベンダープレフィックスを自動付与する
                  ["autoprefixer", { grid: true }],
                ],
              },
            },
          },
        ],
      },
      {
        // 拡張子が.vueの場合
        test: /\.vue$/,
        loader: 'vue-loader',
      },
      {
        // 拡張子が.jsの場合
        test: /\.js$/,
        use:[{
          // babelを利用する
          loader: 'babel-loader',
          // babelのオプションを指定する
          options: {
            presets: [
              // ES5に変換する
              '@babel/preset-env',
            ],
          },
        }],
      },
      {
        // 画像もJSファイルにバンドルする
        // ただしアイコンに使用している拡張子.svgは指定しない
        test: /\.(gif|png|jpg|eot|wof|woff|woff2|ttf)$/,
        // Base64として取り込む
        loader: "url-loader",
      },
    ]
  },
  // import 文で .ts ファイルを解決する
  resolve: {
    extensions: ["*", ".js", ".vue", ".json"],
    alias: {
      // template機能を使えるようにする。
      vue$: 'vue/dist/vue.esm.js',
    }
  },
  plugins: [
      // vueを読み込めるようにする
      new VueLoaderPlugin(),
      // CSSファイルを外出しにするプラグイン
      new MiniCssExtractPlugin({
        // jsファイルの出力先を基準にし、cssディレクトリに出す
        filename: "../css/style.css",
      }),
      // BrowseSyncのように、webpackで監視しているファイルが変更された時オートリロードしてくれる
      new LiveReloadPlugin(),
  ],
  //ES5(IE11など)向けの設定
  target: ["web", "es5"],
};

// 以上設定したら、npm run build