import HowItWorks from "./How-it-works";
import RecentWinners from "./Recent-winners";
import Reviews from "./Reviews";

const Wrapper = () => {
  return (
    <div className="how-it-work-back">
      <div className="how-it-work">
        <HowItWorks />
        <RecentWinners />
        <Reviews />
      </div>
    </div>
  );
};

export default Wrapper;
