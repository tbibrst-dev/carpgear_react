import { ANNOUCMENT } from "../../utils";
import Marquee from "react-fast-marquee";

const Header = () => {
  const announcement = localStorage.getItem(ANNOUCMENT) as string;
  const items = Array.from({ length: 9 });
  return (
    <div>
      <section className="carp-top-bar">
        <div className="autoplay-section-all animation-top-line ">
          <div className="store-notice">
            <div className="ticker-wrap">
              <Marquee>
                {items.map((_, index) => (
                  <div className="ticker__item" key={index}>
                    <span dangerouslySetInnerHTML={{ __html: announcement }} />
                  </div>
                ))}
              </Marquee>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Header;
