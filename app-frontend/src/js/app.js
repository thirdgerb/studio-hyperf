require('./bootstrap');

import { Bubbles } from "../Bubble/Bubbles.js";

const URL = '/web';


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

// `.talk()` will get your bot to begin the conversation
chatWindow.answer(
    '', '#repeat'
);
