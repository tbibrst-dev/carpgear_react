import { SetStateAction, useEffect, useState } from "react";
import { CompetitionType, QunatityType } from "../../types";
import DesktopViewComps from "./Desktop-view";
import MobileViewComps from "./Mobile-view";
import InfiniteScroll from "react-infinite-scroll-component";
import axios from "axios";
import { useParams } from "react-router";
import Loader from "../../common/Loader";
import {
  UPDATE_CART_KEY,
  // cartError,
  handleAddToCart,
  navigateCompetition,
} from "../../utils";
import { useSelector } from "react-redux";
// import { toast } from "react-hot-toast";
import { showErrorToast } from '../../showErrorToast';

import { RootState } from "../../redux/store";
// import { isMobile } from 'react-device-detect';
import { useLocation, useNavigate } from 'react-router-dom';




const Competitions = () => {
  const { category } = useParams();
  const [competitions, setCompetitions] = useState<CompetitionType[]>([]);
  const [quantities, setQuantities] = useState<QunatityType>({});
  const [page, setPage] = useState<number>(1);
  const [hasMore, setHasMore] = useState(true);
  const [fetching, setFetching] = useState(true);
  const [loading, setLoading] = useState(true);
  const [cartKeys, setCartKeys] = useState<{ [key: number]: { key: string } }>(
    {}
  );
  const cartItems = useSelector((state: RootState) => state.cart.cartItems);
  useEffect(() => {
    const keys = localStorage.getItem(UPDATE_CART_KEY) as string;
    const parsedKeys = keys ? JSON.parse(keys) : {};
    setCartKeys(parsedKeys);
  }, [cartItems]);

  useEffect(() => {
    setPage(1); // Reset page to 1 when category changes
  }, [category]);

  useEffect(() => {
    setFetching(true);
    setLoading(true);
    setCompetitions([]);
    fetchCompetitions(1);
    return () => {
      setHasMore(true);
      setFetching((prev) => !prev);
      setLoading(true);
    };
  }, [category]);

  let baseUrl =
    category === "drawn_next_competition"
      ? `/?rest_route=/api/v1/drawn_next_competition`
      : category === "singular_competition" ? `/?rest_route=/api/v1/get_singular_competition` : `/?rest_route=/api/v1/competition`;

  const fetchCompetitions = async (currentPage: number) => {
    try {
      const res = await axios.post(
        baseUrl,
        {
          limit: 10,
          category: category === "all" ? "" : category,
          order_by: "draw_date",
          order: "asc",
          page: currentPage,
          token: import.meta.env.VITE_TOKEN,

          status: category === "finished_and_sold_out" ? "Finished" : "Open",
        },
        { headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` } }
      );

      console.log('res---',res);

      if (res.data.success == "true"  || res.data.success == true) {
        const initialQuantities: { [key: number]: number } = {};
        // const newCompetitions = [...competitions, ...res.data.data];
        res.data.data.forEach((competition: CompetitionType) => {
          initialQuantities[competition.id] = parseInt(competition.quantity)
            ? parseInt(competition.quantity)
            : 1;
        });
        // setQuantities(initialQuantities);
        setQuantities((prevQuantities) => ({
          ...prevQuantities,
          ...initialQuantities,
        }));
        setCompetitions((prevCompetitions) => [
          ...prevCompetitions,
          ...res.data.data,
        ]);
        setPage((prevPage) => prevPage + 1);
      }
    } catch (error) {
      console.log(error);
      setHasMore(false);
    } finally {
      setFetching(false);
      setLoading(false);
    }
  };

  //  =

  //* fetch competition

  //todo handle quantity change of a competition
  const handleQuantityChange = (
    id: number,
    newQuantity: number,
    action: "increment" | "decrement"
  ) => {
    const competition = competitions.find(
      (comp) => comp.id === id
    ) as CompetitionType;
    if (
      Number(competition?.max_ticket_per_user) === quantities[id] &&
      action === "increment"
    ) {
      // toast.error(cartError());
      showErrorToast('You have entered the maximum number of tickets');

      return;
    }
    setQuantities((prevQuantities) => ({
      ...prevQuantities,
      [id]: Math.max(
        1,
        action === "increment"
          ? prevQuantities[id] + newQuantity
          : prevQuantities[id] - newQuantity
      ),
    }));
  };

  //   id: number,
  //   category: string,
  //   competitionName: string
  // ) => {
  //   dispatch(setDetailComp({ id, category }));
  //   const splitName = competitionName.split(" ");
  //   const editedName = splitName.join("-");
  //   const competitionsToBeFetched = JSON.parse(
  //     localStorage.getItem(COMPETITIONS_TO_BE_FETCHED) as string
  //   );

  //   const newCompetitionToAddToLocalStorage = { id: id, category };

  //   const newCompsToFetched = {
  //     ...competitionsToBeFetched,
  //     ...newCompetitionToAddToLocalStorage,
  //   };
  //   localStorage.setItem(
  //     COMPETITIONS_TO_BE_FETCHED,
  //     JSON.stringify(newCompsToFetched)
  //   );

  //   localStorage.setItem(
  //     FETCHED_COMPETITION,
  //     JSON.stringify(newCompetitionToAddToLocalStorage)
  //   );

  //   navigate(`/competition/details/${editedName}`);
  // };

  const handleQuantityChangeInput = (id: number, value: number) => {
    let parsedValue: number;

    if (isNaN(value)) return;
    //* check if user input is not more than max ticket per user
    const competition = competitions.find(
      (item) => item.id === id
    ) as CompetitionType;
    if (value > parseInt(competition.max_ticket_per_user)) {
      setQuantities((prevQuantities) => ({
        ...prevQuantities,
        [id]: parseInt(competition.max_ticket_per_user),
      }));
      return;
    }

    if (!value) {
      parsedValue = 0;
    } else {
      parsedValue = value;
    }
    setQuantities((prevQuantities) => ({
      ...prevQuantities,
      [id]: parsedValue,
    }));
  };

  function renderCategoryText() {
    switch (category) {
      case "drawn_next_competition":
        return "drawn next";
      case "the_big_gear":
        return "The Big gear";
      case "the_accessories_and_bait":
        return "THE ACCESSORIES & BAIT";
      case "finished_and_sold_out":
        return "FINISHED / SOLD OUT";
      case "singular_competition":
        return "comps for everyone";
      default:
        return "Competitions";
    }
  }


  const [activeTab, setActiveTab] = useState('all');
  const location = useLocation();
  const navigate = useNavigate();

  useEffect(() => {
    // Set active tab based on the current URL path
    if (location.pathname.includes('/competitions/all')) {
      setActiveTab('all');
    } else if (location.pathname.includes('/competitions/singular_competition')) {
      setActiveTab('comps-for-everyone');
    } else if (location.pathname.includes('/competitions/instant_win_comps')){
      setActiveTab('instant-wins'); // Default to 'instant-wins' if no match
    }else{
      setActiveTab(''); 
    }
  }, [location.pathname]);

  const handleTabClick = (tabName: SetStateAction<string>, path: string) => {
    setActiveTab(tabName);
    navigate(path);
  };



  const [screenWidth, setScreenWidth] = useState(window.innerWidth);
  useEffect(() => {
    const handleResize = () => {
      setScreenWidth(window.innerWidth);
    };

    window.addEventListener('resize', handleResize);

    // Clean up the event listener on component unmount
    return () => {
      window.removeEventListener('resize', handleResize);
    };
  }, []);

  console.log("Fetching page competitions:", competitions);

  return (
    <>
      <div className="comp-banner">
        <div className="comp-banner-txt">
          <h2>{renderCategoryText()}</h2>
        </div>
      </div>
      <div className="compe-page-tabs">
        <div className="anchor-nav ">
          <div className="tab-nav homepage-tab-links">
            {
              screenWidth >= 567 ?
                <span
                  // href="#"
                  className={`tab-link ${activeTab === 'all' ? 'active' : ''}`}
                  onClick={() => handleTabClick('all', '/competitions/all')}
                  style={{ borderRight: '1px solid #FFFFFF1A' }}
                >
                  All Comps
                </span>
                :
                <span
                  // href="#"
                  className={`tab-link ${activeTab === 'all' ? 'active' : ''}`}
                  onClick={() => handleTabClick('all', '/competitions/all')}
                  style={{ borderRight: '1px solid #FFFFFF1A' }}
                >
                  All
                </span>

            }

            <span
              // href="#"
              className={`tab-link ${activeTab === 'instant-wins' ? 'active' : ''}`}
              onClick={() => handleTabClick('instant-wins', '/competitions/instant_win_comps')}
              style={{ borderRight: '1px solid #FFFFFF1A' }}
            >
              Instantly Win
            </span>
            <span
              // href="#"
              className={`tab-link ${activeTab === 'comps-for-everyone' ? 'active' : ''}`}
              onClick={() => handleTabClick('comps-for-everyone', '/competitions/singular_competition')}
              style={{ borderRight: '1px solid #FFFFFF1A' }}
            >
              Comps for Everyone
            </span>
          </div>
        </div>
      </div>


      {loading ? (
        <Loader />
      ) : (
        <>
          <div className="competion-boxes">
            <div className="container">

              <div className="competion-boxes-all">
                <InfiniteScroll
                  dataLength={competitions.length}
                  next={() => fetchCompetitions(page)}
                  loader={
                    <h4 style={{ textAlign: "center", color: "white" }}></h4>
                  }
                  hasMore={hasMore}
                  style={{ overflowX: "hidden" }}
                >
                  <div className="compt-main-sen">
                    {competitions.length > 0
                      ? competitions?.map((competition) => (

                         
                          <DesktopViewComps
                            key={competition.id}
                            quantities={quantities}
                            competition={competition}
                            handleQuantityChange={handleQuantityChange}
                            navigateCompetition={navigateCompetition}
                            handleAddToCart={handleAddToCart}
                            cartKeys={cartKeys}
                            handleQuantityChangeInput={
                              handleQuantityChangeInput
                            }
                          />
                          

                      ))
                      : !fetching && (
                        <div style={{ margin: "150px auto" }}>
                          <h4
                            style={{
                              color: "white",
                              textAlign: "center",
                              margin: "0 auto",
                            }}
                            className="center-align"
                          >
                            {category === "instant_win_comps"
                              ? "Instant win competitions will appear here when any are live!"
                              : category === "drawn_next_competition"
                                ? "Draw next competitions are not available right now!"
                                : category === "the_big_gear"
                                  ? "Big gear competitions are not available right now!"
                                  : category === "the_accessories_and_bait"
                                    ? "Accessories and bait competitions are not available right now!"
                                    : category === "finished_and_sold_out"
                                      ? "Finished and sold competitions will appear here"
                                      : "Competitions will appear here when any are live!"}
                          </h4>
                        </div>
                      )}
                  </div>
                </InfiniteScroll>
              </div>
            </div>
          </div>


          {/* <InfiniteScroll
            dataLength={competitions.length}
            next={() => fetchCompetitions(page)}
            loader={<h4 style={{ textAlign: "center", color: "white" }}></h4>}
            hasMore={hasMore}
            style={{ overflowX: "hidden" }}
          > */}
          <div className="mobile-comp-area">

            <div className="container">
              {competitions.length > 0
                ? competitions.map((competition) => (
                  <MobileViewComps
                    competition={competition}
                    key={competition.id}
                    handleQuantityChange={handleQuantityChange}
                    quantities={quantities}
                    navigateCompetition={navigateCompetition}
                    handleAddToCart={handleAddToCart}
                    handleQuantityChangeInput={handleQuantityChangeInput}
                    cartKeys={cartKeys}
                  />
                ))
                : !fetching && (
                  <div style={{ margin: "150px 0" }}>
                    <h4
                      style={{
                        color: "white",
                        textAlign: "center",
                      }}
                    >
                      {category === "instant_wins_coms"
                        ? "Instant wins competitions are not available right now!"
                        : category === "drawn_next_competition"
                          ? "Draw next competitions are not available right now!"
                          : category === "the_big_gear"
                            ? "Big gear competitions are not available right now!"
                            : category === "the_accessories_and_bait"
                              ? "Accessories and bait competitions are not available right now!"
                              : category === "finished_and_sold_out"
                                ? "Finished and sold competitions are not available right now!"
                                : "competitions are not available right now!"}
                    </h4>
                  </div>
                )}
            </div>
          </div>
          {/* </InfiniteScroll> */}
        </>
      )}
    </>
  );
};

export default Competitions;
