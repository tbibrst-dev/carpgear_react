import { useEffect, useState, memo } from "react";

interface CountdownTimerProps {
  drawDate: string;
  drawTime: string;
}

const CountdownTimer: React.FC<CountdownTimerProps> = memo(
  ({ drawDate, drawTime }) => {
    const calculateRemainingTime = () => {
      if (!drawDate) {
        return { days: 0, hours: 0, minutes: 0, seconds: 0 };
      }
      const targetDate = new Date(`${drawDate}T${drawTime}`);
      const currentTime = new Date();
      const difference = targetDate.getTime() - currentTime.getTime();

      if (targetDate < currentTime) {
        return { days: 0, hours: 0, minutes: 0, seconds: 0 };
      }

      const days = Math.floor(difference / (1000 * 60 * 60 * 24));
      const hours = Math.floor(
        (difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
      );
      const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((difference % (1000 * 60)) / 1000);

      return { days, hours, minutes, seconds };
    };

    const [timeRemaining, setTimeRemaining] = useState(
      calculateRemainingTime()
    );

    useEffect(() => {
      const timer = setInterval(() => {
        setTimeRemaining(calculateRemainingTime());
      }, 1000);

      return () => clearInterval(timer);
    }, [drawDate, drawTime]);

    return (
      <>
        <div className="draw-btn-one">
          <h4>{timeRemaining.days}</h4>
          <p>DAYS</p>
        </div>
        <div className="draw-btn-one">
          <h4>{timeRemaining.hours}</h4>
          <p>HRS</p>
        </div>
        <div className="draw-btn-one">
          <h4>{timeRemaining.minutes}</h4>
          <p>MINS</p>
        </div>
        <div className="draw-btn-two">
          <h4>{timeRemaining.seconds}</h4>
          <p>SECS</p>
        </div>
      </>
    );
  }
);

export default CountdownTimer;
