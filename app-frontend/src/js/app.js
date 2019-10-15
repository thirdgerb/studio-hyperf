require('./bootstrap');

import { Bubbles } from "../Bubble/Bubbles.js";

const URL = '/web';


import Vue from 'vue'
import vuetify from './Plugins/vuetify'

const app = new Vue({
    vuetify,
    template: `<v-app>
   
    <v-app-bar
      app
      color="blue"
      dark
      clipped-left
      abusolute
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-toolbar-title>CommuneChatbot</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon href="https://github.com/thirdgerb/studio-hyperf" ><v-icon>mdi-github-circle</v-icon></v-btn>
      <v-btn icon @click="showCode = !showCode"><v-icon>mdi-code-tags</v-icon></v-btn>
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
          <v-list-item-avatar>
            <v-img src="/avatar.jpg"></v-img>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title>烈风</v-list-item-title>
            <v-list-item-subtitle>微信公众号 CommuneChatbot</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <v-divider></v-divider>
      <v-subheader>测试机器人</v-subheader>
      <v-list
            dense
            rounded
          >
          <v-list-item two-line>
            <v-list-item-icon>
              <v-icon large>mdi-account</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>大战长坂坡</v-list-item-title>
              <v-list-item-subtitle>对话形式的情景小游戏.</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
      </v-list>
    </v-navigation-drawer>
    
    
    <v-content v-show="!showCode">
      <div id="chat" class="grey lighten-3"></div>
    </v-content>

    <v-content v-show="showCode">
      <h1>Code</h1>
    </v-content>
    

</v-app>`,
    data: () => ({
        drawer: null,
        overlay : false,
        showCode : false,
    }),
    created () {

        //this.$vuetify.theme.dark = true

    },
    mounted() {
        const chatWindow = new Bubbles(
            document.getElementById("chat"), // ...passing HTML container element...
            "window.chatWindow", // ...and name of the function as a parameter
            {
                animationTime: 200,
                typeSpeed : 5,
                recallInteractions : 2,
                placeholder : "请输入...",
                inputCallbackFn: function(o){
                    let text = o.content ? o.content : '';
                    if (window._.isString(text) && text.length < 1) {
                        return;
                    }
                    window.axios.post(URL + location.search, {text}, {
                        timeout : 500,
                        maxRedirects : 2
                    }).then(response => {
                        if (200 === response.status) {
                            window.chatWindow.reply(response.data);
                        } else {
                            alert(response);
                        }
                    }).catch(error => {
                        console.log(error);
                        alert(error);
                    });
                }
            }
        );

        window.chatWindow = chatWindow;

        chatWindow.answer(
            '', '#repeat'
        );
    }
}).$mount('#app')