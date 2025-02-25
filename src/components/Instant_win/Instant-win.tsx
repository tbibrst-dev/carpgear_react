import { SetStateAction, useEffect, useState } from "react";
import { CompetitionType, QunatityType } from "../../types";
import DesktopViewComps from "./Desktop-view";
import MobileViewComps from "./Mobile-view";
import InfiniteScroll from "react-infinite-scroll-component";
import axios from "axios";
import Loader from "../../common/Loader";
import { useSelector } from "react-redux";
import { RootState } from "../../redux/store";
import {
  UPDATE_CART_KEY,
  handleAddToCart,
  navigateCompetition,
} from "../../utils";
// import { toast } from "react-hot-toast";
import { isMobile } from 'react-device-detect';
import { useLocation, useNavigate } from 'react-router-dom';
import { showErrorToast } from '../../showErrorToast';


const InstantWinsComps = () => {
  // const { category } = useParams();
  const [competitions, setCompetitions] = useState<CompetitionType[]>([]);
  const [quantities, setQuantities] = useState<QunatityType>({});
  const [page, setPage] = useState<number>(1);
  const [hasMore, setHasMore] = useState(true);
  const [fetching, setFetching] = useState(true);
  const [loading, setLoading] = useState(true);
  const { cartItems } = useSelector((state: RootState) => state.cart);
  const [cartKeys, setCartKeys] = useState<{ [key: number]: { key: string } }>(
    {}
  );

  useEffect(() => {
    const keys = localStorage.getItem(UPDATE_CART_KEY) as string;
    const parsedKeys = keys ? JSON.parse(keys) : {};
    setCartKeys(parsedKeys);
  }, [cartItems]);

  useEffect(() => {
    fetchCompetitions();
    return () => setHasMore(true);
  }, []);

  //* fetch competition
  const fetchCompetitions = async () => {
    try {
      const res = await axios.post(
        `/?rest_route=/api/v1/competition`,
        {
          limit: 10,
          category: "instant_win_comps",
          order_by: "draw_date",
          order: "asc",
          token: import.meta.env.VITE_TOKEN,
          page,
          status: "Open",
        },
        { headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` } }
      );
      if (res.data.success === "true") {
        const initialQuantities: { [key: number]: number } = {};
        // const newCompetitions = [...competitions, ...res.data.data];
        res.data.data.forEach((competition: CompetitionType) => {
          initialQuantities[competition.id] =
            parseInt(competition.quantity) || 1;
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
        setPage(page + 1);
      }
    } catch (error) {
      console.log(error);
      setHasMore(false);
    } finally {
      setFetching(false);
      setLoading(false);
    }
  };

  //todo handle quantity change of a competition
  const handleQuantityChange = (
    id: number,
    newQuantity: number,
    action: "increment" | "decrement"
  ) => {
    const competition = competitions.find((comp) => comp.id === id);
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

  // if (loading) {
  //   return <Loader />;
  // }

  const [activeTab, setActiveTab] = useState('instant-wins');
  const location = useLocation();
  const navigate = useNavigate();


  useEffect(() => {
    // Set active tab based on the current URL path
    if (location.pathname.includes('/competitions/all')) {
      setActiveTab('all');
    } else if (location.pathname.includes('/competitions/singular_competition')) {
      setActiveTab('comps-for-everyone');
    } else {
      setActiveTab('instant-wins'); // Default to 'instant-wins' if no match
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




 
  return (
    <>
      <div className="comp-banner">
        <div className="comp-banner-txt">
          <h2>INSTANTLY WIN</h2>
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
                  next={fetchCompetitions}
                  loader={
                    <h4 style={{ textAlign: "center", color: "white" }}></h4>
                  }
                  hasMore={hasMore}
                  style={{ overflowX: "hidden" }}
                >
                  <div className="compt-main-sen">
                    {competitions.length > 0
                      ? competitions?.map((competition) => (
                        !isMobile && competition.via_mobile_app == 0 ?
                          <DesktopViewComps
                            key={competition.id}
                            quantities={quantities}
                            competition={competition}
                            handleQuantityChange={handleQuantityChange}
                            navigateCompetition={navigateCompetition}
                            handleAddToCart={handleAddToCart}
                            handleQuantityChangeInput={
                              handleQuantityChangeInput
                            }
                            cartKeys={cartKeys}
                          /> : ""
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
                            Instant wins competitions are not available right
                            now!
                          </h4>
                        </div>
                      )}
                  </div>
                </InfiniteScroll>
              </div>
            </div>
          </div>
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
                      Instant wins competitions are not available right now!
                    </h4>
                  </div>
                )}
            </div>
          </div>
        </>
      )}
    </>
  );
};

export default InstantWinsComps;
