import { initializeApp } from 'firebase/app';
import { getMessaging, onBackgroundMessage } from 'firebase/messaging/sw';

const firebaseConfig = {
    apiKey: "AIzaSyDph7KeUMN9hqsFlih3JIgPW3KOPG5ihgE",
    authDomain: "instantwin-notifications.firebaseapp.com",
    projectId: "instantwin-notifications",
    storageBucket: "instantwin-notifications.appspot.com",
    messagingSenderId: "1067659387624",
    appId: "1:1067659387624:web:69950ecf596f8b1fea1f5d",
    measurementId: "G-50LERQDERY"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

onBackgroundMessage(messaging, (payload) => {
  console.log('Received background message:', payload);
  const notificationTitle = payload.notification?.title || 'New Notification';
  const notificationOptions = {
    body: payload.notification?.body || '',
    icon: payload.notification?.icon || '/firebase-logo.png',
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
