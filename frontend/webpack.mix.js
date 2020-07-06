const mix = require('laravel-mix');
const HtmlWebpackPlugin = require('html-webpack-plugin');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.setPublicPath('public');
mix.js('src/js/app.js', 'js/app.js');
mix.styles([
    "./src/Bubble/css/setup.css",
    "./src/Bubble/css/says.css",
    "./src/Bubble/css/reply.css",
    "./src/Bubble/css/typing.css",
    "./src/Bubble/css/input.css",
], 'public/css/app.css');

mix.webpackConfig({
    output: {
        publicPath : '',
    },
    plugins: [
        new HtmlWebpackPlugin({                        //根据模板插入css/js等生成最终HTML
            filename:'index.html',    //生成的html存放路径，相对于 path
            template:'src/html/index.html',    //html模板路径
            title: '测试',
            cache: true,
            inject:true,    //允许插件修改哪些内容，包括head与body
            hash:true,    //为静态资源生成hash值
            minify:{    //压缩HTML文件
                removeComments:true,    //移除HTML中的注释
                collapseWhitespace:false    //删除空白符与换行符
            }
        })
    ],
});
