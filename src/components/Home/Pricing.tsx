import { useEffect, useState } from "react";
import axios from "axios";

const Pricing = () => {

  interface ApiData {
    data: {
      winner_stat: string;
      prizes_stat: string;
      donated_stat: string;
      followers_stat: string;
    };
  }

  const [apidata, setApidata] = useState<ApiData | null>(null);

  const totalPrizeValue = localStorage.getItem('totalPrizeValue');
  const totalWinner = localStorage.getItem('totalWinner');



  useEffect(() => {
    fetchGlobalSetting();
  }, []);
  const fetchGlobalSetting = async () => {
    try {
      const response = await axios.get("?rest_route=/api/v1/getsettings", {
        headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` },
      });
      if (response.data.success) {
        setApidata(response.data)
        

      }
    } catch (error) {
      console.log(error);

    }
  };

  return (
    <div className="prices-section">
      <div className="desktop-pricing-vector"></div>
      <div className="container position-relative">
        <div className="pricing-all-data ">
          {/* Mobile-price-section-start */}
          <div className="mobs-show">
            <div className="review-ratings-mob">
              <div className="price-rating-lefts-mob">
                <div className="price-ratings-left-mob">
                  <img src="images/stars.svg" />
                </div>
                <div className="price-ratings-lefts-two">
                  <p>
                    <span>4.9</span> Rating
                  </p>
                </div>
                <div className="price-ratings-lefts-two">
                  <img src="images/reviewsio-logo.svg" />
                </div>
              </div>
            </div>
          </div>

          {/* Mobile-price-section-end */}
          <div className="price-section-all">
            <div className="price-part">
              <img src="images/troffee.svg" alt="" />
              <div className="price-txt">
                {/* <h4>{ totalWinner}</h4> */}
                <h4>{ ((Number(apidata?.data?.winner_stat) || 0) + Number(totalWinner)).toLocaleString()  }</h4>

                <p>Winners</p>
              </div>
            </div>
            <div className="price-part">
              <img src="images/gift.svg" alt="" />
              <div className="price-txt">
              <h4>£{((Number(apidata?.data?.prizes_stat) || 0) + Number(totalPrizeValue)).toLocaleString()}</h4>
                <p>Prizes</p>
              </div>
            </div>
            <div className="price-part">
              <img src="images/heart.svg" alt="" />
              <div className="price-txt">
              <h4>£{Number(apidata?.data?.donated_stat)?.toLocaleString() ?? ""}</h4>
                <p>Donated</p>
              </div>
            </div>
            <div className="price-part">
              <img src="images/ok.svg" alt="" />
              <div className="price-txt">
                {/* <h4>{ apidata?.data?.followers_stat ?? ""}</h4> */}
                <h4>{Number(apidata?.data?.followers_stat)?.toLocaleString() ?? ""}</h4>

                <p>Followers</p>
              </div>
            </div>
          </div>
          {/* mobile-section-start */}
          <div className="mob-get-exclusive">
            <div className="mob-get-exclusive-all">
              <div className="mob-get-exclusive-txt">
                <h2>Get exclusive offers</h2>
                <p>
                  Get Exclusive Competitions &amp; Offers Available Only For Our
                  App Users.
                </p>
                <div className="mob-get-exc-icon">
                  <a
                    href="https://apps.apple.com/us/app/carp-gear-giveaways/id1513020494"
                    target="_blank"
                  >
                    {" "}
                    <img src="images/get-exc-2.svg" alt="" />
                  </a>
                  <a
                    href="https://play.google.com/store/apps/details?id=co.uk.carpgeargiveaways.app"
                    target="_blank"
                  >
                    {" "}
                    <img src="images/get-exc-1.svg" alt="" />{" "}
                  </a>
                </div>
              </div>
            </div>
          </div>
          {/* mobile-section-end */}
          <div className="single-compi-top-rating">
            <div className="container">
              <div className="single-compi-top-rating-all">
                <div className="single-compi-top-rating-left">
                  <div className="single-compi-top-rating-left-one">
                    <img src="images/stars.svg" alt="" />
                  </div>
                  <div className="single-compi-top-rating-left-two">
                    <p>
                      <span> 4.9</span> Rating
                    </p>
                  </div>
                  <div className="single-compi-top-rating-left-three">
                    <a href="">
                      {" "}
                      <img src="images/reviewsio-logo.svg" alt="" />{" "}
                    </a>
                  </div>
                </div>
                <div className="single-compi-top-rating-right">
                  <div className="single-compi-top-rating-right-one">
                    <a
                      href="https://apps.apple.com/us/app/carp-gear-giveaways/id1513020494"
                      target="_blank"
                    >
                      {" "}
                      <img src="images/single-comp-top.svg" alt="" />
                    </a>
                  </div>
                  <div className="single-compi-top-rating-right-one">
                    <a
                      href="https://play.google.com/store/apps/details?id=co.uk.carpgeargiveaways.app"
                      target="_blank"
                    >
                      {" "}
                      <img src="images/single-comp-top-1.svg" alt="" />{" "}
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Pricing;
