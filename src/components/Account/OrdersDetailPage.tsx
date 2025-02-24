import { useParams } from 'react-router-dom';
import useGetSingleOrder from "./hooks/getSingleOrder";
import { AUTH_TOKEN_KEY, decryptToken } from "../../utils";
import Details from "./Details";




const OrdersDetailPage = () => {
  const { order_id } = useParams();

  const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
  const token = decryptToken(encodedToken);
  const { order, isLoading } = useGetSingleOrder(token, order_id);


  return (
    isLoading ?
      <div className="basket-loader-container">
        <svg viewBox="25 25 50 50" className="loader-svg">
          <circle r={20} cy={50} cx={50} className="loader" />
        </svg>
      </div>
      :
      <div className="recent-right-side">
        <Details data={order} />
      </div>
  );
};

export default OrdersDetailPage;
