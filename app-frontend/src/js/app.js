require('./bootstrap');

import { Bubbles } from "../Bubble/Bubbles.js";
import Vue from 'vue'
import vuetify from './Plugins/vuetify'
import axios from 'axios'
import VueAxios from 'vue-axios'
import hljs from 'highlight.js/lib/highlight';
import php from 'highlight.js/lib/languages/php';
import 'highlight.js/styles/xcode.css';


const WEB_URI = '/web';
const API_URI = '/api';

hljs.registerLanguage('php', php);

Vue.use(VueAxios, axios);

const app = new Vue({
    vuetify,
    template: `<v-app>
   

    <v-app-bar
      app
      color="indigo"
      dark
      clipped-left
      abusolute
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-toolbar-title>CommuneChatbot</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon @click="toggleCode"><v-icon>mdi-code-tags</v-icon></v-btn>
    </v-app-bar>

    <v-navigation-drawer
      v-model="drawer"
      app
      clipped
      fixed
      floating
    >
      <v-list>
        <v-list-item>
          <v-list-item-icon>
            <v-icon large>mdi-robot</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>空妙对话机器人</v-list-item-title>
            <v-list-item-subtitle>多轮对话机器人开发框架</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
      <v-divider></v-divider>
      <v-list
        dense
        nav
      >
        <v-list-item 
          onClick="alert('正在撰写开发文档, 预计10月底完成');"
        >
          <v-list-item-icon>
            <v-icon >mdi-book-open-outline</v-icon>
          </v-list-item-icon>
          <v-list-item-content >
            <v-list-item-title>开发文档</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item 
          v-for="(item, i) in nav"
          :key="i"
          :href="item.url"
          target="_blank"
        >
          <v-list-item-icon>
            <v-icon v-text="item.icon"></v-icon>
          </v-list-item-icon>
          <v-list-item-content >
            <v-list-item-title v-text="item.title"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <v-divider></v-divider>
      <v-subheader>对话机器人 Demo</v-subheader>
      <v-list
        dense
        nav
      >
        <v-list-item-group>
          <v-list-item
            @click="changeScene('')"
          >
            <v-list-item-icon>
              <v-icon>mdi-home-circle</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Demo 入口</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item
            v-for="(item, i) in menu"
            :key="i"
            @click="changeScene(item.scene)"
          >
            <v-list-item-icon>
              <v-icon v-text="item.icon"></v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title v-text="item.title"></v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-item-group>
        
      </v-list>

      <div style="height:36px;"></div>
      <v-footer
        fixed
        padless
      >
        <v-btn
          href="http://www.beian.miit.gov.cn/"
          target="_blank"
          link
          width="100%"
          >
          京ICP备19041094号
        </v-btn>
      </v-footer>
    </v-navigation-drawer>

    <v-overlay :value="loading" opacity="0.1">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
   
    <v-content v-show="!showCode">
      <div id="chat" class="grey lighten-3"></div>
    </v-content>

    <v-content v-show="showCode">
      <v-container>
        <v-card>
          <v-card-title>当前上下文源码</v-card-title>
          <v-card-text>
            <ul>
              <li>名称 : {{ context.name }}</li>
              <li>类名 : {{ context.class }}</li>
              <li>简介 : {{ context.desc }}</li>
              <li>说明 : 这是对话+web两种互动形式同时作用的示范. 当对话语境变更时, 本页的源代码也会切换. </li>
            </ul>
          </v-card-text>
          <v-divider></v-divider>
          <pre><code class="php hljs" v-html="codeHtml"></code></pre>
        </v-card>
      </v-container>
    </v-content>
</v-app>`,
    data: () => ({
        drawer: null,
        // 遮罩
        overlay : false,
        showCode : false,
        dialog: {
            contextName: '',
        },
        loading : false,
        scene: '',
        context : {
            name : '',
            class : '',
            desc : '',
            code : '',
        },
        codeHtml : '',
        nav : [
          // {
          //   title : '开发文档',
          //   url : 'https://github.com/thirdgerb/studio-hyperf',
          //   icon : 'mdi-book-open-outline',
          // },
          {
            title : 'github仓库',
            url : 'https://github.com/thirdgerb/studio-hyperf',
            icon : 'mdi-github-circle',
          },
        ],
        menu: [
          {
            scene: 'introduce',
            title: '项目介绍',
            icon: 'mdi-account-circle',
          },
          {
            scene: 'special',
            title: '框架特点',
            icon: 'mdi-account-circle',
          },
          {
              scene: 'unheard',
              title: '对话侦探游戏DEMO',
              icon: 'mdi-account-circle',
          },
          {
              scene: 'story',
              title: '情景游戏DEMO',
              icon: 'mdi-account-circle',
          },
          {
              scene: 'game',
              title: '对话小游戏',
              icon: 'mdi-account-circle',
          },
          {
            scene: 'nlu',
            title: '自然语言用例',
            icon: 'mdi-account-circle',
          },
          {
              scene: 'dev',
              title: '对话式开发工具',
              icon: 'mdi-account-circle',
          },
          
        ]
    }),
    mounted() {
        let $this = this;

        Vue.axios.interceptors.request.use(function (config) {
          $this.loading = true;
          return config;
        }, function (error) {
          $this.loading = false;
          return Promise.reject(error);
        });

        Vue.axios.interceptors.response.use(function (response) {
            $this.loading = false;
            return response;
        }, function (error) {
            $this.loading = false;
            return Promise.reject(error);
        });


        //this.$vuetify.theme.dark = true
        const chatWindow = new Bubbles(
            document.getElementById("chat"), // ...passing HTML container element...
            "window.chatWindow", // ...and name of the function as a parameter
            {
                animationTime: 200,
                typeSpesed : 5,
                recallInteractions : 2,
                placeholder : "请输入...",
                inputCallbackFn: function(o){
                    let text = o.content ? o.content.trim() : '';
                    if (window._.isString(text) && text.length < 1) {
                        alert("输入不能为空");
                        return;
                    } else if (text.length > 100) {
                        alert("请控制在一百个字符以内");
                        return;
                    }

                    $this.chat(text);
                }
            }
        );

        window.chatWindow = chatWindow;

        let search = location.search
        if (search.length > 0) {
            search = search.substr(1);
            let searchArr = search.split('&');
            for (var i = 0; i < searchArr.length; i++) {
                let parts = searchArr[i].split('=');
                if (parts[0] == 'scene') {
                    $this.scene = parts[1];
                    $this.chat('#home');
                    return;
                }
            }
        }

        $this.chat('#repeat');
        
    },
    methods : {

        chat : function(text) {
            let $this = this;
            let scene = $this.scene;
            console.log(scene);

            Vue.axios.post(WEB_URI, {text}, {
                timeout : 1500,
                params : {
                    scene : scene,
                },
                maxRedirects : 2
            }).then(response => {
                if (200 === response.status) {
                  if (response.data.code != 0) {
                    alert(response.data.msg);
                    return;
                  }

                  let data = {};
                  if (response.data.hasOwnProperty('data')) {
                    data = response.data.data;
                  }

                  if (data.hasOwnProperty('replies')) {
                      window.chatWindow.reply(data.replies);
                  }
                  if (data.hasOwnProperty('dialog')) {
                      $this.dialog = data.dialog;
                  }

                } else {
                    alert(response);
                }
            }).catch(error => {
                alert(error);
            });

        },
        toggleCode() {
            let $this = this;

            if ($this.showCode) {
              $this.showCode = false;
              return;
            }

            Vue.axios.get(API_URI, {
                timeout : 500,
                params : {
                    action : "context-code",
                    contextName : $this.dialog.contextName
                },
                maxRedirects : 2
            }).then(response => {
                if (200 === response.status) {
                  if (response.data.code == 0) {
                    Vue.set($this, 'context', response.data.data);
                    let html = hljs.highlight('php', this.context.code);
                    $this.codeHtml = html.value;
                    $this.showCode = ! $this.showCode;
                  } else {
                    alert(response.data.msg);
                  }

                } else {
                    alert(response);
                }
            }).catch(error => {
                alert(error);
            });
        },
        changeScene(scene) {
          let $this = this;          
          $this.showCode = false;
          $this.scene = scene;
          $this.chat('#home');

        },
        link(url) {
          window.location.href = url;
        }

    }
}).$mount('#app')