// firebase-config.ts
import { initializeApp } from 'firebase/app';
import { getDatabase, ref, onValue, Database,remove } from 'firebase/database';

const firebaseConfig = {
    apiKey: "AIzaSyDph7KeUMN9hqsFlih3JIgPW3KOPG5ihgE",
    authDomain: "instantwin-notifications.firebaseapp.com",
    projectId: "instantwin-notifications",
    storageBucket: "instantwin-notifications.appspot.com",
    messagingSenderId: "1067659387624",
    appId: "1:1067659387624:web:69950ecf596f8b1fea1f5d",
    measurementId: "G-50LERQDERY"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const database: Database = getDatabase(app);

export { database, ref, onValue,remove };
