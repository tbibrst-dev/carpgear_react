import { PointLogs } from "../../types";

type Props = {
  points: number | string;
  pointsLogs: PointLogs[];
};



const CouponsUI: React.FC<Props> = ({ pointsLogs }) => {

console.log('pointsLogs', pointsLogs);


  return (

    pointsLogs && pointsLogs.length > 0 ?
      <div className="user-coupons-section ">

        <div className="user-coupons-section-header-main">
          <div className="user-coupons-section-header">
            <h1>Available Coupons & CGG Discount Creds</h1>
            <span>List of coupons which are valid & available for use. Click on the coupon to use it. The coupon discount will be visible only when at least one product is present in the cart.</span>
          </div>
        </div>

        <div className="user-coupons-section-details order-section-right-side">
          <div className="user-coupons-section-details-table-head row">
            <div className="coupons-table-heading col-md-8"> <span>Coupon</span> </div>
            <div className="coupons-table-heading col-md-2  coupons-table-heading-total"><span>Amount</span></div>
            <div className="coupons-table-heading col-md-2"><span></span></div>

          </div>
          {pointsLogs.map((log) => (
            <div className="user-coupons-section-details-table-row row">

              <div className="coupons-table-row col-md-8">
                <span>{log.description}
                </span>
              </div>

              <div className="coupons-table-row col-md-2">
                <div className="coupon-button">
                  <span>{log && log.data && log.data.discount_amount ? "£" + (log.data.discount_amount as number).toFixed(2) : "£" + 1.50.toFixed(2)}
                  </span>
                </div>
              </div>

              {/* <div className="coupons-table-row col-md-2">
                <span><button> Use Coupon</button>
                </span>
              </div> */}

            </div>
          ))}
        </div>

        {/* mobile view */}
        {pointsLogs.map((log) => (
          <div className="order-section-mobile-view mb-3">
            <div className="single-order-section-mobile-container">
              <div className="point-section-child-div">
                <div >
                  <span className="heading">COUPON</span>
                </div>
                <div className="heading-details-div">
                  <span className="heading-details">{log.description}</span>
                </div>
              </div>

              <div className="point-section-child-div">
                <span className="heading">TOTAL</span>
                <span className="heading-details total-heading">{log.date_display_human}</span>
              </div>


              {/* <div className="point-section-button-div">
                <span className="heading"><button>Use Coupon</button></span>
              </div> */}
            </div>
          </div>
        ))}
      </div>
      :
      <div className="user-points-section-empty-section row">
        <div className="user-points-section-details-empty-section">
          <span className="empty-page-message">
            You don’t have any coupons at present
          </span>

        </div>
      </div>
  );
};

export default CouponsUI;
