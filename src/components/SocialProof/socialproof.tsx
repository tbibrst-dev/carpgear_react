import React, { useState, useEffect } from 'react';
import useInstantWins from '../../useInstantWins';

interface InstantWinNotification {
  user: string;
  prize: string;
  image: string;
  timestamp: number;
}

const Notifications: React.FC = () => {
  const [currentNotification, setCurrentNotification] = useState<InstantWinNotification | null>(null);
  const [showNotification, setShowNotification] = useState(false);

  // Callback function passed to useInstantWins hook
  const handleNewNotification = (notification: InstantWinNotification) => {
    setCurrentNotification(notification);
    setShowNotification(true); // Show the notification
  };

  useInstantWins(handleNewNotification); // Hook listens for new notifications

  useEffect(() => {
    if (showNotification) {
      // Automatically hide the notification after 10 seconds
      const timer = setTimeout(() => {
        setShowNotification(false);
        setCurrentNotification(null); // Clear the notification
      }, 10000);

      return () => clearTimeout(timer); // Cleanup the timer on unmount or when notification changes
    }
  }, [showNotification]);

  if (!showNotification || !currentNotification) return null; // Hide if no notification to show

  return (
    <div className="notifications-container">
      <div className="mainDivSocialProofe">
        <div className="mainContainer">
          <div className="containerSocialProofe">
            <div className="socialProofImage">
              <img src={currentNotification.image} alt={currentNotification.prize} />
              <div className="socialProof-instant-win-boxx-flag"></div>
            </div>
            <div className="socilaProofDetails">
              <span>
                <span className="sUserName">{currentNotification?.user
                  ? currentNotification.user.split(' ')[0]
                  : 'Mike'} </span>
                just won a <span className="sCompName">{currentNotification.prize}</span> Instant Win!!!
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Notifications;
