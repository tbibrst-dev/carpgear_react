import "swiper/css";
import "swiper/css/scrollbar";
import {
  CompetitionType,
  InstantWinDetails,
  OrderDetails,
  QunatityType,
} from "../../types";
import SwiperSlideComponent from "../Home/SwiperSlide";
import { handleAddToCart } from "../../utils";
import { NavigateFunction } from "react-router";
import { useNavigate } from 'react-router-dom';
import { useRef } from "react";
import { useSelector } from "react-redux";
import { RootState } from "../../redux/store";





type PropTypes = {
  order: OrderDetails;
  cartKeys: { [key: number]: { key: string } };
  handleQuantityChange: (
    id: number,
    newQuantity: number,
    action: "increment" | "decrement"
  ) => void;
  navigateCompetition: (
    id: number,
    category: string,
    competitionName: string,
    navigate: NavigateFunction
  ) => void;
  handleQuantityChangeInput: (id: number, value: number) => void;
  quantities: QunatityType;
  competitions: CompetitionType[];
  isFetching: boolean;
  isInstantWinner: boolean;
  instantWinDetails: InstantWinDetails[];
};

const ConfirmationTwo: React.FC<PropTypes> = ({
  order,
  cartKeys,
  handleQuantityChange,
  handleQuantityChangeInput,
  navigateCompetition,
  quantities,
  competitions,
  isFetching,
  isInstantWinner,
  instantWinDetails,
}) => {
  //? function to render upsell content based on if the user is instant winner or not
  const user = useSelector((state: RootState) => state.userReducer.user);
  console.log('user++++', user)
  const swiperRef = useRef<any | null>(null);

  const onSwiperInit = (swiper: any) => {
    swiperRef.current = swiper;
  };

  function renderUpsells() {
    if (isFetching) {
      return (
        <div className="confirmation-loading">
          <h1>Please wait...</h1>
        </div>
      );
    }
    if (!isInstantWinner) {
      return (
        <div className="swiper inst-slider">
          <SwiperSlideComponent
            cartKeys={cartKeys}
            navigateCompetition={navigateCompetition}
            handleQuantitiyChangeInput={handleQuantityChangeInput}
            handleQuantityChange={handleQuantityChange}
            quantities={quantities}
            competitions={competitions}
            handleAddToCart={handleAddToCart}
            category=""
            scrollbarId="swiper-scrollbar-confirmation"
            onSwiperInit={onSwiperInit}
          />
          <div className="swiper-scrollbar" />
        </div>
      );
    }
    return null;
  }

  const navigate = useNavigate();

  console.log('instantWinDetails',instantWinDetails);
  const handleClick = (prizeData: any) => {
    const cashAlt = prizeData?.value > 0 ? 1 : 0;

    navigate(`/claim/prize?competition_name=${prizeData?.competition_name}&competition_type=instant&prize_name=${prizeData?.title}&prize_id=${prizeData?.instant_id}&competition_id=${prizeData?.competition_id}&order=${order?.order_number}&ticket_number=${prizeData?.ticket_number}&user_id=${prizeData?.user_id}&cash_alt=${cashAlt}`);
  };

  const handleClicktoWon =() =>{
    navigate('/account?tab=won');
  }

  function renderInstantWins() {
    const instantWinJSX = instantWinDetails.map((item, index) => (
      <div className={`winner-section-right-all ${index !== 0 && "mt-3"}`} key={index}>
        <div className="bait-reward-center-winner">
          <h2>
            <div className="bait-instant-win-head-title-winner">
              <svg
                width={31}
                height={31}
                viewBox="0 0 31 31"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M5.16699 18.0833L18.0837 3.875V12.9167H25.8337L12.917 27.125V18.0833H5.16699Z"
                  fill="white"
                  stroke="white"
                  strokeWidth={2}
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
              </svg>
              <h2>Winner</h2>
            </div>
          </h2>
        </div>

        <div className="confirm-winner-all">
          <div className="confirm-winner-al-left">
            <img src={item.image} alt="" />
          </div>
          <div className="confirm-winner-al-right">
            <h4>{item.title}</h4>
            <p>Ticket number: {item.ticket_number}</p>
          </div>
        </div>
        <div className="winner-section-claim-price">
          {
            item.type != 'Points'  && item.type != 'Tickets' ?
              <button type="button" className="section-claim-pric" onClick={() => handleClick(item)}>
                Claim your prizes
              </button>

              :
              <button type="button" className="section-claim-pric" onClick={() => handleClicktoWon()}>
                View Winning Tickets
              </button>

          }

        </div>
      </div>
    ));
    return instantWinJSX;
  }

  return (
    <div>

      <div className="comp-banner">
        <div className="comp-banner-txt">
          <h2>Confirmation</h2>
        </div>
      </div>

      <div className="no-winner-section">
        <div className="container">
          <div className="winner-section-all">
            <div className="winner-section-left">
              <div className="main-draw-win-head-title">
                <h2>Main Draw</h2>
              </div>
              <div className="winner-confirmation-left-top">
                <svg
                  width={32}
                  height={33}
                  viewBox="0 0 32 33"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <rect
                    y="0.5"
                    width={32}
                    height={32}
                    rx={16}
                    fill="#2CB4A5"
                  />
                  <path
                    d="M23.6652 11.1202C24.1116 11.5666 24.1116 12.2915 23.6652 12.7379L14.5234 21.8798C14.077 22.3261 13.3521 22.3261 12.9057 21.8798L8.33478 17.3088C7.88841 16.8625 7.88841 16.1375 8.33478 15.6912C8.78116 15.2448 9.50608 15.2448 9.95246 15.6912L13.7163 19.4515L22.0511 11.1202C22.4975 10.6739 23.2224 10.6739 23.6688 11.1202H23.6652Z"
                    fill="black"
                  />
                </svg>
                {/* <img src="images/entry-confirm.png" alt=""> */}
                <div className="winner-confirmation-left-top-head">
                  <h2>Entry Confirmed</h2>
                  <p>
                    Thank you for entering, your ticket purchase has been
                    recieved.
                  </p>
                </div>
              </div>
              <div className="confirmed-order-number">
                <div className="confirmed-order-number-sep">
                  <div className="confirmed-order-number-sep-one">
                    <h4>{order.order_number}</h4>
                    <p>Order Number</p>
                  </div>
                  <div className="confirmed-order-number-sep-one">
                    <h4>
                      {order.order_created && order.order_created.date
                        ? order.order_created.date
                        : "a"}
                    </h4>
                    <p>Order Date</p>
                  </div>
                </div>
                <div className="confirmed-order-number-mail">
                  <h4>{order.email}</h4>
                  <p>Email</p>
                </div>
              </div>
            </div>

            {isInstantWinner ? (
              <div className="winner-section-right">
                {renderInstantWins()}
              </div>
            ) : (
              <div className="winner-section-right">
                <div className="no-winner-section-right-all">
                  <div className="bait-reward-center-winner">
                    <h2>
                      <div className="bait-instant-win-head-title-winner">
                        <svg
                          width={31}
                          height={31}
                          viewBox="0 0 31 31"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M5.16699 18.0833L18.0837 3.875V12.9167H25.8337L12.917 27.125V18.0833H5.16699Z"
                            fill="white"
                            stroke="white"
                            strokeWidth={2}
                            strokeLinecap="round"
                            strokeLinejoin="round"
                          />
                        </svg>
                        <h2>Instant Wins</h2>
                      </div>
                    </h2>
                  </div>

                  <div className="no-winner-all">
                    <div className="no-winner-txt">
                      <h4>Nothing this time</h4>
                      <p>
                        You haven’t got any instant wins, but there are always
                        more opportunities!
                      </p>
                    </div>
                  </div>
                  <div className="winner-section-claim-price">
                    <a href="/" className="section-claim-pric">
                      Try Again
                    </a>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>

      <section className="bait-other-comp" id="upsell-comps">
        <div className="container">
          <div
            className={`instant-slider winner ${!competitions.length && "d-none"
              }`}
          >
            {renderUpsells()}
          </div>
        </div>
      </section>
    </div>
  );
};

export default ConfirmationTwo;
