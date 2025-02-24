import { useEffect, useState } from "react";
import { CompetitionType } from "../../../types";
import axios from "axios";
import { TOKEN } from "../../../utils";

type ReturnType = { data: CompetitionType[] | null; isLoading: boolean };

const useGetDrawnTickets = (token: string, currentDate: string): ReturnType => {
  const [drawnTickets, setDrawnTickets] = useState<CompetitionType[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const fetchTickets = async () => {
      try {
        //TODO: logic for api
        const response = await axios.post(
          "?rest_route=/api/v1/get_user_competition",
          {
            token,
            draw_date: currentDate,
            type: "drawn",
          },
          {
            headers: {
              Authorization: TOKEN,
            },
          }
        );
        if (response.data.success) {
          setDrawnTickets(response.data.data);
        }
      } catch (error) {
        console.log(error);
      } finally {
        setIsLoading(false);
      }
    };
    fetchTickets();
  }, []);

  return { data: drawnTickets, isLoading };
};

export default useGetDrawnTickets;
