import { CompetitionType, PurchasedTickets, QunatityType } from "../../types";
import {
  calculatePercentage,
  isDrawToday,
  isDrawTomorrow,
  isNewlyLaunched,
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
import {showErrorToast} from "../../showErrorToast";

import Slider from "@mui/material/Slider";

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
    isEnabledQuest: any
  ) => void;
  handleQuantityChangeInput: (id: number, value: number) => void;
  cartKeys: {};
}

const MobileViewComps: React.FC<PropsType> = ({
  competition,
  quantities,
  handleQuantityChange,
  navigateCompetition,
  handleAddToCart,
  // handleQuantityChangeInput,
  cartKeys,
}) => {
  const dispatch = useDispatch();
  const { purchasedTickets, user } = useSelector(
    (state: RootState) => state.userReducer
  );
  const navigate = useNavigate();

  const [isAccountLocked, setIsAccountLock] = useState(false);
  // const [isAccountLockedPeriod, setIsAccountLockPeriod] = useState("");

  useEffect(() => {
    if (user && parseInt(user.lock_account)) {
      setIsAccountLock(true);
      // setIsAccountLockPeriod(user.locking_period);
    }
  }, [user]);

  const { cartItems } = useSelector((state: RootState) => state.cart);
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

  const [activeColors, setActiveColors] = useState<{
    [key: string | number]: boolean;
  }>({});

  const getColor = useCallback(
    (id: string | number) => {
      return activeColors[id] ? "#2CB4A5" : "#2CB4A5";
    },
    [activeColors]
  );

  const handleButtonPress = (id: number, isActive: boolean) => {
    setActiveColors((prev) => ({ ...prev, [id]: isActive }));
  };

  const handleSliderChange = (id: number, newValue: number | number[]) => {
    if (typeof newValue === "number") {
      const currentQuantity = quantities[id] || 0;
      const difference = newValue - currentQuantity;

      if (difference !== 0) {
        const action = difference > 0 ? "increment" : "decrement";
        handleQuantityChangeWrapper(id, Math.abs(difference), action);
      }
    }
    setActiveColors((prev) => ({ ...prev, [id]: true }));
  };

  const handleSliderChangeCommitted = (id: any) => {
    setActiveColors((prev) => ({ ...prev, [id]: false }));
  };

  return (
    <div>
      {" "}
      <div className="mob-comp-parts">
        <div className="mob-comp-top-section">
          <div
            className="mob-comp-top-pic"
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
            // src={competition.image}
        src={competition.images_thumb_cat && competition.images_thumb_cat != null ? "https://cggprelive.co.uk/competition/wp-content/uploads/thumbs/home"+ competition.images_thumb_cat : competition.image  }
             
             />
          </div>
          <div className="mob-comp-top-text cursor-pointer" onClick={() => {
            navigateCompetition(
              competition.id,
              competition.category,
              competition.title,
              navigate
            );
          }}>
            <h2>{truncateText(competition.title, 90)}</h2>
            <h4>
              {competition.sale_price &&
              competition.sale_price > 0 &&
              new Date(competition.sale_end_date) >= new Date() &&
              new Date(competition.sale_start_date) <= new Date() ? (
                <h4>
                  <span className="strikethrough-text">
                    {`£${competition.price_per_ticket}`}{" "}
                  </span>
                  {""}
                  {`£${competition.sale_price}`} <span>PER ENTRY</span>
                </h4>
              ) : (
                <h4>
                  {`£${competition.price_per_ticket}`} <span>PER ENTRY</span>
                </h4>
              )}
              {/* £{competition.price_per_ticket} <span>PER ENTRY</span> */}
            </h4>
          </div>
        </div>
        <div className="comp-tag-buttons">
          <div className="comp-tag-buttons-on bottom-tag-new">
            <h4>{competition.promotional_messages}</h4>
          </div>
          {isNewlyLaunched(competition.created_at) && (
            <div className="comp-tag-buttons-jst">
              <h4>Just Launched</h4>
            </div>
          )}
        </div>
        {!hideTimer && (
          <div className="mob-comp-shw">
            <div className="draw-btn-mb">
              <CountdownTimer
                drawDate={competition.draw_date}
                drawTime={competition.draw_time}
              />
            </div>
            <div className="comp-ones">
              <div className="comps-clock">
                <svg
                  width={10}
                  height={10}
                  viewBox="0 0 10 10"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                    fill="#2CB4A5"
                  />
                </svg>

                <div className="comps-clock-txt">
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
        )}
        <div className="mob-comp-progress-all">
          <div className="mob-progs-71">
            <div
              className="mob-progs-71-shade"
              style={{
                width: `${calculatePercentage(
                  Number(competition.total_ticket_sold),
                  Number(competition.total_sell_tickets)
                )}%`,
              }}
            ></div>

            {!hideTicketCount ? (
              <h5>
                <div className="mob-progs-lef">
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
                  <h4>{competition.total_ticket_sold}</h4>
                </div>
                <div className="mob-progs-rgt">
                  <h4>{competition.total_sell_tickets}</h4>
                </div>
              </h5>
            ) : (
              ""
            )}
          </div>
          <div className="mob-progs-per">
            <h4>
              {calculatePercentage(
                Number(competition.total_ticket_sold),
                Number(competition.total_sell_tickets)
              )}
              %
            </h4>
          </div>
        </div>

        {new Date(competition.draw_date) < new Date() ||
        competition?.category == "finished_and_sold_out" ||
        competition?.total_sell_tickets == competition?.total_ticket_sold ? (
          <div className="finish-btns">
            <button type="button" className="closse-btn">
              {competition?.total_sell_tickets ==
                competition?.total_ticket_sold &&
              new Date(competition.draw_date) > new Date()
                ? "SOLD OUT"
                : "CLOSED"}
            </button>
          </div>
        ) : (
          <div
            className={`mob-comp-btnss ${
              disbaleTickets ? "change-opacity" : ""
            }`}
          >
            <div className="mob-comp-button-all-new">
              <div className="mob-comp-btn-lefts-card">
                {/* <div className="increase-quantity">
                  <form>
                    <input
                      id="number"
                      value={quantities[competition.id]}
                      onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                        handleQuantityChangeInput(
                          competition.id,
                          Number(e.target.value)
                        )
                      }
                      className="mob-comps"
                      onClick={(e) => e.stopPropagation()}
                    />
                    <button
                      className="value-button"
                      id="decrease-mob-comps"
                      onClick={(e) => {
                        e.stopPropagation();
                        e.preventDefault();
                        handleQuantityChangeWrapper(
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
                      id="increase-mob-comps"
                      onClick={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        handleQuantityChangeWrapper(
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
                </div> */}
                <div className="input-button-slider-button-div">
                  <div className="input-show-quantity">
                    {/* <input
                      // type="number"
                      id="number"
                      value={quantities[competition.id]}
                      className="num"
                      onChange={(
                        e: React.ChangeEvent<HTMLInputElement>
                      ) =>
                        handleQuantityChangeInput &&
                        handleQuantityChangeInput(
                          competition.id,
                          Number(e.target.value)
                        )
                      }
                      onClick={(e) => e.stopPropagation()}
                      disabled
                    /> */}
                    <span className="tickets-number">
                      {quantities[competition.id]}{" "}
                      {quantities[competition.id] > 1 ? ` Tickets` : ` Ticket`}
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
                      onMouseDown={() =>
                        handleButtonPress(competition.id, true)
                      }
                      onMouseUp={() => handleButtonPress(competition.id, false)}
                      onTouchStart={() =>
                        handleButtonPress(competition.id, true)
                      }
                      onTouchEnd={() =>
                        handleButtonPress(competition.id, false)
                      }
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
                        onChange={(_, newValue) =>
                          handleSliderChange(competition.id, newValue)
                        }
                        onChangeCommitted={() =>
                          handleSliderChangeCommitted(competition.id)
                        }
                        sx={{
                          color: `${getColor(competition.id)} !important`,
                          "& .MuiSlider-thumb": {
                            backgroundColor: `${getColor(
                              competition.id
                            )} !important`,
                          },
                          "& .MuiSlider-track": {
                            backgroundColor: `${getColor(
                              competition.id
                            )} !important`,
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
                      onMouseDown={() =>
                        handleButtonPress(competition.id, true)
                      }
                      onMouseUp={() => handleButtonPress(competition.id, false)}
                      onTouchStart={() =>
                        handleButtonPress(competition.id, true)
                      }
                      onTouchEnd={() =>
                        handleButtonPress(competition.id, false)
                      }
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
              <div className="mob-comp-btn-rights-card">
                {isAccountLocked ? (
                  <button
                    type="button"
                    className="enter-btn-mob-comps"
                    onClick={(e) => {
                      e.stopPropagation();
                      showErrorToast(`Your account has been locked!`);
                    }}
                  >
                    ADD
                  </button>
                ) : (
                  <button
                    type="button"
                    className="enter-btn-mob-comps"
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
                    ADD
                  </button>
                )}
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default MobileViewComps;
