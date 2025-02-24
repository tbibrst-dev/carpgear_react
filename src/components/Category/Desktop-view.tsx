import { CompetitionType, PurchasedTickets, QunatityType } from "../../types";
import {
  calculatePercentage,
  isDrawToday,
  isDrawTomorrow,
  truncateText,
} from "../../utils";
import CountdownTimer from "../../common/Countdown";
import { useDispatch, useSelector } from "react-redux";
import {
  setCurrentCompetition,
  setFetching,
  setRecommendedComps,
} from "../../redux/slices";
import { useGetRecommendedCompsMutation } from "../../redux/queries";
import { RootState } from "../../redux/store";
import { Dispatch } from "@reduxjs/toolkit";
import { useCallback, useEffect, useState } from "react";
import { NavigateFunction, useNavigate } from "react-router";
// import toast from "react-hot-toast";
import { showErrorToast } from "../../showErrorToast";

import Slider from '@mui/material/Slider';


interface PropsType {
  competition: CompetitionType;
  quantities: QunatityType;
  handleQuantityChange: (
    id: number,
    newQuantity: number,
    action: "increment" | "decrement"
  ) => void;
  navigateCompetition: (
    id: number,
    category: string,
    competitionName: string,
    navigate: NavigateFunction
  ) => void;
  handleAddToCart: (
    competition: CompetitionType,
    cartItems: CompetitionType[],
    dispatch: Dispatch,
    quantities: QunatityType,
    cartKeys: { [key: number]: { key: string } },
    purchasedTickets: PurchasedTickets[],
    isEnabledQuestion: any
  ) => void;
  handleQuantityChangeInput: (id: number, value: number) => void;
  cartKeys: {};
}

const DesktopViewComps: React.FC<PropsType> = ({
  competition,
  quantities,
  handleQuantityChange,
  navigateCompetition,
  handleAddToCart,
  // handleQuantityChangeInput,
  cartKeys,
}) => {
  //* importing dispatch hook
  const dispatch = useDispatch();
  const { cartItems } = useSelector((state: RootState) => state.cart);
  const { purchasedTickets, user } = useSelector(
    (state: RootState) => state.userReducer
  );
  const navigate = useNavigate();

  const [isAccountLocked, setIsAccountLock] = useState(false);



  useEffect(() => {
    if (user && parseInt(user.lock_account)) {
      setIsAccountLock(true);

    }
  }, [user]);

  //* quantity changer function callback
  const handleQuantityChangeWrapper = (
    id: number,
    newQuantity: number,
    action: "increment" | "decrement"
  ) => {
    if (handleQuantityChange) {
      handleQuantityChange(id, newQuantity, action);
    }
  };

  //todo select recommended competitions from redux store
  const recommendComps = useSelector(
    (state: RootState) => state.competition.recommendComps
  );

  //? import query func for fetching recommended competition
  const [fetchRecommendedComps] = useGetRecommendedCompsMutation();

  const isEnabledQuestion = Number(competition.comp_question) ? true : false;

  //* handling modal open func
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

  const disbaleTickets = parseInt(competition.disable_tickets) ? true : false;
  const hideTicketCount = parseInt(competition.hide_ticket_count)
    ? true
    : false;
  const hideTimer = parseInt(competition.hide_timer) ? true : false;


  const [activeColors, setActiveColors] = useState<{ [key: string | number]: boolean }>({});

  const getColor = useCallback((id: string | number) => {
    return activeColors[id] ? '#2CB4A5' : '#2CB4A5';
  }, [activeColors]);


  const handleButtonPress = (id: number, isActive: boolean) => {
    setActiveColors(prev => ({ ...prev, [id]: isActive }));
  };

  const handleSliderChange = (id: number, newValue: number | number[]) => {
    if (typeof newValue === 'number') {
      const currentQuantity = quantities[id] || 0;
      const difference = newValue - currentQuantity;

      if (difference !== 0) {
        const action = difference > 0 ? "increment" : "decrement";
        handleQuantityChangeWrapper(id, Math.abs(difference), action);
      }
    }
    setActiveColors(prev => ({ ...prev, [id]: true }));
  };

  const handleSliderChangeCommitted = (id: any) => {
    setActiveColors(prev => ({ ...prev, [id]: false }));
  };

  const nowTime = new Date();
  const drawDateTime = new Date(`${competition.draw_date}T${competition.draw_time}`);

  return (
    <div
      className="competion-box-part"

    >
      <div className="competion-box-part-left"
        onClick={() =>
          navigateCompetition(
            competition.id,
            competition.category,
            competition.title,
            navigate
          )
        }
      >
        <img
          //src={competition.image}
          src={competition.images_thumb_cat && competition.images_thumb_cat != null ? "https://cggprelive.co.uk/competition/wp-content/uploads/thumbs/home" + competition.images_thumb_cat : competition.image}

          alt="" />
        {competition.promotional_messages && <div className="bottom-tag-all-comp bottom-tag-new">
          <h4>{competition.promotional_messages}</h4>
        </div>}
      </div>
      <div className="competion-box-part-right">
        <div className="comp-text-area">
          {!hideTimer ? (
            <div className="comp-one">
              <div className="draw-btn">
                <CountdownTimer
                  drawDate={competition.draw_date}
                  drawTime={competition.draw_time}
                />
              </div>
              <div
                className={
                  isDrawToday(competition.draw_date) ||
                    isDrawTomorrow(competition.draw_date) ||
                    new Date(competition.draw_date) < new Date()
                    ? "comp-onse"
                    : "comp-ones"
                }
              >
                <div className="comp-clock">
                  <svg
                    width={12}
                    height={12}
                    viewBox="0 0 12 12"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M6.5 5.5V3C6.5 2.86739 6.44732 2.74021 6.35355 2.64645C6.25979 2.55268 6.13261 2.5 6 2.5C5.86739 2.5 5.74021 2.55268 5.64645 2.64645C5.55268 2.74021 5.5 2.86739 5.5 3V6C5.5 6.13261 5.55268 6.25979 5.64645 6.35355C5.74021 6.44732 5.86739 6.5 6 6.5H8.5C8.63261 6.5 8.75979 6.44732 8.85355 6.35355C8.94732 6.25979 9 6.13261 9 6C9 5.86739 8.94732 5.74021 8.85355 5.64645C8.75979 5.55268 8.63261 5.5 8.5 5.5H6.5ZM6 11C3.2385 11 1 8.7615 1 6C1 3.2385 3.2385 1 6 1C8.7615 1 11 3.2385 11 6C11 8.7615 8.7615 11 6 11Z"
                      fill={
                        isDrawToday(competition.draw_date) ||
                          isDrawTomorrow(competition.draw_date) ||
                          new Date(competition.draw_date) < new Date()
                          ? "#fff"
                          : "#2CB4A5"
                      }
                    />
                  </svg>

                  <div className="comp-clock-txt">
                    <p>
                      {isDrawTomorrow(competition.draw_date) ? (
                        <span>Draws tomorrow</span>
                      ) : isDrawToday(competition.draw_date) ? (
                        <span>Draws today</span>
                      ) : new Date(competition.draw_date) < new Date() ? (
                        <span>Closed</span>
                      ) : (
                        <>
                          Draw:{" "}
                          <span>
                            {(() => {
                              const drawDate = new Date(competition.draw_date);
                              const day = drawDate.getDate();
                              const suffix = (day: number) => {
                                if (day >= 11 && day <= 13) return "th";
                                switch (day % 10) {
                                  case 1:
                                    return "st";
                                  case 2:
                                    return "nd";
                                  case 3:
                                    return "rd";
                                  default:
                                    return "th";
                                }
                              };
                              const formattedDate =
                                drawDate.toLocaleDateString("en-GB", {
                                  weekday: "short",
                                }) +
                                ` ${day}<sup>${suffix(day)}</sup> ` +
                                drawDate.toLocaleDateString("en-GB", {
                                  month: "short",
                                });
                              return (
                                <span
                                  dangerouslySetInnerHTML={{
                                    __html: formattedDate,
                                  }}
                                />
                              );
                            })()}
                          </span>
                        </>
                      )}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          ) : (
            <div className="comp-one" style={{ height: "65px" }}></div>
          )}
          <div className="comp-main-txt cursor-pointer" onClick={() => {
            navigateCompetition(
              competition.id,
              competition.category,
              competition.title,
              navigate
            );
          }}>
            <h2>{truncateText(competition.title, 142)}</h2>
            <h4>
              {
                competition.sale_price && competition.sale_price > 0 && new Date(competition.sale_end_date) >= new Date() && new Date(competition.sale_start_date) <= new Date() ?
                  <h4>
                    <span className="strikethrough-text">{`£${competition.price_per_ticket}`} </span>{""}
                    {`£${competition.sale_price}`}  <span>PER ENTRY</span>
                  </h4>
                  :
                  <h4>
                    {`£${competition.price_per_ticket}`} <span>PER ENTRY</span>
                  </h4>

              }

              {/* £{competition.price_per_ticket} <span>PER ENTRY</span> */}
            </h4>
          </div>
          <div className={`comp-progress-all  ${disbaleTickets ? "change-opacity" : ""}   ${drawDateTime < nowTime || competition?.category == 'finished_and_sold_out' || (competition?.total_sell_tickets == competition?.total_ticket_sold) ? "change-opacity" : ""}`}>
            <div className="progs-26">
              <div
                className="progs-26-shade"
                style={{
                  width: `${calculatePercentage(
                    Number(competition.total_ticket_sold),
                    Number(competition.total_sell_tickets)
                  )}%`,
                }}
              ></div>

              {!hideTicketCount ?

                <h5>
                  <div className="progs-lef">
                    <svg
                      width="12"
                      height="8"
                      viewBox="0 0 12 8"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M12 2.94024V1.52288C12 0.963074 11.5462 0.509277 10.9865 0.509277H1.01348C0.453773 0.509254 0 0.963051 0 1.52286V2.94968C0.514617 3.02361 0.910641 3.46503 0.910641 4.00013C0.910641 4.53523 0.514617 4.9767 0 5.05043V6.47745C0 7.03707 0.453773 7.49086 1.01348 7.49086H10.9865C11.5462 7.49086 12 7.03707 12 6.47745V5.05988C11.4369 5.03354 10.9881 4.56987 10.9881 4.00015C10.9881 3.43046 11.4369 2.96679 12 2.94024ZM2.84801 6.85137H2.49933V5.94195H2.84801V6.85137ZM2.84801 5.25361H2.49933V4.34421H2.84801V5.25361ZM2.84801 3.6559H2.49933V2.74629H2.84801V3.6559ZM2.84801 2.05819H2.49933V1.14877H2.84801V2.05819Z"
                        fill="white"
                      />
                    </svg>
                    <h4>{competition.total_ticket_sold} </h4>
                  </div>
                  <div className="progs-rgt">
                    <h4>{competition.total_sell_tickets}</h4>
                  </div>
                </h5>

                : ""}
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


          {
            drawDateTime < nowTime || competition?.category == 'finished_and_sold_out' || (competition?.total_sell_tickets == competition?.total_ticket_sold) ? (
              <div className={`finish-btns ${(competition?.total_sell_tickets == competition?.total_ticket_sold) || (new Date(competition.draw_date) > new Date()) ? "change-opacity" : "change-opacity"}`}>
                <div className={`comp-btnss ${disbaleTickets ? "change-opacity" : ""}`}  >

                  <div className="comp-button-all-new">
                    <div className="comp-btn-lefts-card">

                      <div className="input-button-slider-button-div">

                        <div className="input-show-quantity">

                          <span className="tickets-number">
                            {quantities[competition.id]} {quantities[competition.id] > 1 ? ` Tickets` : ` Ticket`}
                          </span>

                        </div>

                        <div className="button-slider-button">

                          <button
                            className="value-button"
                            id="decreases"
                            onClick={(e) => {
                              e.stopPropagation();
                              e.preventDefault();
                              handleQuantityChangeWrapper(
                                competition.id,
                                1,
                                "decrement"
                              );
                            }}

                            style={{ backgroundColor: getColor(competition.id) }}
                            onMouseDown={() => handleButtonPress(competition.id, true)}
                            onMouseUp={() => handleButtonPress(competition.id, false)}
                            onTouchStart={() => handleButtonPress(competition.id, true)}
                            onTouchEnd={() => handleButtonPress(competition.id, false)}

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


                          <div className="swiper-no-swiping">
                            <Slider
                              defaultValue={quantities[competition.id]}
                              aria-label="Default"
                              valueLabelDisplay="auto"
                              min={1}
                              max={Number(competition.max_ticket_per_user)}
                              value={Number(quantities[competition.id]) || 1}

                              onChange={(_, newValue) => handleSliderChange(competition.id, newValue)}

                              onChangeCommitted={() => handleSliderChangeCommitted(competition.id)}


                              sx={{
                                color: `${getColor(competition.id)} !important`,
                                '& .MuiSlider-thumb': {
                                  backgroundColor: `${getColor(competition.id)} !important`,
                                },
                                '& .MuiSlider-track': {
                                  backgroundColor: `${getColor(competition.id)} !important`,
                                },
                              }}

                            />
                          </div>


                          <button
                            className="value-button"
                            id="increases"
                            onClick={(e) => {
                              e.stopPropagation();
                              e.preventDefault();
                              handleQuantityChangeWrapper(
                                competition.id,
                                1,
                                "increment"
                              );
                            }}

                            style={{ backgroundColor: getColor(competition.id) }}
                            onMouseDown={() => handleButtonPress(competition.id, true)}
                            onMouseUp={() => handleButtonPress(competition.id, false)}
                            onTouchStart={() => handleButtonPress(competition.id, true)}
                            onTouchEnd={() => handleButtonPress(competition.id, false)}

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

                        </div>

                      </div>
                    </div>
                    {/* <div className="comp-btn-rights-card">
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
                            className="enter-btn-comp"
                            data-bs-toggle="modal"
                            // data-bs-target={
                            //   isEnabledQuestion ? "#enter" : "#exampleModal-3"
                            // }
                            onClick={(e) => {
                              e.stopPropagation();
                              const updatedQty = quantities[competition.id];
                              const updatedComp = { ...competition };
                              updatedComp.quantity = updatedQty.toString();
                              dispatch(setCurrentCompetition(updatedComp));
                              handleModalOpen(competition);
                              // if (!isEnabledQuestion) {
                              handleAddToCart(
                                competition,
                                cartItems,
                                dispatch,
                                quantities,
                                cartKeys,
                                purchasedTickets,
                                isEnabledQuestion
                              );
                              // }
                            }}
                            disabled={disbaleTickets}
                          >
                            ENTER
                          </button>}
                    </div> */}
                  </div>
                </div>
                <button type="button" className="closse-btn">
                  {(competition?.total_sell_tickets == competition?.total_ticket_sold) && (drawDateTime > nowTime) ? 'SOLD OUT' : 'CLOSED'}
                </button>
              </div>

            ) : (
              <div className={`comp-btnss ${disbaleTickets ? "change-opacity" : ""}`}  >

                <div className="comp-button-all-new">
                  <div className="comp-btn-lefts-card">

                    <div className="input-button-slider-button-div">

                      <div className="input-show-quantity">

                        <span className="tickets-number">
                          {quantities[competition.id]} {quantities[competition.id] > 1 ? ` Tickets` : ` Ticket`}
                        </span>

                      </div>

                      <div className="button-slider-button">

                        <button
                          className="value-button"
                          id="decreases"
                          onClick={(e) => {
                            e.stopPropagation();
                            e.preventDefault();
                            handleQuantityChangeWrapper(
                              competition.id,
                              1,
                              "decrement"
                            );
                          }}

                          style={{ backgroundColor: getColor(competition.id) }}
                          onMouseDown={() => handleButtonPress(competition.id, true)}
                          onMouseUp={() => handleButtonPress(competition.id, false)}
                          onTouchStart={() => handleButtonPress(competition.id, true)}
                          onTouchEnd={() => handleButtonPress(competition.id, false)}

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


                        <div className="swiper-no-swiping">
                          <Slider
                            defaultValue={quantities[competition.id]}
                            aria-label="Default"
                            valueLabelDisplay="auto"
                            min={1}
                            max={Number(competition.max_ticket_per_user)}
                            value={Number(quantities[competition.id]) || 1}

                            onChange={(_, newValue) => handleSliderChange(competition.id, newValue)}

                            onChangeCommitted={() => handleSliderChangeCommitted(competition.id)}


                            sx={{
                              color: `${getColor(competition.id)} !important`,
                              '& .MuiSlider-thumb': {
                                backgroundColor: `${getColor(competition.id)} !important`,
                              },
                              '& .MuiSlider-track': {
                                backgroundColor: `${getColor(competition.id)} !important`,
                              },
                            }}

                          />
                        </div>


                        <button
                          className="value-button"
                          id="increases"
                          onClick={(e) => {
                            e.stopPropagation();
                            e.preventDefault();
                            handleQuantityChangeWrapper(
                              competition.id,
                              1,
                              "increment"
                            );
                          }}

                          style={{ backgroundColor: getColor(competition.id) }}
                          onMouseDown={() => handleButtonPress(competition.id, true)}
                          onMouseUp={() => handleButtonPress(competition.id, false)}
                          onTouchStart={() => handleButtonPress(competition.id, true)}
                          onTouchEnd={() => handleButtonPress(competition.id, false)}

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

                      </div>

                    </div>
                  </div>
                  <div className="comp-btn-rights-card">
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
                          className="enter-btn-comp"
                          data-bs-toggle="modal"
                          // data-bs-target={
                          //   isEnabledQuestion ? "#enter" : "#exampleModal-3"
                          // }
                          onClick={(e) => {
                            e.stopPropagation();
                            const updatedQty = quantities[competition.id];
                            const updatedComp = { ...competition };
                            updatedComp.quantity = updatedQty.toString();
                            dispatch(setCurrentCompetition(updatedComp));
                            handleModalOpen(competition);
                            // if (!isEnabledQuestion) {
                            handleAddToCart(
                              competition,
                              cartItems,
                              dispatch,
                              quantities,
                              cartKeys,
                              purchasedTickets,
                              isEnabledQuestion
                            );
                            // }
                          }}
                          disabled={disbaleTickets}
                        >
                          ENTER
                        </button>}
                  </div>
                </div>
              </div>

            )}

          {/* {new Date(competition.draw_date) < new Date() || competition?.category == 'finished_and_sold_out' || (competition?.total_sell_tickets == competition?.total_ticket_sold) ? (
            <div className="finish-btns">
              <button type="button" className="closse-btn">
                {competition?.total_sell_tickets == competition?.total_ticket_sold && new Date(competition.draw_date) > new Date() ? 'SOLD OUT' : 'CLOSED'}
              </button>
            </div>
          ) :
            <div className={`comp-btnss ${disbaleTickets ? "change-opacity" : ""}`}  >

              <div className="comp-button-all-new">
                <div className="comp-btn-lefts-card">
                  
                  <div className="input-button-slider-button-div">

                    <div className="input-show-quantity">
                     
                      <span className="tickets-number">
                        {quantities[competition.id]} {quantities[competition.id] > 1 ? ` Tickets` : ` Ticket`}
                      </span>

                    </div>

                    <div className="button-slider-button">

                      <button
                        className="value-button"
                        id="decreases"
                        onClick={(e) => {
                          e.stopPropagation();
                          e.preventDefault();
                          handleQuantityChangeWrapper(
                            competition.id,
                            1,
                            "decrement"
                          );
                        }}

                        style={{ backgroundColor: getColor(competition.id) }}
                        onMouseDown={() => handleButtonPress(competition.id, true)}
                        onMouseUp={() => handleButtonPress(competition.id, false)}
                        onTouchStart={() => handleButtonPress(competition.id, true)}
                        onTouchEnd={() => handleButtonPress(competition.id, false)}

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


                      <div className="swiper-no-swiping">
                        <Slider
                          defaultValue={quantities[competition.id]}
                          aria-label="Default"
                          valueLabelDisplay="auto"
                          min={1}
                          max={Number(competition.max_ticket_per_user)}
                          value={Number(quantities[competition.id]) || 1}

                          onChange={(_, newValue) => handleSliderChange(competition.id, newValue)}

                          onChangeCommitted={() => handleSliderChangeCommitted(competition.id)}


                          sx={{
                            color: `${getColor(competition.id)} !important`,
                            '& .MuiSlider-thumb': {
                              backgroundColor: `${getColor(competition.id)} !important`,
                            },
                            '& .MuiSlider-track': {
                              backgroundColor: `${getColor(competition.id)} !important`,
                            },
                          }}

                        />
                      </div>


                      <button
                        className="value-button"
                        id="increases"
                        onClick={(e) => {
                          e.stopPropagation();
                          e.preventDefault();
                          handleQuantityChangeWrapper(
                            competition.id,
                            1,
                            "increment"
                          );
                        }}

                        style={{ backgroundColor: getColor(competition.id) }}
                        onMouseDown={() => handleButtonPress(competition.id, true)}
                        onMouseUp={() => handleButtonPress(competition.id, false)}
                        onTouchStart={() => handleButtonPress(competition.id, true)}
                        onTouchEnd={() => handleButtonPress(competition.id, false)}

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

                    </div>

                  </div>
                </div>
                <div className="comp-btn-rights-card">
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
                        className="enter-btn-comp"
                        data-bs-toggle="modal"
                        // data-bs-target={
                        //   isEnabledQuestion ? "#enter" : "#exampleModal-3"
                        // }
                        onClick={(e) => {
                          e.stopPropagation();
                          const updatedQty = quantities[competition.id];
                          const updatedComp = { ...competition };
                          updatedComp.quantity = updatedQty.toString();
                          dispatch(setCurrentCompetition(updatedComp));
                          handleModalOpen(competition);
                          // if (!isEnabledQuestion) {
                          handleAddToCart(
                            competition,
                            cartItems,
                            dispatch,
                            quantities,
                            cartKeys,
                            purchasedTickets,
                            isEnabledQuestion
                          );
                          // }
                        }}
                        disabled={disbaleTickets}
                      >
                        ENTER
                      </button>}
                </div>
              </div>
            </div>
          } */}
        </div>
      </div>
    </div>
  );
};

export default DesktopViewComps;
