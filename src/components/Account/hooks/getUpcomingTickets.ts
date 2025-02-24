import { useEffect, useState } from "react";
import { CompetitionType } from "../../../types";
import axios from "axios";
import { TOKEN } from "../../../utils";

type ReturnType = { data: CompetitionType[] | null; isLoading: boolean };

const useGetUpcomingTickets = (
  token: string,
  currentDate: string
): ReturnType => {
  const [upcomingTickets, setUpcomingTickets] = useState<
    CompetitionType[] | null
  >(null);

  const [isLoading, setIsLoading] = useState(true);
  // Fetch upcoming competitions
  useEffect(() => {
    const fetchTickets = async () => {
      try {
        //TODO: logic for api
        const response = await axios.post(
          "?rest_route=/api/v1/get_user_competition",
          {
            token,
            draw_date: currentDate,
          },
          {
            headers: {
              Authorization: TOKEN,
            },
          }
        );

        if (response.data.success) {
          setUpcomingTickets(response.data.data);
        }
      } catch (error) {
        console.log(error);
      } finally {
        setIsLoading(false);
      }
    };
    fetchTickets();
  }, []);

  return { data: upcomingTickets, isLoading };
};

export default useGetUpcomingTickets;
