require('./bootstrap');

import { Bubbles } from "../Bubble/Bubbles.js";

let convo = // pass your JSON/JavaScript object to `.talk()` function where
    // you define how the conversation between the bot and user will go
    {
        // "says" defines an array of sequential bubbles
        // that the bot will produce
        "says": [ "#repeat"],

    };

const chatWindow = new Bubbles(
    document.getElementById("chat"), // ...passing HTML container element...
    "window.chatWindow", // ...and name of the function as a parameter
    {
        animationTime: 100,
        recallInteractions : 20,
        inputCallbackFn: function(o){
            console.log(o);
            chatWindow.reply(convo);
        }
    }
);

window.chatWindow = chatWindow;

// `.talk()` will get your bot to begin the conversation
chatWindow.answer(
    '', '#repeat'
);
