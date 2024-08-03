importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
   
firebase.initializeApp({
    apiKey: "AIzaSyBa0_iGr6D7y_xqYiBG9ZemNXkkXcQ72bA",
    projectId: "blb-infra",
    messagingSenderId: "930773697230",
    appId: "1:930773697230:web:1ec6ae45509cefc4c2869f"
});
  
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function({data:{title,body,icon}}) {
    return self.registration.showNotification(title,{body,icon});
});