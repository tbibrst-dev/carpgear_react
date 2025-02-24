import { useEffect } from 'react';
import { ref, onChildAdded, remove, DataSnapshot } from 'firebase/database';
import { database } from '../firebase-config';

interface InstantWinNotification {
  user: string;
  prize: string;
  image: string;
  timestamp: number;
}

const useInstantWins = (callback: (notification: InstantWinNotification) => void) => {
  useEffect(() => {
    if (typeof callback !== 'function') {
      console.error('Callback is not a function:', callback);
      return;
    }

    const instantWinsRef = ref(database, 'instant-wins');

    // Listener to trigger when a new notification is added
    const unsubscribe = onChildAdded(instantWinsRef, async (snapshot: DataSnapshot) => {
      const newWin = snapshot.val();
      if (newWin) {
        // Process the new notification
        callback(newWin);

        // Remove the notification after a delay to ensure it is displayed first
        try {
          await new Promise(resolve => setTimeout(resolve, 100)); // Short delay
          await remove(snapshot.ref);
          console.log('Notification removed:', snapshot.key);
        } catch (error) {
          console.error('Failed to delete notification:', error);
        }
      }
    });

    return () => unsubscribe(); // Cleanup listener on component unmount
  }, [callback]);
};

export default useInstantWins;
