import Orders from "./Orders";
import useGetOrders from "./hooks/getOrders";
// import useGetSingleOrder from "./hooks/getSingleOrder";
import { AUTH_TOKEN_KEY, decryptToken } from "../../utils";

const OrdersPage = () => {
  const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
  const token = decryptToken(encodedToken);
  const { data, isLoading } = useGetOrders(token);
  // const { order, isLoading: isFetching } = useGetSingleOrder();

  if (isLoading) {
    return (
      <div className="basket-loader-container">
        <svg viewBox="25 25 50 50" className="loader-svg">
          <circle r={20} cy={50} cx={50} className="loader" />
        </svg>
      </div>
    );
  }

  return (
    <>
      <Orders orders={data} />
    </>
  );
};

export default OrdersPage;
