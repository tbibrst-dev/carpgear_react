import { useEffect, useState } from "react";
import {
  CART_HEADER,
  COMPS_QUEST,
  LEGAL_TERMS_ACTIVE_INDEX,
  NONCE_KEY,
  NONCE_TIMESTAMP,
  SUGGESTED_TICKETS,
  UPDATE_CART_KEY,
  calculateCartQuantity,
  calculatePercentage,
  cartError,
  // checkExistingCart,
  fetchNonceValue,
  oddsCalculator,
} from "../utils";
import { useDispatch, useSelector } from "react-redux";
import { RootState } from "../redux/store";
import { AnswersType, CompetitionType, PurchasedTickets } from "../types";
import axios from "axios";
import { addToCart, isAddingToCart } from "../redux/slices/cartSlice";
// import { toast } from "react-hot-toast";
import { useNavigate } from "react-router";
import { Modal } from 'bootstrap';
import Slider from '@mui/material/Slider';
import { showErrorToast } from '../showErrorToast';




type Question = {
  [key: number]: [key: string];
};

const CarouselModal = () => {
  const competition = useSelector(
    (state: RootState) => state.competition.competition
  );

  const { purchasedTickets } = useSelector(
    (state: RootState) => state.userReducer
  );

  const [oddsVariables, setOddsVariables] = useState({
    totalTickets: 0,
    quantity: 0,
    basketQuantity: 0,
  });
  const [activeIndex, setActiveIndex] = useState<number>(0);

  const [currentOdds, setCurrentOdds] = useState<any>(0);
  const [recomnmendedOdds, setRecommendedOdds] = useState<any>(0);
  const [questionOptions, setQuestionOptions] = useState<AnswersType>();
  const dispatch = useDispatch();
  const { isAdding, cartItems } = useSelector((state: RootState) => state.cart);
  const suggested_tickets = parseInt(
    localStorage.getItem(SUGGESTED_TICKETS) as string
  );
  const navigate = useNavigate();

  const [cartKeys, setCartKeys] = useState<{ [key: number]: { key: string } }>(
    {}
  );
  useEffect(() => {
    const keys = localStorage.getItem(UPDATE_CART_KEY) as string;
    const parsedKeys = keys ? JSON.parse(keys) : {};
    setCartKeys(parsedKeys);
  }, [cartItems]);

  const [selectedAnswer, setSelectedAnswer] = useState<string>("");

  const [cartQuantity, setCartQuantity] = useState<number>(0);

  // useEffect(() => {

  //   const purchasedTicketsCompetition = purchasedTickets.find((item) => parseInt(item.competition_id) === Number(competition.id)) as PurchasedTickets;
  //   const boughtTickets = parseInt(purchasedTicketsCompetition?.total_tickets);


  //   const quantity = calculateCartQuantity(cartItems);
  //   setCartQuantity(quantity || 0);

  //   const quantitySingle = cartItems.find((item) => (item.id) == (competition.id));

  //   // const remainingTickets =
  //   //   Number(competition.total_sell_tickets) -
  //   //   Number(competition.total_ticket_sold);

  //   const remainingTickets = Number(competition.total_sell_tickets);

  //   const currentOdds = oddsCalculator(remainingTickets, (Number(quantitySingle?.quantity) || 0) + (boughtTickets || 0));

  //   setCurrentOdds(currentOdds);
  //   const recomnmendedOdds = oddsCalculator(
  //     remainingTickets,
  //     Number(quantitySingle?.quantity) ? Number(quantitySingle?.quantity) + suggested_tickets + (boughtTickets || 0) : suggested_tickets + (boughtTickets || 0)
  //   );
  //   setRecommendedOdds(recomnmendedOdds);
  // }, [cartItems, competition]);

  if (!competition) {
    return null;
  }

  useEffect(() => {
    if (competition) {
      const competitionQuantity = Number(competition.quantity);
      const remainingTickets =
        Number(competition.total_sell_tickets) -
        Number(competition.total_ticket_sold);

      setOddsVariables((prevState) => ({
        ...prevState,
        totalTickets: remainingTickets,
        quantity: competitionQuantity ? competitionQuantity * 2 : 2,
        basketQuantity: cartQuantity || 2,
      }));

      Number(competition.comp_question) &&
        setQuestionOptions(JSON.parse(competition.question_options));
    }
  }, [competition]);

  const [quantity, setQuantity] = useState<number>(
    Number(competition.quantity) || 1
  );

  useEffect(() => {
    Number(competition.quantity)
      ? setQuantity(Number(competition.quantity))
      : setQuantity(1);
  }, [competition]);

  const calculateRemainingTime = (drawDate: string, drawTime: string) => {
    if (!drawDate) {
      return { days: 0, hours: 0, minutes: 0, seconds: 0 };
    }
    const targetDate =
      drawDate && drawTime
        ? new Date(`${drawDate}T${drawTime}`)
        : new Date(`${drawDate}`);
    const currentTime = new Date();
    const difference = targetDate.getTime() - currentTime.getTime();

    if (targetDate < currentTime) {
      return { days: 0, hours: 0, minutes: 0, seconds: 0 };
    }

    const days = Math.floor(difference / (1000 * 60 * 60 * 24));
    const hours = Math.floor(
      (difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
    );
    const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((difference % (1000 * 60)) / 1000);

    return { days, hours, minutes, seconds };
  };

  const [timeRemaining, setTimeRemaining] = useState(
    calculateRemainingTime(competition.draw_date, competition.draw_time)
  );

  useEffect(() => {
    const timer = setInterval(() => {
      setTimeRemaining(
        calculateRemainingTime(competition.draw_date, competition.draw_time)
      );
    }, 1000);

    return () => clearInterval(timer);
  }, [competition]);

  const handleQuantityChange = (operation: string) => {
    if (
      Number(competition?.max_ticket_per_user) === quantity &&
      operation === "increment"
    ) {
      showErrorToast(cartError());
      return;
    }
    const value =
      operation === "increment"
        ? quantity + 1
        : quantity > 1
          ? quantity - 1
          : quantity;

    setQuantity(value);

    // const oddsResult = oddsCalculator(oddsVariables.totalTickets, cartQuantity);
    // const recomnmendedOdds = oddsCalculator(
    //   oddsVariables.totalTickets,
    //   cartQuantity + suggested_tickets
    // );
    // setCurrentOdds(oddsResult);
    // setRecommendedOdds(recomnmendedOdds);
    console.log('oddsVariables', oddsVariables);
  };

  const handleAnswerChange = (value: string, index: number) => {
    activeIndex === index ? setActiveIndex(-1) : setActiveIndex(index);
    setSelectedAnswer(value);
    const answersCopy: Question = JSON.parse(
      localStorage.getItem(COMPS_QUEST) as string
    );
    const updatedAnswers = {
      ...answersCopy,
      [competition.competition_product_id]: value,
    };
    localStorage.setItem(COMPS_QUEST, JSON.stringify(updatedAnswers));
  };

  const handleAddToCart = async (competition: CompetitionType) => {
    const purchasedTicketsCompetition = purchasedTickets.find((item) => parseInt(item.competition_id) === Number(competition.id)) as PurchasedTickets;
    const ticketsAvailable = parseInt(competition?.total_sell_tickets) - parseInt(competition?.total_ticket_sold);
    //first we need to check
    const alreadyInCart = cartItems.find((item) => item.competition_product_id === competition.competition_product_id);

    const totalTicketsInCart = alreadyInCart ? parseInt(alreadyInCart.quantity) : 0;
    if (alreadyInCart) {
      const ticketsQtyLeft = parseInt(competition.max_ticket_per_user) - totalTicketsInCart;
      const boughtTickets = parseInt(purchasedTicketsCompetition?.total_tickets);

      if (ticketsQtyLeft <= 0) {
        showErrorToast(
          "You already have the maximum number of tickets for this competition in cart!"
        );
        dispatch(isAddingToCart(false));
        return;
      }

      if (totalTicketsInCart >= ticketsAvailable) {
        dispatch(isAddingToCart(false));
        showErrorToast(
          `Oops! It seems you've already have ${totalTicketsInCart + boughtTickets} tickets for this competition in cart.`
        );
        return;
      }

      // if (quantity > ticketsQtyLeft) {
      //   dispatch(isAddingToCart(false));
      //   showErrorToast(
      //     `Oops! It seems you've already have ${totalTicketsInCart + boughtTickets} tickets for this competition in cart. You can only get ${ticketsQtyLeft > ticketsAvailable ? ticketsQtyLeft : ticketsAvailable} more.`
      //   );
      //   return;
      // }
      if (quantity > ticketsQtyLeft) {
        dispatch(isAddingToCart(false));
        showErrorToast(
          `Only  ${ticketsQtyLeft > ticketsAvailable ? ticketsQtyLeft : ticketsAvailable} tickets available,${totalTicketsInCart + boughtTickets} tickets have been added to your basket.`
        );
        return;
      }

      

    }


    if (ticketsAvailable === 0) {
      showErrorToast(
        "Oops! There is not ticket left to purchase for this competition"
      );
      return;
    }
    if (quantity > ticketsAvailable) {
      showErrorToast(
        `You can only buy ${ticketsAvailable} tickets for this competition`
      );
      return;
    }



    if (purchasedTicketsCompetition) {
      const boughtTickets = parseInt(purchasedTicketsCompetition?.total_tickets);
      const ticketsQtyLeft = parseInt(competition.max_ticket_per_user) - boughtTickets;
      if (ticketsQtyLeft <= 0) {
        showErrorToast(
          "You already have the maximum number of tickets for this competition"
        );
        dispatch(isAddingToCart(false));
        return;
      }

      if (quantity > ticketsQtyLeft) {
        dispatch(isAddingToCart(false));
        showErrorToast(
          `Oops! It seems you've already bought ${boughtTickets} tickets for this competition. You can only get ${ticketsQtyLeft} more.`
        );
        return;
      }
    }



    const modalElement = document.getElementById('exampleModal-3');
    if (modalElement) {
      const modal = new Modal(modalElement);
      modal.show();
    }

    dispatch(isAddingToCart(true));
    let nonceVal = localStorage.getItem(NONCE_KEY);
    const storedTimestamp = localStorage.getItem(NONCE_TIMESTAMP) as string;

    //* chekc if nonce exist if not refetch the nonce if nonce exists check timestamp
    if (!nonceVal || !storedTimestamp) {
      const res: any = await fetchNonceValue();
      nonceVal = res.nonce;
      const timestamp = Date.now();
      localStorage.setItem(NONCE_KEY, res.nonce);
      localStorage.setItem(NONCE_TIMESTAMP, timestamp.toString());
    } else {
      const timestampDiff = Date.now() - parseInt(storedTimestamp, 10);
      if (timestampDiff > 11 * 60 * 60 * 1000) {
        const res: any = await fetchNonceValue();
        nonceVal = res.nonce;
        const timestamp = Date.now();
        localStorage.setItem(NONCE_KEY, res.nonce);
        localStorage.setItem(NONCE_TIMESTAMP, timestamp.toString());
      }
    }
    const cart_header = localStorage.getItem(CART_HEADER) || undefined;

    // const URL = "?rest_route=/wc/store/v1/cart/add-item";
    const URL = import.meta.env.VITE_ADD_TO_CART_API;
    try {
      const response = await axios.post(
        URL,
        {
          id: competition.competition_product_id,
          quantity,
          nonce: nonceVal,
          cart_header,
        },
        {
          headers: {
            "X-WC-Store-api-nonce": nonceVal,  //only for cgg live
            Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`,
          },
        }
      );

      if (response.status === 200 || response.status === 201) {
        const cart_header = response.data.cart_header;
        const items = response.data.items.map((item: any) => {
          const competition = item.competition;
          competition.quantity = item.quantity.toString();
          competition.totals = item.totals;
          const competitionWithKey = { ...competition, key: item.key };
          return competitionWithKey;
        });

        items.forEach((item: any) => {
          const newKeys = {
            ...cartKeys,
            [item.id as number]: { key: item.key },
          };
          localStorage.setItem(UPDATE_CART_KEY, JSON.stringify(newKeys));
        });
        if (cart_header) {
          localStorage.setItem(CART_HEADER, cart_header);
        }
        dispatch(addToCart(items));
      }
    } catch (error) {
      console.log(error);
    } finally {
      dispatch(isAddingToCart(false));
    }
  };

  const disbaleTickets = parseInt(competition.disable_tickets) ? true : false;
  // const hideTicketCount = parseInt(competition.hide_ticket_count)
  //   ? true
  //   : false;
  const hideTimer = parseInt(competition.hide_timer) ? true : false;

  const [divColorlefts, setDivColorleft] = useState('#2CB4A5');
  const [divColorRights, setDivColorRight] = useState('#2CB4A5');
  const [isSliderColor, setSliderColor] = useState('#2CB4A5');

  const handleChangeSlider = (_event: Event, newValue: number | number[]) => {
    setQuantity(newValue as number);
    setDivColorRight('#2CB4A5');  // Change to slider color when pressed
    setDivColorleft('#2CB4A5');  // Change to slider color when pressed
    setSliderColor('#2CB4A5');

  };
  const handleSliderChangeCommitted = () => {
    setDivColorRight('#FFBB41');  // Change to slider color when pressed
    setDivColorleft('#FFBB41');  // Change to slider color when pressed
    setSliderColor('#FFBB41');

  };

  useEffect(() => {
    const purchasedTicketsCompetition = purchasedTickets.find(
      (item) => parseInt(item.competition_id) === Number(competition.id)
    ) as PurchasedTickets;
    const boughtTickets = parseInt(purchasedTicketsCompetition?.total_tickets);
    const safeBoughtTickets = Number.isNaN(boughtTickets) ? 0 : boughtTickets;

    const quantitys = calculateCartQuantity(cartItems);
    const quantitySingle = cartItems.find((item) => item.id == competition.id);

    setCartQuantity(quantitys || 0);

    // const remainingTickets =
    //   Number(competition.total_sell_tickets) -
    //   Number(competition.total_ticket_sold);

    const remainingTickets = Number(competition.total_sell_tickets);

    const currentOdds = oddsCalculator(
      remainingTickets,
      (Number(quantitySingle?.quantity) || 0) + (Number(quantity || 0)) + (safeBoughtTickets || 0)
    );

    setCurrentOdds(currentOdds);

    const recomnmendedOdds = oddsCalculator(
      remainingTickets,
      Number(quantitySingle?.quantity)
        ? Number(quantitySingle?.quantity) +
        suggested_tickets +
        (safeBoughtTickets || 0) + (Number(quantity || 0))
        : suggested_tickets + (safeBoughtTickets || 0)
    );

    console.log("remainingTickets", remainingTickets);
    console.log("quantity", quantity);
    console.log("safeBoughtTickets", safeBoughtTickets);
    console.log("recomnmendedOdds", recomnmendedOdds);

    setRecommendedOdds(recomnmendedOdds);
  }, [cartItems, quantity, competition]);
  
  return (
    <div>
      <div className="main-enter-pop">
        <div
          className="modal fade enter-popup"
          id="enter"
          data-bs-backdrop="static"
          data-bs-keyboard="false"
          tabIndex={-1}
          aria-labelledby="enterLabel"
          aria-hidden="true"
        >
          <div className="modal-dialog">
            <div className="modal-content">
              <div className="modal-header">
                <div className="enter-pop-head">
                  <h4>Ready to Enter?</h4>
                  <p>First answer this simple question.</p>
                </div>
                <div className="enter-bait-boat">
                  <p>{competition.question}</p>
                  <div className="enter-bait-boat-select">
                    <form>
                      <div
                        className={`form-group selct-one ${activeIndex === 1 && "active"
                          }`}
                      >
                        <input
                          type="radio"
                          id="why-one"
                          name="answer"
                          value={questionOptions?.answer1}
                          onChange={(e) =>
                            handleAnswerChange(e.target.value, 1)
                          }
                          checked={activeIndex === 1}
                        />
                        <label htmlFor="why-one">
                          {questionOptions?.answer1}
                        </label>
                      </div>
                      <div
                        className={`form-group selct-one ${activeIndex === 2 && "active"
                          }`}
                      >
                        <input
                          type="radio"
                          id="why-two"
                          name="answer"
                          value={questionOptions?.answer2}
                          onChange={(e) =>
                            handleAnswerChange(e.target.value, 2)
                          }
                          checked={activeIndex === 2}
                        />
                        <label htmlFor="why-two">
                          {questionOptions?.answer2}
                        </label>
                      </div>
                      <div
                        className={`form-group selct-one ${activeIndex === 3 && "active"
                          }`}
                      >
                        <input
                          type="radio"
                          id="why-three"
                          name="answer"
                          onChange={(e) =>
                            handleAnswerChange(e.target.value, 3)
                          }
                          checked={activeIndex === 3}
                          value={questionOptions?.answer3}
                        />
                        <label htmlFor="why-three">
                          {questionOptions?.answer3}
                        </label>
                      </div>
                    </form>
                  </div>
                </div>
                <div className="enter-pop-middle">
                  <div className="prog-and-butns">
                    <div className="comp-progress-all">
                      <div className="progs-71">
                        <div
                          className="progs-71-shade"
                          style={{
                            width: `${calculatePercentage(
                              Number(competition.total_ticket_sold),
                              Number(competition.total_sell_tickets)
                            )}%`,
                          }}
                        ></div>
                        <h5>
                          <div className="progs-lef">
                            <img src="images/Ticket icon 1.svg" alt="" />
                            <h4>{competition.total_ticket_sold}</h4>
                          </div>
                          <div className="progs-rgt">
                            <h4>{competition.total_sell_tickets}</h4>
                          </div>
                        </h5>
                      </div>
                      <div className="progs-per">
                        <h4>
                          {calculatePercentage(
                            Number(competition.total_ticket_sold),
                            Number(competition.total_sell_tickets)
                          )}
                          %
                        </h4>
                      </div>
                    </div>
                    <div className="five-tickets-txt">
                        <p>
                          <span>{quantity}</span>Tickets{" "}
                        </p>
                      </div>
                    <div className="five-tickets-btnss">
                     

                      <div className="five-tickets-min">
                        <button
                          type="button"
                          className="five-tickets-mins"
                          onClick={() => handleQuantityChange("decrement")}
                          style={{ backgroundColor: divColorlefts }}
                                onMouseDown={() => {
                                  setDivColorleft('#2CB4A5');  // Change to slider color when pressed
                                  setSliderColor('#2CB4A5');
                                }}
                                onMouseUp={() => {
                                  setDivColorleft('#FFBB41');  // Revert to original color when released
                                  setSliderColor('#FFBB41');
                                }}
                                onTouchStart={() => {
                                  setDivColorleft('#2CB4A5');  // For mobile touch
                                  setSliderColor('#2CB4A5');
                                }}
                                onTouchEnd={() => {
                                  setDivColorleft('#FFBB41');  // For mobile touch release
                                  setSliderColor('#FFBB41');
                                }}
                        >
                          <svg
                            width={15}
                            height={3}
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
                      </div>

                      <Slider
                        defaultValue={Number(quantity)}
                        aria-label="Default" valueLabelDisplay="auto"
                        min={1}
                        max={Number(competition?.max_ticket_per_user)}
                        value={quantity}
                        onChange={handleChangeSlider}
                        onChangeCommitted={handleSliderChangeCommitted}
                        sx={{
                          color: `${isSliderColor || '#FFBB41'} !important`,
                          '& .MuiSlider-thumb': {
                            backgroundColor: `${isSliderColor || '#FFBB41'} !important`,
                          },
                          '& .MuiSlider-track': {
                            backgroundColor: `${isSliderColor || '#FFBB41'} !important`,
                          },
                        }}
                      />


                      <div className="five-tickets-pls">
                        <button
                          type="button"
                          className="five-tickets-plus"
                          onClick={() => handleQuantityChange("increment")}
                          style={{ backgroundColor: divColorRights }}

                          onMouseDown={() => {
                            setDivColorRight('#2CB4A5');  // Change to slider color when pressed
                            setSliderColor('#2CB4A5');
                          }}
                          onMouseUp={() => {
                            setDivColorRight('#FFBB41');  // Revert to original color when released
                            setSliderColor('#FFBB41');
                          }}
                          onTouchStart={() => {
                            setDivColorRight('#2CB4A5');  // For mobile touch
                            setSliderColor('#2CB4A5');
                          }}
                          onTouchEnd={() => {
                            setDivColorRight('#FFBB41');  // For mobile touch release
                            setSliderColor('#FFBB41');
                          }}
                        >
                          <svg
                            width={25}
                            height={25}
                            viewBox="0 0 25 25"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path
                              d="M13.5 11.5V6.5C13.5 6.23478 13.3946 5.98043 13.2071 5.79289C13.0196 5.60536 12.7652 5.5 12.5 5.5C12.2348 5.5 11.9804 5.60536 11.7929 5.79289C11.6054 5.98043 11.5 6.23478 11.5 6.5V11.5H6.5C6.23478 11.5 5.98043 11.6054 5.79289 11.7929C5.60536 11.9804 5.5 12.2348 5.5 12.5C5.5 12.7652 5.60536 13.0196 5.79289 13.2071C5.98043 13.3946 6.23478 13.5 6.5 13.5H11.5V18.5C11.5 18.7652 11.6054 19.0196 11.7929 19.2071C11.9804 19.3946 12.2348 19.5 12.5 19.5C12.7652 19.5 13.0196 19.3946 13.2071 19.2071C13.3946 19.0196 13.5 18.7652 13.5 18.5V13.5H18.5C18.7652 13.5 19.0196 13.3946 19.2071 13.2071C19.3946 13.0196 19.5 12.7652 19.5 12.5C19.5 12.2348 19.3946 11.9804 19.2071 11.7929C19.0196 11.6054 18.7652 11.5 18.5 11.5H13.5Z"
                              fill="#202323"
                            />
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                  <button
                    type="button"
                    className="pop-enter-pop"
                    data-bs-toggle="modal"
                    // data-bs-target="#exampleModal-3"
                    data-bs-dismiss="modal"
                    data-bs-close="Close"
                    disabled={!selectedAnswer || isAdding || disbaleTickets}
                    onClick={() => handleAddToCart(competition)}
                  >
                    {isAdding ? "Please wait..." : "Enter"}
                  </button>
                </div>
                <div className="pop-enter-second-bottom">
                  <h6>
                    🐡 Your current odds:{" "}
                    <span className="spc-1"> {currentOdds}</span>{" "}
                  </h6>
                  <h5>
                    🎣{" "}
                    <span className="mob-spn">
                      {" "}
                      {suggested_tickets}
                    </span>{" "}
                    more tickets and that goes up to{" "}
                    <span className="spc"> {recomnmendedOdds}</span>
                  </h5>
                </div>
                {/* <div class="pop-enter-second-bottom-mob">
      <h6>🐟 Your current odds are  <span>1 / 6790</span> </h6>
      <h5>🎣  5 more tickets and they go up to <span class="spc" >1 / 50</span> </h5>

    </div> */}
                <div className="enter-pop-bottom-section">
                  {!hideTimer && (
                    <div className="enter-pop-bottom-timing">
                      <p>
                        Closes in{" "}
                        <span>
                          {timeRemaining.days}d, {timeRemaining.hours}h,{" "}
                          {timeRemaining.minutes}m, {timeRemaining.seconds}s
                        </span>
                      </p>
                    </div>
                  )}
                  <div className="enter-pop-bottom-terms">
                    <p>
                      All orders are subject to our{" "}
                      <a
                        href="#"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                        onClick={() => {
                          localStorage.setItem(LEGAL_TERMS_ACTIVE_INDEX, "1");
                          navigate("/legal-terms");
                        }}
                      >
                        Terms
                      </a>{" "}
                      &amp;{" "}
                      <a
                        href="#"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                        onClick={() => {
                          localStorage.setItem(LEGAL_TERMS_ACTIVE_INDEX, "3");
                          navigate("/legal-terms");
                        }}
                      >
                        Privacy
                      </a>{" "}
                      . For free postal entry route <a href="#"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                        onClick={() => {
                          navigate("/free-postal-route");
                        }}>see here.</a>{" "}
                    </p>
                  </div>
                </div>
                <button
                  type="button"
                  className="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CarouselModal;
