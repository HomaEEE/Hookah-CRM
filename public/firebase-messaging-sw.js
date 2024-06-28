// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

try
{
    // Initialize the Firebase app in the service worker by passing in
    // your app's Firebase config object.
    // https://firebase.google.com/docs/web/setup#config-object
    firebase.initializeApp({
        apiKey: "AIzaSyCf7BfFTaDsXCazTH7zGTraucmLCxdjyhU",
        authDomain: "noire-crm.firebaseapp.com",
        databaseURL: "",
        projectId: "noire-crm",
        storageBucket: "noire-crm.appspot.com",
        messagingSenderId: "371952932790",
        appId: "1:371952932790:web:47406acf3c6beda2915317",
        measurementId: ""
    });


    // Retrieve an instance of Firebase Messaging so that it can handle background
    // messages.
    const messaging = firebase.messaging();

    messaging.onBackgroundMessage((payload) => {
    //
        

        let options = {
            body: "",
            icon: "",
            image: "",
            tag: "alert",
        };

        if(payload.data.body){
            options.body = payload.data.body;
        }

        if(payload.data.image){
            options.icon = payload.data.image;
        }

        let notification = self.registration.showNotification(
            payload.data.title,
            options
        );

        if(payload.data.url){
            // link to page on clicking the notification
            notification.onclick = (payload) => {
                window.open(payload.data.url);
            };
        }
    });
}
catch(e) {
    console.log(e)
}
