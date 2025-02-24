import axios from "axios";
import { useEffect, useState } from "react";
import { TOKEN } from "../../../utils";
import { OrderType } from "../../../types";

const useGetOrders = (token: string) => {
  const [data, setData] = useState<OrderType[]>([]);
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    // get data from API or Database here.
    const fetchOrders = async () => {
      try {
        setIsLoading(true);
        const res = await axios.post(
          "?rest_route=/api/v1/orders",
          { token },
          { headers: { Authorization: TOKEN } }
        );
        console.log(res);
        if (res.data.success) {
          setData(res.data.data);
        }
      } catch (error) {
        console.log(error);
      } finally {
        setIsLoading(false);
      }
    };
    fetchOrders();
  }, []);

  return { data, isLoading };
};

export default useGetOrders;
