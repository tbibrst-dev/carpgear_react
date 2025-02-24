import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Pagination } from "swiper/modules";
import {
  useGetFeaturedCompsMutation,
  useGetRecommendedCompsMutation,
} from "../redux/queries";
import { useEffect, useState } from "react";
import { CompetitionType, QunatityType } from "../types";
import Loader from "./Loader";
import { useNavigate } from "react-router";
import CarouselModal from "./CarouselModal";
import { useDispatch, useSelector } from "react-redux";
import {
  setCurrentCompetition,
  setFetching,
  setRecommendedComps,
} from "../redux/slices";
import { RootState } from "../redux/store";
import {
  UPDATE_CART_KEY,
  cartError,
  handleAddToCart,
  navigateCompetition,
} from "../utils";
// import { toast } from "react-hot-toast";
import { showErrorToast } from '../showErrorToast';


const CACHE_KEY = "featuredComps";
// const CACHE_TIMEOUT = 1 * 1000; // 10 minutes in milliseconds
const CACHE_TIMEOUT = import.meta.env.GLOBAL_CACHE_TIMEOUT


const Carousel = () => {
  const navigate = useNavigate();
  const cartItems = useSelector((state: RootState) => state.cart.cartItems);
  const [mutate, { isLoading }] = useGetFeaturedCompsMutation();
  const [competitions, setCompetitions] = useState<CompetitionType[]>([]);
  const [quantities, setQuantities] = useState<QunatityType>({});
  const [fetchingComps, setFetchingComps] = useState<boolean>(true);
  const [fetchRecommendedComps] = useGetRecommendedCompsMutation();
  const dispatch = useDispatch();
  const [cartKeys, setCartKeys] = useState<{ [key: number]: { key: string } }>(
    {}
  );

  const { purchasedTickets, user } = useSelector(
    (state: RootState) => state.userReducer
  );

  const [isAccountLocked, setIsAccountLock] = useState(false);



  useEffect(() => {
    if (user && parseInt(user.lock_account)) {
      setIsAccountLock(true);


    }
  }, [user]);

  useEffect(() => {
    const keys = localStorage.getItem(UPDATE_CART_KEY) as string;
    const parsedKeys = keys ? JSON.parse(keys) : {};
    setCartKeys(parsedKeys);
  }, [cartItems]);
  const recommendComps = useSelector(
    (state: RootState) => state.competition.recommendComps
  );

  //* quanitity setter function
  const handleQuantitySetter = (competitions: CompetitionType[]) => {
    const initialQuantities: QunatityType = {};
    competitions.forEach((competition) => {
      initialQuantities[competition.id] = parseInt(competition.quantity)
        ? parseInt(competition.quantity)
        : 1;
      setQuantities(initialQuantities);
    });
  };

  useEffect(() => {
    const fetchInstantWinCompetitons = async () => {
      const cachedData = localStorage.getItem(CACHE_KEY);
      const cachedTimestamp = localStorage.getItem(`${CACHE_KEY}_timestamp`);
      const currentTime = new Date().getTime();

      if (cachedData && cachedTimestamp) {
        const timeDiff = currentTime - parseInt(cachedTimestamp, 10);
        if (timeDiff < CACHE_TIMEOUT) {
          const parsedCompetitions: CompetitionType[] = JSON.parse(cachedData);
          setCompetitions(parsedCompetitions);
          handleQuantitySetter(parsedCompetitions);
          return;
        }
      }
      try {
        const res: any = await mutate({
          limit: 5,
          category: "the_big_gear",
          order_by: "id",
          order: "desc",
          token: import.meta.env.VITE_TOKEN,
        });
        if (!res.error) {
          setCompetitions(res.data.data);
          const initialQuantities: { [key: number]: number } = {};
          res.data.data.forEach((competition: CompetitionType) => {
            initialQuantities[competition.id] = parseInt(competition.quantity)
              ? parseInt(competition.quantity)
              : 1;
          });
          setQuantities(initialQuantities);
          localStorage.setItem(CACHE_KEY, JSON.stringify(res.data.data));
          localStorage.setItem(
            `${CACHE_KEY}_timestamp`,
            currentTime.toString()
          );
        }
      } catch (error) {
        console.log(error);
      } finally {
        setFetchingComps(false);
      }
    };
    fetchInstantWinCompetitons();
  }, []);

  //todo function to calculate sold ticket percentage
  const calculatePercentage = (
    totalSold: number,
    totalTickets: number
  ): number => {
    if (totalSold === null || !totalSold) {
      return 0;
    }
    let percentage = Math.floor((totalSold / totalTickets) * 100);
    return percentage;
  };

  if (isLoading) {
    return <Loader />;
  }

  const truncateText = (text: string, maxLength: number) => {
    if (text.length > maxLength) {
      return text.substring(0, maxLength) + "...";
    }
    return text;
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
      showErrorToast(cartError());
      return;
    }
    setQuantities((prevQuantities) => ({
      ...prevQuantities,
      [id]: Math.max(
        0,
        action === "increment"
          ? prevQuantities[id] + newQuantity
          : prevQuantities[id] - newQuantity
      ),
    }));
  };

  const handleQuantityChangeInput = (id: number, value: number) => {
    let parsedValue: number;
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

    if (isNaN(value)) return;
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

  const handleModalOpen = async (competition: CompetitionType) => {
    const isExistingComps =
      recommendComps.length > 0
        ? recommendComps.every((item) => item.category === competition.category)
        : false;
    if (isExistingComps) {
      return;
    }
    dispatch(setFetching(true));
    try {
      const res: any = await fetchRecommendedComps({
        limit: 3,
        order: "desc",
        order_by: "draw_date",
        token: import.meta.env.VITE_TOKEN,
        category: competition.category,
        id: competition.id,
      });
      if (!res.error) {
        dispatch(setRecommendedComps(res.data.data));
      }
    } catch (error) {
      console.log(error);
    } finally {
      dispatch(setFetching(false));
    }
  };

  return (
    <div>
      <div className="banner">
        <div className="container-fluid">
          <div className="swiper banner-Swiper">
            {competitions.length > 0 ? (
              <>
                <Swiper
                  className="swiper-wrapper"
                  modules={[Pagination, Navigation]}
                  navigation={{
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                  }}
                >
                  {competitions.map((competition) => {
                    const soldPercentage = calculatePercentage(
                      Number(competition.total_ticket_sold),
                      Number(competition.total_sell_tickets)
                    );
                    const isEnabledQuestion = Number(competition.comp_question) ? true : false;
                    const isEnabledQuestionGlobaly = Number(localStorage.getItem('SHOW_QUESTION')) ? true : false;
                    console.log('isEnabledQuestionGlobaly--carousol', isEnabledQuestionGlobaly);



                    return (
                      <SwiperSlide
                        className="swiper-slide"
                        key={competition.id}
                      >
                        <div
                          className="carosel-all"
                          onClick={() =>
                            navigateCompetition(
                              competition.id,
                              competition.category,
                              competition.title,
                              navigate
                            )
                          }
                          style={{
                            backgroundImage: `url(${competition.image})`,
                          }}
                        >
                          <div className="banner-txt">
                            <h2 style={{ overflow: "hidden" }}>
                              {truncateText(competition.title, 16)}
                            </h2>
                            <div className="main-all">
                              <div className="secon">
                                <div
                                  className="secon-shade"
                                  style={{ width: `${soldPercentage}%` }}
                                ></div>
                                <h5>
                                  <div className="lef">
                                    <svg
                                      width="12"
                                      height="9"
                                      viewBox="0 0 13 9"
                                      fill="none"
                                      xmlns="http://www.w3.org/2000/svg"
                                    >
                                      <path
                                        d="M12.6115 3.2222L12.5373 1.80678C12.508 1.24774 12.0311 0.818318 11.4721 0.847611L1.51277 1.36956C0.953829 1.39883 0.524428 1.87575 0.553726 2.43479L0.6284 3.85966C1.14618 3.90655 1.56476 4.32664 1.59277 4.86101C1.62077 5.39538 1.2484 5.85697 0.738345 5.95753L0.813029 7.38259C0.842317 7.94144 1.31922 8.37087 1.87816 8.34158L11.8375 7.81963C12.3964 7.79034 12.8258 7.31341 12.7966 6.75456L12.7224 5.33893C12.1587 5.3421 11.6862 4.90255 11.6564 4.33362C11.6266 3.7647 12.0505 3.27819 12.6115 3.2222ZM3.6767 7.60695L3.3285 7.6252L3.28091 6.71702L3.62911 6.69877L3.6767 7.60695ZM3.59308 6.01138L3.24488 6.02963L3.19729 5.12148L3.54549 5.10323L3.59308 6.01138ZM3.50946 4.41586L3.16126 4.43411L3.11366 3.52574L3.46186 3.5075L3.50946 4.41586ZM3.42585 2.82034L3.07765 2.83859L3.03005 1.93041L3.37825 1.91216L3.42585 2.82034Z"
                                        fill="white"
                                      ></path>
                                    </svg>
                                    <h4>{competition.total_ticket_sold}</h4>
                                  </div>
                                  <div className="rgt">
                                    <h4>{competition.total_sell_tickets}</h4>
                                  </div>
                                </h5>
                              </div>
                              <div className="per">
                                <h4>{soldPercentage}%</h4>
                              </div>
                            </div>
                            <div className="banner-btnss">
                              <div className="button-all">
                                <div className="banner-btn-left">
                                  <div className="increase-quantity">
                                    <form>
                                      <input
                                        // type="number"
                                        id="number"
                                        value={quantities[competition.id]}
                                        onChange={(
                                          event: React.ChangeEvent<HTMLInputElement>
                                        ) =>
                                          handleQuantityChangeInput(
                                            competition.id,
                                            Number(event.target.value)
                                          )
                                        }
                                        onClick={(e) => e.stopPropagation()}
                                      />
                                      <button
                                        className="value-button"
                                        id="decrease"
                                        onClick={(e) => {
                                          e.preventDefault();
                                          e.stopPropagation();
                                          handleQuantityChange(
                                            competition.id,
                                            1,
                                            "decrement"
                                          );
                                        }}
                                      >
                                        <svg
                                          width="15"
                                          height="3"
                                          viewBox="0 0 15 3"
                                          fill="none"
                                          xmlns="http://www.w3.org/2000/svg"
                                        >
                                          <path
                                            d="M1.5 0.5H13.5C13.7652 0.5 14.0196 0.605357 14.2071 0.792893C14.3946 0.98043 14.5 1.23478 14.5 1.5C14.5 1.76522 14.3946 2.01957 14.2071 2.20711C14.0196 2.39464 13.7652 2.5 13.5 2.5H1.5C1.23478 2.5 0.98043 2.39464 0.792893 2.20711C0.605357 2.01957 0.5 1.76522 0.5 1.5C0.5 1.23478 0.605357 0.98043 0.792893 0.792893C0.98043 0.605357 1.23478 0.5 1.5 0.5Z"
                                            fill="#202323"
                                          />
                                        </svg>
                                      </button>
                                      <button
                                        className="value-button"
                                        id="increase"
                                        onClick={(e) => {
                                          e.preventDefault();
                                          e.stopPropagation();
                                          handleQuantityChange(
                                            competition.id,
                                            1,
                                            "increment"
                                          );
                                        }}
                                      >
                                        <svg
                                          width="15"
                                          height="15"
                                          viewBox="0 0 15 15"
                                          fill="none"
                                          xmlns="http://www.w3.org/2000/svg"
                                        >
                                          <path
                                            d="M8.83331 6.5V1.5C8.83331 1.23478 8.72796 0.98043 8.54042 0.792893C8.35288 0.605357 8.09853 0.5 7.83331 0.5C7.5681 0.5 7.31374 0.605357 7.12621 0.792893C6.93867 0.98043 6.83331 1.23478 6.83331 1.5V6.5H1.83331C1.5681 6.5 1.31374 6.60536 1.12621 6.79289C0.93867 6.98043 0.833313 7.23478 0.833313 7.5C0.833313 7.76522 0.93867 8.01957 1.12621 8.20711C1.31374 8.39464 1.5681 8.5 1.83331 8.5H6.83331V13.5C6.83331 13.7652 6.93867 14.0196 7.12621 14.2071C7.31374 14.3946 7.5681 14.5 7.83331 14.5C8.09853 14.5 8.35288 14.3946 8.54042 14.2071C8.72796 14.0196 8.83331 13.7652 8.83331 13.5V8.5H13.8333C14.0985 8.5 14.3529 8.39464 14.5404 8.20711C14.728 8.01957 14.8333 7.76522 14.8333 7.5C14.8333 7.23478 14.728 6.98043 14.5404 6.79289C14.3529 6.60536 14.0985 6.5 13.8333 6.5H8.83331Z"
                                            fill="#202323"
                                          />
                                        </svg>
                                      </button>
                                    </form>
                                  </div>
                                </div>
                                <div className="banner-btn-right">
                                  {

                                    isAccountLocked ?

                                      <button
                                        type="button"
                                        className="enter-btn"
                                        onClick={(e) => {
                                          e.stopPropagation();
                                          showErrorToast(`Your account has been locked!`);


                                        }}
                                      >ENTER</button>


                                      :
                                      <button
                                        type="button"
                                        className="enter-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target={
                                          isEnabledQuestion 
                                            ? "#enter"
                                            : "#exampleModal-3"
                                        }
                                        onClick={(e) => {
                                          e.stopPropagation();
                                          const updatedQty =
                                            quantities[competition.id];
                                          const updatedComp = { ...competition };
                                          updatedComp.quantity =
                                            updatedQty.toString();
                                          dispatch(
                                            setCurrentCompetition(updatedComp)
                                          );
                                          handleModalOpen(competition);
                                          if (!isEnabledQuestion ) {
                                            handleAddToCart(
                                              competition,
                                              cartItems,
                                              dispatch,
                                              quantities,
                                              cartKeys,
                                              purchasedTickets
                                            );
                                          }
                                        }}
                                        disabled={
                                          quantities[competition.id] === 0

                                        }
                                      >
                                        ENTER
                                      </button>
                                  }

                                </div>
                              </div>
                            </div>
                          </div>

                          <div className="fish-bar">
                            <img src="images/bidd.png" alt="" />
                          </div>
                          <div className="fish-bar-two">
                            <img src="images/mob-stick.svg" alt="" />
                          </div>
                        </div>
                      </SwiperSlide>
                    );
                  })}
                </Swiper>
                <div className="swiper-button-next" />
                <div className="swiper-button-prev" />
              </>
            ) : (
              <div style={{ margin: "150px 0" }}>
                <h4
                  style={{
                    color: "white",
                    textAlign: "center",
                  }}
                >
                  {!fetchingComps &&
                    " Featured competitions are not available right now!"}
                </h4>
              </div>
            )}
          </div>
        </div>
      </div>
      {/* {modal && <CarouselModal />} */}
      <CarouselModal />
    </div>
  );
};

export default Carousel;
