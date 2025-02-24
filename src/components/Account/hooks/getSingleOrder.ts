import axios from "axios";
import { useEffect, useState } from "react";
import { TOKEN } from "../../../utils";

const useGetSingleOrder = (token:string, id:any) => {
  const [order, setOrder] = useState({});
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    const fetchSingleOrder = async () => {
      setIsLoading(true);
      try {
        const response = await axios.post(
          "?rest_route=/api/v1/order_detail",
          { token ,id},
          { headers: { Authorization: TOKEN } }
        );
        if (response.data) {     
            
          setOrder(response.data.data);
        }
      } catch (error) {
        console.log(error);
      }finally {
        setIsLoading(false);
      }
    };
    fetchSingleOrder();
  }, []);

  return { order, isLoading };
};

export default useGetSingleOrder;
