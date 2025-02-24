import { useEffect, useState } from "react";
import { AUTH_TOKEN_KEY, decryptToken, getTodayDate } from "../../utils";
import useGetUpcomingTickets from "./hooks/getUpcomingTickets";
import { setUpcomingTicketsUser } from "../../redux/slices/userSlice";
import { useDispatch, useSelector } from "react-redux";
import { RootState } from "../../redux/store";
import UpcomingTickets from "./tickets-components/UpcomingTickets";
import useGetDrawnTickets from "./hooks/getDrawnTickets";
import DrawnTicket from "./tickets-components/DrawnTickets";
import WonTickets from "./tickets-components/WonTickets";
import getWonTickets from "./hooks/getWonTickets";
import { useLocation, useNavigate } from "react-router";


const Account = () => {

  const [spanTopPos] = useState<number>(() => {
    if (navigator.userAgent.includes("Mac")) {
      return 4;
    } else {
      return 7;
    }
  });

  const [activeTab, setActiveTab] = useState("upcoming"); // Default tab is "upcoming"
  const location = useLocation();
  const navigate = useNavigate();

  useEffect(() => {
    const searchParams = new URLSearchParams(location.search);
    const tab = searchParams.get("tab");
    if (tab) {
      setActiveTab(tab);
    }
  }, [location]);




  const dispatch = useDispatch();
  const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
  let token;
  if (encodedToken) {
    token = decryptToken(encodedToken);
  }
  const currentDate = getTodayDate();
  const { data, isLoading } = useGetUpcomingTickets(
    token as string,
    currentDate
  );

  const { data: drawnTickets, isLoading: fetchingDrawnTickets } =
    useGetDrawnTickets(token as string, currentDate);

  const { data: wonTickets, isLoading: fetchingWonTickets } =
  getWonTickets(token as string);


  const { upcomingTickets } = useSelector(
    (state: RootState) => state.userReducer
  );

  useEffect(() => {
    if (data) {
      dispatch(setUpcomingTicketsUser(data));
    }
  }, [data]);

  if (isLoading || fetchingDrawnTickets || fetchingWonTickets) {
    return (
      <div className="basket-loader-container">
        <svg viewBox="25 25 50 50" className="loader-svg">
          <circle r={20} cy={50} cx={50} className="loader" />
        </svg>
      </div>
    );
  }



  // console.log('userAgent', navigator?.userAgent)
  // console.log('usnavigator.userAgent.includes("Mac")erAgent', navigator.userAgent.includes("Mac"))
  // console.log('wonTickets', wonTickets)
  const handleTabChange = (tab: string) => {
    navigate(`/account?tab=${tab}`);
  };


  return (
    <div className="ticket-right-side">
      <div className="ticket-right-side-tabs">
        <ul className="nav nav-tabs nav-justified mb-3" id="ex1" role="tablist">
          <li className="nav-item" role="presentation">
            <a
             className={`nav-link ${activeTab === "upcoming" ? "active" : ""}`}
             onClick={() => handleTabChange("upcoming")}
              id="ex3-tab-1"
              data-bs-toggle="tab"
              href="#ex3-tabs-1"
              role="tab"
              aria-controls="ex3-tabs-1"
              aria-selected={activeTab === "upcoming"}
              style={{
                paddingTop: `${spanTopPos}px`,
              }}
            >
              Upcoming
            </a>
          </li>
          <li className="nav-item" role="presentation">
            <a
             className={`nav-link ${activeTab === "drawn" ? "active" : ""}`}
             onClick={() => handleTabChange("drawn")}
              id="ex3-tab-2"
              data-bs-toggle="tab"
              href="#ex3-tabs-2"
              role="tab"
              aria-controls="ex3-tabs-2"
              aria-selected={activeTab === "drawn"}
              style={{
                paddingTop: `${spanTopPos}px`,
              }}
            >
              Drawn
            </a>
          </li>

          <li className="nav-item" role="presentation">
            <a
              className={`nav-link ${activeTab === "won" ? "active" : ""}`}
              onClick={() => handleTabChange("won")}
              id="ex3-tab-3"
              data-bs-toggle="tab"
              href="#ex3-tabs-3"
              role="tab"
              aria-controls="ex3-tabs-3"
              aria-selected={activeTab === "won"}
              style={{
                paddingTop: `${spanTopPos}px`,
              }}
            >
              Won
            </a>
          </li>
        </ul>

        <div className="tab-content" id="ex2-content">
          <div
            className={`tab-pane fade ${activeTab === "upcoming" ? "show active" : ""}`}
            id="ex3-tabs-1"
            role="tabpanel"
            aria-labelledby="ex3-tab-1"
          >
            {upcomingTickets ? (
              upcomingTickets?.map((ticket) => (
                <UpcomingTickets tickets={ticket} key={ticket.id} />
              ))
            ) : (
              <div style={{ marginTop: "100px" }}>
                <h4
                  className="text-white text-center text-uppercase"
                  style={{ fontWeight: 800 }}
                >
                  You have no tickets for upcoming competitions
                </h4>
              </div>
            )}
          </div>

          <div
            className={`tab-pane fade ${activeTab === "drawn" ? "show active" : ""}`}
            id="ex3-tabs-2"
            role="tabpanel"
            aria-labelledby="ex3-tab-2"
          >
            {drawnTickets?.length ? (
              drawnTickets.map((ticket) => (
                <DrawnTicket ticket={ticket} key={ticket.id} />
              ))
            ) : (
              <div style={{ marginTop: "100px" }}>
                <h4
                  className="text-white text-center text-uppercase"
                  style={{ fontWeight: 800 }}
                >
                  You have no tickets for drawn competitions
                </h4>
              </div>
            )}
          </div>

          <div
            className={`tab-pane fade ${activeTab === "won" ? "show active" : ""}`}
            id="ex3-tabs-3"
            role="tabpanel"
            aria-labelledby="ex3-tab-3"
          >
            {wonTickets?.length ? (
              wonTickets.map((ticket) => (
                <WonTickets tickets={ticket} key={ticket.id} />
              ))
            ) : (
              <div style={{ marginTop: "100px" }}>
                <h4
                  className="text-white text-center text-uppercase"
                  style={{ fontWeight: 800 }}
                >
                  YOU HAVE NOT WON ANY COMPS YET!
                </h4>
              </div>
            )}
          </div>

        </div>
      </div>
    </div>
  );
};

export default Account;
