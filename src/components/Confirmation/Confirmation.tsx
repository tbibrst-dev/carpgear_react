import { OrderDetails } from "../../types";

type PropTypes = {
  order: OrderDetails;
};

const Confirmation: React.FC<PropTypes> = ({ order }) => {
  return (
    <div>
      <div className="comp-banner">
        <div className="comp-banner-txt">
          <h2>Confirmation</h2>
        </div>
      </div>

      <div className="winner-section">
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
                  <rect y="0.5" width={32} height={32} rx={16} fill="#2CB4A5" />
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
                    <h4>{order.email}</h4>
                    <p>Order Date</p>
                  </div>
                </div>
                <div className="confirmed-order-number-mail">
                  <h4>{order.email}</h4>
                  <p>Email</p>
                </div>
              </div>
            </div>
            <div className="winner-section-right">
              <div className="winner-section-right-all">
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
                    <img src="images/bait-reward-win-1.png" alt="" />
                  </div>
                  <div className="confirm-winner-al-right">
                    <h4>Ridgemonkey Hunter 750</h4>
                    <p>Ticket number: 10256</p>
                  </div>
                </div>
                <div className="winner-section-claim-price">
                  <button type="button" className="section-claim-pric">
                    Claim your prizes
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Confirmation;
