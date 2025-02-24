import { OrderType } from "../../types";
import { useNavigate } from "react-router-dom";


type Props = {
  orders: OrderType[];
};

const Orders: React.FC<Props> = ({ orders }) => {
  const navigate = useNavigate();

  const viewOrderOnClick = (order_id: any) => {
    navigate(`/account/recent/orders/order-detail/${order_id}`);
  }

  // const convertDateFormat = (inputDate: string): string => {
  //   const [month, day, year] = inputDate.split(' ');
  
  //   const monthNames: { [key: string]: number } = {
  //     January: 0, February: 1, March: 2, April: 3,
  //     May: 4, June: 5, July: 6, August: 7,
  //     September: 8, October: 9, November: 10, December: 11
  //   };
  //   const monthNumber = monthNames[month];
  
  //   const date = new Date(parseInt(year), monthNumber, parseInt(day));
    
  //   const dayFormatted = String(date.getDate()).padStart(2, '0');
  //   const monthFormatted = String(date.getMonth() + 1).padStart(2, '0');
  //   const yearFormatted = date.getFullYear();
  
  //   return `${dayFormatted} / ${monthFormatted} / ${yearFormatted}`;
  // };

  return (

    orders && orders.length > 0 ?
     
        <>
          {/* order right */}
          <div className="order-section-right-side main-div-right-side-account">
            {/* table head */}
            <div className="order-section-table-head">
              <div className="order-section-table-head-div-1">
                <h6>status</h6>
              </div>
              <div className="order-section-table-head-div">
                <h6>order</h6>
              </div>
              <div className="order-section-table-head-div">
                <h6>date</h6>
              </div>
              <div className="order-section-table-head-div">
                <h6>total</h6>
              </div>
              <div className="order-section-table-head-div">
                <h6 style={{width:197}}></h6>
              </div>
            </div>
            {/* table head */}
            {/* table row */}
            {orders.map((order) => (
              <div className="order-section-table-row">
                <div className="order-section-table-row-div-1">
                  <h6 className="status">{order.order_status}</h6>
                                  </div>
                <div className="order-section-table-row-div">
                  <h6>#{order.order_id_}</h6>
                </div>
                <div className="order-section-table-row-div">
                  <h6>{ order && order.order_date ? (order.order_date) : "N/A"}</h6>
                </div>
                <div className="order-section-table-row-div">
                  <h6>
                    <span
                      dangerouslySetInnerHTML={{ __html: order.order_total }}
                    ></span>{" "}
                    <span className="fade-font">
                      for {order.item_count} Items
                    </span>
                  </h6>
                </div>
                <div className="order-section-table-row-div">
                  <button onClick={() => viewOrderOnClick(order.order_id_)}>view</button>
                </div>
              </div>
            ))}
          </div>
          {/* order right */}
          {/* mobile orders table */}
          {orders.map((order, index) => (
            <div
              className={`order-section-mobile-view ${index !== 0 && "mt-3"}`}
              key={order.order_id_}
            >
              <div className="order-section-mobile-container">
                <div className="order-section-mobile-details">
                  <div className="title">
                    <h6>Order</h6>
                  </div>
                  <div className="title">
                    <h6>Date</h6>
                  </div>
                  <div className="title">
                    <h6>Total</h6>
                  </div>
                  <div className="title">
                    <h6>Status</h6>
                  </div>
                </div>
                <div className="order-section-mobile-details">
                  <div className="title">
                    <p>#{order.order_id_}</p>
                  </div>
                  <div className="title">
                    <p>{ order && order.order_date ? (order.order_date) : "N/A"}</p>
                  </div>
                  <div className="title">
                    <p>
                      <span
                        dangerouslySetInnerHTML={{ __html: order.order_total }}
                      ></span>{" "}
                      <span className="fade-text-mob">
                        for {order.item_count} Items
                      </span>
                    </p>
                  </div>
                  <div className="title">
                    <div className="status">{order.order_status}</div>
                  </div>
                </div>
              </div>
              {/* button */}
              <div className="order-sec-mob-button">
                <button onClick={() => viewOrderOnClick(order.order_id_)}>view</button>
              </div>
            </div>
          ))}
        </>
     
      : <div className="user-points-section-empty-section row  ">
        <div className="user-points-section-details-empty-section">
          <span className="empty-page-message">
            You don’t have any orders at present
          </span>

        </div>
      </div>
  );
};

export default Orders;
