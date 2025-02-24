// import { ReactElement, JSXElementConstructor, ReactNode, ReactPortal } from "react";
import moment from 'moment';

type Props = {
  data: any
};

type Options = {
  month: 'long' | 'short' | 'narrow';
  day: 'numeric' | '2-digit';
  year: 'numeric' | '2-digit';
}

const Details: React.FC<Props> = ({ data }) => {

  const dateCreated = data &&  data.date_created ? new Date(data.date_created.date) : null;
  const formattedDate = dateCreated ? moment(dateCreated).format('MMMM D, YYYY') : "";

  function formatTimeToAMPM(time: string) {
    if (time) {
      // Parse the time string into hours and minutes
      const [hours, minutes] = time.split(':').map(Number);
      // Check if it's AM or PM
      const suffix = hours >= 12 ? 'PM' : 'AM';
      // Convert hours from 24-hour format to 12-hour format
      const displayHours = hours % 12 || 12;
      // Format the time in AM/PM format
      return `${displayHours}:${minutes < 10 ? '0' : ''}${minutes} ${suffix}`;
    } else {
      return 'N/A';
    }

  }

  function formatDate(date: string) {
    const dateCreated = new Date(date);
    const options: Options = { month: 'long', day: 'numeric', year: 'numeric' };
    const formattedDates = dateCreated ? dateCreated.toLocaleDateString(undefined, options) : "";
    return formattedDates
  }
  return (
    <>


      <div className="order-view-main-div-desktop">
        <div className=" order-section-right-side order-details-section mb-3">
          <span className="order-number-with-status">Order  <span className="order-number">#{data.id}</span> was places on  <span className="order-date">{formattedDate} </span>and is currently<span className="order-status"> {data.status}</span> </span>
        </div>

        <div className="order-section-right-side mb-3">
          <div className="order-details-section">
            <div className="order-section-table-head">
              <div className="right-side-order-details">
                <h6>PRODUCT</h6>
              </div>
              <div className="left-side-order-details">
                <h6>TOTAL</h6>
              </div>

            </div>
            {
              data && data.product && data.product.length > 0 && (
                data.product.map((product: { draw_time: string; title: string; quantity: string; tickets: string[]; draw_date: string; price: any; }) => (
                  <div className="order-section-table-row-order-details  mb-3" key={product.title}>
                    <div className="right-side-order-details">

                      <div className="product-title">
                        <span>{product.title}</span> x {product.quantity}
                      </div>

                      <div className="product-ticket-info">
                        {
                          product.tickets && product.tickets.length > 0 ?
                        <h5 className="ticket-heading-number">{`Ticket Number(s):`}</h5>
                          :""
                        }
                        <div className="number-tickets-order">
                          {product.tickets.map(ticket => <div className="number-tickets number-tickets-new-design" key={ticket}>
              <p>{ticket}</p>
              <div className="number-tickets-new-design-end"></div>

            </div>)}
                        </div>
                      </div>
                      <div className="product-draw-info">
                        <h6 className="draw-dates-wise">

                          <span className="date-wise-draw">Draw Date: </span>
                          {product.draw_date ? (
                            <>
                              {formatDate(product.draw_date)} @ {formatTimeToAMPM(product.draw_time)}
                            </>
                          ) : (
                            'N/A'
                          )}

                        </h6>
                      </div>

                    </div>
                    <div className="left-side-order-details">
                      <div className="product-price">
                        <h6>£  {product && product.price ? parseFloat(product.price).toFixed(2) : "N/A"}</h6>
                      </div>

                    </div>
                  </div>
                ))
              )
            }


            <div className="order-section-table-row-order-details no-border">
              <div className="right-side-order-details">
                <div className="order-billing-detail-div">
                  <span>SUBTOTAL  </span>
                </div>

                <div className="order-billing-detail-div">
                  <span>SHIPPING </span>
                </div>

                <div className="order-billing-detail-div">
                  <span>PAYMENT METHOD </span>
                </div>

                <div className="order-billing-detail-div ">
                  <span className="total-sections">TOTAL </span>
                </div>
              </div>


              <div className="left-side-order-details">
                <div className="order-billing-detail-div prices-right">
                  <span>£{data && data.total ? parseFloat(data.total).toFixed(2) : "N/A"} </span>
                </div>

                <div className="order-billing-detail-div prices-right">
                  <span>Flat Rate </span>
                </div>

                <div className="order-billing-detail-div prices-right">
                <span>{data && data.payment_method_title ? data.payment_method_title :'N/A'} </span>
                </div>

                <div className="order-billing-detail-div prices-right">
                  <span className="total-section">£{data && data.total ? parseFloat(data.total).toFixed(2) : "N/A"}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="order-section-right-side mb-3">
          <div className="order-details-section">
            <div className="order-billing-address">
              <strong>BILLING ADDRESS</strong>
              {data.billing && (
                <>
                  <span>{data.billing.first_name} {data.billing.last_name}</span>
                  <span>{data.billing.address_1}</span>
                  <span>{data.billing.address_2}</span>
                  <span>{data.billing.city}</span>
                  <span>{data.billing.state} {data.billing.postcode}</span>
                  <span>{data.billing.phone}</span>
                  <span className="billing-email">{data.billing.email}</span>

                </>
              )}
            </div>


            <div className="sipping-billing-address">
              <strong>SHIPPING ADDRESS</strong>
              {data.shipping && (
                <>
                  <span>{data.shipping.first_name} {data.shipping.last_name}</span>
                  <span>{data.shipping.address_1}</span>
                  <span>{data.shipping.address_2}</span>
                  <span>{data.shipping.city}</span>
                  <span>{data.shipping.state} {data.shipping.postcode}</span>
                </>
              )}
            </div>
          </div>

        </div>


      </div>



      <div className={`order-section-mobile-view order-section-mobile-view-div`}>
        <div className={`single-order-section-mobile-container`}>
          <span className="order-number-with-status">Order  <span className="order-number">#{data.id}</span> was places on  <span className="order-date">{formattedDate} </span>and is currently<span className="order-status"> {data.status}</span> </span>
        </div>

      </div>

      <div className={`order-section-mobile-view order-section-mobile-view-div `}>
        <div className={`single-order-section-mobile-container`}>
          <div className="right-side-order-details">
            <h6>PRODUCT</h6>
          </div>
          {
            data && data.product && data.product.length > 0 && (
              data.product.map((product: { draw_time: string; title: string; quantity: string; tickets: string[]; draw_date: string; price: string | number }) => (
                <div className="order-section-table-row-order-details mb-3" key={product.title}>
                  <div className="right-side-order-details">

                    <div className="product-title">
                      <span>{product.title}</span> x {product.quantity}
                    </div>

                    <div className="product-ticket-info">
                      <h6>Ticket Number(s)</h6>
                      <div className="number-tickets-order">
                        {product.tickets.map(ticket => <span key={ticket}>{ticket}</span>)}
                      </div>
                    </div>
                    <div className="product-draw-info">
                      <h6 className="draw-dates-wise"> <span className="date-wise-draw">Draw Date: </span>{product.draw_date}  @  {formatTimeToAMPM(product.draw_time)}</h6>
                    </div>

                  </div>
                  <div className="left-side-order-details">
                    <div className="product-price">
                      {/* <h6>£{product.price}</h6> */}
                    </div>

                  </div>
                </div>
              ))
            )
          }
          <div className="order-section-table-row-order-details total-section-single-order no-border">
            <div className="right-side-order-details-mobile">
              <div className="order-billing-detail-mobile">
                <span>SUBTOTAL  </span>
              </div>

              <div className="order-billing-detail-mobile">
                <span>SHIPPING </span>
              </div>

              <div className="order-billing-detail-mobile">
                <span>PAYMENT METHOD </span>
              </div>

              <div className="order-billing-detail-mobile ">
                <span className="total-section">TOTAL </span>
              </div>
            </div>


            <div className="left-side-order-details-mobile">
              <div className="order-billing-detail-mobile">
                <span>£{data && data.total ? data.total : 0.00} </span>
              </div>

              <div className="order-billing-detail-mobile">
                <span>Flat Rate </span>
              </div>

              <div className="order-billing-detail-mobile">
                <span>{data && data.payment_method_title ? data.payment_method_title :'N/A'} </span>
              </div>

              <div className="order-billing-detail-mobile">
                <span className="total-section">£{data.total}</span>
              </div>
            </div>
          </div>

        </div>

      </div>

      <div className={`order-section-mobile-view order-section-mobile-view-div `}>
        <div className={`single-order-section-mobile-container`}>
          <div className="order-details-section">
            <div className="order-billing-address">
              <strong>BILLING ADDRESS</strong>
              {data.billing && (
                <>
                  <span>{data.billing.first_name} {data.billing.last_name}</span>
                  <span>{data.billing.address_1}</span>
                  <span>{data.billing.address_2}</span>
                  <span>{data.billing.city}</span>
                  <span>{data.billing.state} {data.billing.postcode}</span>
                  <span>{data.billing.phone}</span>
                  <span className="billing-email">{data.billing.email}</span>

                </>
              )}
            </div>


            <div className="sipping-billing-address">
              <strong>SHIPING ADDRESS</strong>
              {data.shipping && (
                <>
                  <span>{data.shipping.first_name} {data.shipping.last_name}</span>
                  <span>{data.shipping.address_1}</span>
                  <span>{data.shipping.address_2}</span>
                  <span>{data.shipping.city}</span>
                  <span>{data.shipping.state} {data.shipping.postcode}</span>
                </>
              )}
            </div>
          </div>

        </div>

      </div>
    </>
  );
};

export default Details;
