import axios from "axios";
import { useEffect, useState } from "react";
import { TOKEN } from "../../../utils";
import { PointLogs } from "../../../types";

const useGetPoints = (token: string) => {
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [avialablePoints, setAvailablePoints] = useState(0);
  const [pointLogs, setPointsLogs] = useState<PointLogs[]>([]);

  useEffect(() => {
    const handleGetPoints = async () => {
      setIsLoading(true);
      try {
        const response = await axios.post(
          "?rest_route=/api/v1/points",
          { token },
          { headers: { Authorization: TOKEN } }
        );
        console.log(response);
        if (response.data.success) {
          setPointsLogs(response.data.logs);
          setAvailablePoints(response.data.points);
        }
      } catch (error) {
        console.log(error);
      } finally {
        setIsLoading(false);
      }
    };
    handleGetPoints();
  }, []);

  return { pointLogs, isLoading, avialablePoints };
};

export default useGetPoints;
