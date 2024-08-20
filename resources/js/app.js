import './bootstrap';
import { createApp } from 'vue'
import app from './components/app.vue'
import router from './router/index.js'
import vuetify from '../plugins/vuetify.js'
import axios from 'axios'
import { registerLicense } from '@syncfusion/ej2-base';

// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
    apiKey: "AIzaSyDd9kcEdYsgNDrJ94aZ1RgGa9MF9v3RFnY",
    authDomain: "labfti-15835.firebaseapp.com",
    projectId: "labfti-15835",
    storageBucket: "labfti-15835.appspot.com",
    messagingSenderId: "557658901015",
    appId: "1:557658901015:web:bcf55577ebcaaab39fea52",
    measurementId: "G-1WH6GT0R7E"
};

// Initialize Firebase
const appp = initializeApp(firebaseConfig);
const messaging = getMessaging(appp);

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then(registration => {
                console.log('Service worker registered:', registration);
            })
            .catch(error => {
                console.error('Service worker registration failed:', error);
            });
    });
}

onMessage(messaging, (payload) => {
    console.log('Message received. ', payload);
    //alert('ada notifikasi baru');
});

getToken(messaging, {
    vapidKey: "BPJXPb6SVxThYS4bBf7vx9Ies5W6uxiQU94kvxUIJX2LEtxZETl8VRHlmwVXKxYDppYmhotNbLOCju32dYuJt6Q",
}).then((currentToken) => {
    if (currentToken) {
        console.log(currentToken);
        sentTokenToServer(currentToken);
        requestPermission();
    } else {
        requestPermission();
        console.log('No registration token available. Request permission to generate one.');
    }
}).catch((err) => {
    console.log('An error occurred while retrieving token. ', err);
});

function sentTokenToServer(token) {
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let formData = new FormData();
    formData.append('web_token', token);
    formData.append('UserID', localStorage.getItem('UserID'));
    fetch("/tokenweb", {
        method: "POST",
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrf,
            _method: 'POST'
        },
        credentials: 'same-origin'
    })
        .then((response) => {
            console.log(response.status);
        });
}

function requestPermission() {
    Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
            console.log("Notification permission granted.");
        } else {
            alert("Unable to get permission to notify.");
        }
    });
}

registerLicense("Ngo9BigBOggjHTQxAR8/V1NBaF5cXmZCe0x3QHxbf1x0ZFZMYFtbQH5PMyBoS35RckVnWHpec3RSQmlZVkd0")
axios.defaults.withXSRFToken = true
createApp(app).use(router).use(vuetify).mount("#app") 