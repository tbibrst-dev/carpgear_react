import React, { useCallback, useEffect, useState } from "react";
import { CompetitionType, PurchasedTickets } from "../../types";
import CountdownTimer from "../../common/Countdown";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import "swiper/css/scrollbar";
import { Scrollbar, Navigation } from "swiper/modules";
import { Link, NavigateFunction, useNavigate } from "react-router-dom";
import { truncateText } from "../../utils";
import { useDispatch, useSelector } from "react-redux";
import {
  setCurrentCompetition,
  setFetching,
  setRecommendedComps,
} from "../../redux/slices";
import { useGetRecommendedCompsMutation } from "../../redux/queries";
import { RootState } from "../../redux/store";
import { Dispatch } from "@reduxjs/toolkit";
// import toast from "react-hot-toast";
import { isMobile } from 'react-device-detect';
import Slider from '@mui/material/Slider';
import { showErrorToast } from '../../showErrorToast';




interface QunatityType {
  [key: number]: number;
}

interface PropTypes {
  competitions: CompetitionType[];
  quantities: QunatityType;
  scrollbarId: string;
  handleQuantityChange?: (
    id: number,
    newQuantity: number,
    action: "increment" | "decrement"
  ) => void;
  handleQuantitiyChangeInput?: (id: number, value: number) => void;
  navigateCompetition: (
    id: number,
    category: string,
    competitionName: string,
    navigate: NavigateFunction
  ) => void;
  handleAddToCart: (
    competiton: CompetitionType,
    cartItems: CompetitionType[],
    dispatch: Dispatch,
    quantities: QunatityType,
    cartKeys: { [key: number]: { key: string } },
    purchasedTickets: PurchasedTickets[],
    isEnabledQuestion: any
  ) => void;
  category: string;
  cartKeys: { [key: number]: { key: string } };
  onSwiperInit: any;
}

const SwiperSlideComponent: React.FC<PropTypes> = ({
  competitions,
  quantities,
  scrollbarId,
  handleQuantityChange,
  category,
  navigateCompetition,
  // handleQuantitiyChangeInput,
  handleAddToCart,
  cartKeys,
  onSwiperInit
}) => {

  const cartItems = useSelector((state: RootState) => state.cart.cartItems);
  const dispatch = useDispatch();
  const [fetchRecommendedComps] = useGetRecommendedCompsMutation();
  const recommendComps = useSelector(
    (state: RootState) => state.competition.recommendComps
  );
  const { purchasedTickets, user } = useSelector(
    (state: RootState) => state.userReducer
  );
  const navigate = useNavigate();

  const [isAccountLock, setIsAccountLock] = useState(false);
  // const [isAccountLockedPeriod, setIsAccountLockPeriod] = useState("");




  const handleQuantityChangeWrapper = (
    id: number,
    newQuantity: number,
    action: "increment" | "decrement"
  ) => {
    if (handleQuantityChange) {
      handleQuantityChange(id, newQuantity, action);
    }
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

  useEffect(() => {
    if (user && parseInt(user.lock_account)) {
      setIsAccountLock(true);


    }
  }, [user]);



  // const [activeColors, setActiveColors] = useState<{[key: string | number]: boolean}>({});

  const [activeColors, setActiveColors] = useState<{ [key: number]: { increment: boolean, decrement: boolean, slider: boolean } }>({});


  // Get the active color based on the button or slider
  const getColor = useCallback((id: number, type: 'increment' | 'decrement' | 'slider') => {
    return activeColors[id]?.[type] ? '#2CB4A5' : '#2CB4A5';
  }, [activeColors]);


  // Handle button press for either increment or decrement
  const handleButtonPress = (id: number, type: 'increment' | 'decrement', isActive: boolean) => {
    setActiveColors(prev => ({
      ...prev,
      [id]: {
        ...prev[id],
        [type]: isActive,
        slider: isActive // Update slider color with button press as well
      }
    }));
  };

  // Handle slider change
  const handleSliderChange = (id: number, newValue: number | number[]) => {
    console.log(newValue);
    if (typeof newValue === 'number') {
      const currentQuantity = quantities[id] || 0;
      const difference = newValue - currentQuantity;

      if (difference !== 0) {
        const action = difference > 0 ? "increment" : "decrement";
        handleQuantityChangeWrapper(id, Math.abs(difference), action);
      }
    }
    setActiveColors(prev => ({
      ...prev,
      [id]: {
        ...prev[id],
        increment: true, // Update increment button color
        decrement: true, // Update decrement button color
        slider: true,    // Update slider color
      }
    }));
  };

  // Handle slider change committed
  const handleSliderChangeCommitted = (id: number) => {
    setActiveColors(prev => ({
      ...prev,
      [id]: {
        ...prev[id],
        increment: false, // Reset increment button color after sliding
        decrement: false, // Reset decrement button color after sliding
        slider: false,    // Reset slider color after sliding
      }
    }));
  };


  console.log('competitions', competitions);

  return (

    <div className="instant-slider">
      <div className="swiper inst-slider">

        <Swiper

          className="swiper-wrapper"
          noSwipingClass="swiper-no-swiping"

          slidesPerView={3.3}
          spaceBetween={20}
          modules={[Scrollbar, Navigation]}
          onSwiper={onSwiperInit}
          navigation={{
            nextEl: `#${scrollbarId}-next`, // Use unique ID for next button
            prevEl: `#${scrollbarId}-prev`,
          }}
          scrollbar={{ hide: true, el: `#${scrollbarId}`, draggable: true, }}
          breakpoints={{
            0: {
              slidesPerView: 1,
              spaceBetween: 10,
            },
            360: {
              slidesPerView: 1.3,
              spaceBetween: 10,
            },
            375: {
              slidesPerView: 1.3,
              spaceBetween: 10,
            },
            420: {
              slidesPerView: 1.3,
              spaceBetween: 10,
            },
            600: {
              slidesPerView: 2,
              spaceBetween: 30,
            },
            800: {
              slidesPerView: 2,
              spaceBetween: 0,
            },
            1200: {
              slidesPerView: 3.3,
              spaceBetween: 10,
            },
            1400: {
              slidesPerView: 3.3,
              spaceBetween: 10,
            },
          }}
        >
          {competitions.map((competition) => {
            // const soldPercentage = useMemo(
            // () =>

            const startDate = new Date(competition.sale_start_date + ' ' + competition.sale_start_time);
            const endDate = new Date(competition.sale_end_date + ' ' + competition.sale_end_time);

            // console.log({
            //   name: competition.title,
            //   sale_end_date: new Date(competition.sale_end_date).getTime(),
            //   sale_start_date: new Date(competition.sale_start_date).getTime(),
            //   new_date: new Date().getTime(),
            //   current_date: new Date(),
            //   simple_sales_date: competition.sale_end_date,
            //   simple_sales_date_start: competition.sale_start_date,
            //   new_sales_date_start: new Date(competition.sale_start_date),
            //   new_sales_date_end: new Date(competition.sale_end_date),
            //   new_check_end: new Date(competition.sale_end_date).setHours(23, 59, 59, 999),
            //   end_date: endDate,
            //   start_Date: startDate,

            // });

            const soldPercentage = competition.total_ticket_sold
              ? Math.floor(
                (Number(competition.total_ticket_sold) /
                  Number(competition.total_sell_tickets)) *
                100
              )
              : 0;
            //   [competition.total_ticket_sold, competition.total_sell_tickets]
            // );

            const nowTime = new Date();
            const drawDateTime = new Date(`${competition.draw_date}T${competition.draw_time}`);


            const isDrawTomorrow =
              new Date(competition.draw_date).toDateString() ===
              new Date(
                new Date().getTime() + 24 * 60 * 60 * 1000
              ).toDateString();



            // const isDrawToday = drawDateTime.toDateString() === nowTime.toDateString() && drawDateTime <= nowTime;
            const isDrawDateToday = new Date(competition.draw_date).toDateString() === new Date().toDateString();
            const isTimePassed = drawDateTime <= nowTime;
            const isDrawToday = isDrawDateToday && !isTimePassed;

            // console.log('drawDateTime++', drawDateTime);
            // console.log('nowTime++', nowTime);
            // console.log('competition++', competition.title);
            // console.log('isDrawToday++', isDrawToday);
            // console.log('isTimePassed++',isTimePassed);


            const isNewlyLaunched =
              new Date(competition.created_at) >=
              new Date(new Date().getTime() - 48 * 60 * 60 * 1000);

            const isEnabledQuestion = Number(competition.comp_question)
              ? true
              : false;



            const disbaleTickets = parseInt(competition.disable_tickets)
              ? true
              : false;
            const hideTicketCount = parseInt(competition.hide_ticket_count)
              ? true
              : false;
            const hideTimer = parseInt(competition.hide_timer) ? true : false;

            if (!isMobile && competition.via_mobile_app == 0) {
              return (
                <SwiperSlide
                  className="swiper-slide"
                  key={competition.id}

                >
                  <div className="Instant-slider-content">
                    <div className="Instant-image" onClick={() => {
                      navigateCompetition(
                        competition.id,
                        competition.category,
                        competition.title,
                        navigate
                      );
                    }}>
                      <img
                        //src={competition.image}
                        src={competition.images_thumb && competition.images_thumb != null ? "https://cggprelive.co.uk/competition/wp-content/uploads/thumbs/list" + competition.images_thumb : competition.image}
                        alt="competition image"
                        loading="lazy"
                      />
                      <div className="top-tag">
                        {isNewlyLaunched && <h4>Just Launched</h4>}
                      </div>
                      {competition.promotional_messages && <div className="bottom-tag">
                        <h4>{competition.promotional_messages}</h4>
                      </div>}

                    </div>
                    <div className={`Instant-text-area`}>

                      {!hideTimer ? (
                        <div className={`comp-one `}>
                          <div className={`draw-btn ${drawDateTime > nowTime ? "" : "change-opacity"}`}>
                            <CountdownTimer
                              drawDate={competition.draw_date}
                              drawTime={competition.draw_time}
                            />
                          </div>
                          <div
                            className={
                              isDrawToday ||
                                isDrawTomorrow ||
                                // new Date(competition.draw_date) < new Date()
                                drawDateTime < nowTime
                                ? "comp-onse"
                                : "comp-ones"
                            }
                          >
                            <div className="comps-clock">
                              <svg
                                width="10"
                                height="10"
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill={
                                    isDrawToday ||
                                      isDrawTomorrow ||
                                      // new Date(competition.draw_date) < new Date()
                                      drawDateTime < nowTime
                                      ? "#fff"
                                      : "#2CB4A5"
                                  }
                                ></path>
                              </svg>

                              <div className="comps-clock-txt">
                                <p>
                                  {isDrawTomorrow ? (
                                    <span>Draws tomorrow</span>
                                  ) : isDrawToday ? (
                                    <span>Draws today</span>
                                  ) : drawDateTime < nowTime ? (
                                    <span>Closed</span>
                                  ) : (
                                    <>
                                      Draw:{" "}
                                      <span>
                                        {(() => {
                                          const drawDate = new Date(
                                            competition.draw_date
                                          );
                                          const day = drawDate.getDate();
                                          const suffix = (day: number) => {
                                            if (day >= 11 && day <= 13)
                                              return "th";
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
                                            drawDate.toLocaleDateString(
                                              "en-GB",
                                              {
                                                weekday: "short",
                                              }
                                            ) +
                                            ` ${day}<sup>${suffix(
                                              day
                                            )}</sup> ` +
                                            drawDate.toLocaleDateString(
                                              "en-GB",
                                              {
                                                month: "short",
                                              }
                                            );
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
                        <div
                          className="comp-one"
                          style={{ height: "65px" }}
                        >

                          <div className="draw-btn hiddenTimerDiv">
                            <div className="comps-clock ">
                              <svg
                                width="10"
                                height="10"
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill={
                                    isDrawToday ||
                                      isDrawTomorrow ||
                                      // new Date(competition.draw_date) < new Date()
                                      drawDateTime < nowTime
                                      ? "#fff"
                                      : "#2CB4A5"
                                  }
                                ></path>
                              </svg>

                              <div className="comps-clock-txt">
                                <p>
                                  {isDrawTomorrow ? (
                                    <span>Draws tomorrow</span>
                                  ) : isDrawToday ? (
                                    <span>Draws today</span>
                                  ) : drawDateTime < nowTime ? (
                                    <span>Closed</span>
                                  ) : (
                                    <>
                                      Draw:{" "}
                                      <span>
                                        {(() => {
                                          const drawDate = new Date(
                                            competition.draw_date
                                          );
                                          const day = drawDate.getDate();
                                          const suffix = (day: number) => {
                                            if (day >= 11 && day <= 13)
                                              return "th";
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
                                            drawDate.toLocaleDateString(
                                              "en-GB",
                                              {
                                                weekday: "short",
                                              }
                                            ) +
                                            ` ${day}<sup>${suffix(
                                              day
                                            )}</sup> ` +
                                            drawDate.toLocaleDateString(
                                              "en-GB",
                                              {
                                                month: "short",
                                              }
                                            );
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
                      <div className="instant-main-txt cursor-pointer" onClick={() => {
                        navigateCompetition(
                          competition.id,
                          competition.category,
                          competition.title,
                          navigate
                        );
                      }}>
                        <h3>{truncateText(competition.title, 56)}</h3>

                        {/* <h4>
                              £{competition.price_per_ticket}{" "}
                              <span>PER ENTRY</span>
                            </h4> */}
                        {/* {


                          competition.sale_price && competition.sale_price > 0 && competition.sale_price < competition.price_per_ticket && new Date(competition.sale_end_date) >= new Date() && new Date(competition.sale_start_date) <= new Date() ?
                            <h4>
                              <span className="strikethrough-text">{`£${competition.price_per_ticket}`} </span>{""}
                              {`£${competition.sale_price}`}  <span>PER ENTRY</span>
                            </h4>
                            :
                            <h4>
                              {`£${competition.price_per_ticket}`} <span>PER ENTRY</span>
                            </h4>

                        } */}



                        {
                          competition.sale_price &&
                            competition.sale_price > 0 &&
                            competition.sale_price < competition.price_per_ticket &&
                            competition.sale_end_date &&
                            competition.sale_start_date &&
                            endDate.getTime() >= new Date().getTime() &&
                            startDate.getTime() <= new Date().getTime() ? (
                            <h4>
                              <span className="strikethrough-text">{`£${competition.price_per_ticket}`} </span>
                              {`£${competition.sale_price}`} <span>PER ENTRY</span>
                            </h4>
                          ) : (
                            <h4>
                              {`£${competition.price_per_ticket}`} <span>PER ENTRY</span>
                            </h4>
                          )
                        }





                      </div>

                      <div className={`main-all  ${disbaleTickets ? "change-opacity" : ""}   ${drawDateTime < nowTime || competition?.category == 'finished_and_sold_out' || (competition?.total_sell_tickets == competition?.total_ticket_sold) ? "change-opacity" : ""}`}>
                        <div className="secon">
                          <div
                            className="secon-shade"
                            style={{ width: `${soldPercentage}%` }}
                          ></div>

                          {
                            !hideTicketCount ?
                              <h5>
                                <div className="lef">
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
                                  <h4>
                                    {!competition.total_ticket_sold
                                      ? 0
                                      : competition.total_ticket_sold}
                                  </h4>
                                </div>
                                <div className="rgt">
                                  <h4>{competition.total_sell_tickets}</h4>
                                </div>
                              </h5> : ""
                          }

                        </div>
                        <div className="per">
                          <h4>{soldPercentage}%</h4>
                        </div>
                      </div>

                      {
                        drawDateTime < nowTime || competition?.category == 'finished_and_sold_out' || (competition?.total_sell_tickets == competition?.total_ticket_sold) ? (
                          <div className={`finish-btns ${(competition?.total_sell_tickets == competition?.total_ticket_sold) || (new Date(competition.draw_date) > new Date()) ? "change-opacity" : "change-opacity"}`}>

                            <div className={`banner-btnss  ${disbaleTickets ? "change-opacity" : ""}`}>
                              <div className="button-all-with-slider">
                                <div className="banner-btn-lefts-card">

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
                                        handleQuantitiyChangeInput &&
                                        handleQuantitiyChangeInput(
                                          competition.id,
                                          Number(e.target.value)
                                        )
                                      }
                                      onClick={(e) => e.stopPropagation()}
                                      disabled
                                    /> */}
                                      <span className="tickets-number">
                                        {(competition?.total_sell_tickets == competition?.total_ticket_sold) && (new Date(competition.draw_date) > new Date()) ? 'SOLD OUT' : 'CLOSED'}
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

                                        style={{ backgroundColor: getColor(competition.id, 'decrement') }}
                                        onMouseDown={() => handleButtonPress(competition.id, 'decrement', true)}
                                        onMouseUp={() => handleButtonPress(competition.id, 'decrement', false)}
                                        onTouchStart={() => handleButtonPress(competition.id, 'decrement', true)}
                                        onTouchEnd={() => handleButtonPress(competition.id, 'decrement', false)}

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
                                            color: `${getColor(competition.id, 'slider')} !important`,
                                            '& .MuiSlider-thumb': {
                                              backgroundColor: `${getColor(competition.id, 'slider')} !important`,
                                            },
                                            '& .MuiSlider-track': {
                                              backgroundColor: `${getColor(competition.id, 'slider')} !important`,
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

                                        style={{ backgroundColor: getColor(competition.id, 'increment') }}
                                        onMouseDown={() => handleButtonPress(competition.id, 'increment', true)}
                                        onMouseUp={() => handleButtonPress(competition.id, 'increment', false)}
                                        onTouchStart={() => handleButtonPress(competition.id, 'increment', true)}
                                        onTouchEnd={() => handleButtonPress(competition.id, 'increment', false)}

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



                              </div>
                            </div>

                            <button type="button" className="closse-btn">
                              {(competition?.total_sell_tickets == competition?.total_ticket_sold) && (drawDateTime > nowTime) ? 'SOLD OUT' : 'CLOSED'}
                            </button>


                          </div>
                        ) : (
                          <div className={`banner-btnss  ${disbaleTickets ? "change-opacity" : ""}`}>
                            <div className="button-all-with-slider">
                              <div className="banner-btn-lefts-card">

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
                                        handleQuantitiyChangeInput &&
                                        handleQuantitiyChangeInput(
                                          competition.id,
                                          Number(e.target.value)
                                        )
                                      }
                                      onClick={(e) => e.stopPropagation()}
                                      disabled
                                    /> */}
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

                                      style={{ backgroundColor: getColor(competition.id, 'decrement') }}
                                      onMouseDown={() => handleButtonPress(competition.id, 'decrement', true)}
                                      onMouseUp={() => handleButtonPress(competition.id, 'decrement', false)}
                                      onTouchStart={() => handleButtonPress(competition.id, 'decrement', true)}
                                      onTouchEnd={() => handleButtonPress(competition.id, 'decrement', false)}

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
                                          color: `${getColor(competition.id, 'slider')} !important`,
                                          '& .MuiSlider-thumb': {
                                            backgroundColor: `${getColor(competition.id, 'slider')} !important`,
                                          },
                                          '& .MuiSlider-track': {
                                            backgroundColor: `${getColor(competition.id, 'slider')} !important`,
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

                                      style={{ backgroundColor: getColor(competition.id, 'increment') }}
                                      onMouseDown={() => handleButtonPress(competition.id, 'increment', true)}
                                      onMouseUp={() => handleButtonPress(competition.id, 'increment', false)}
                                      onTouchStart={() => handleButtonPress(competition.id, 'increment', true)}
                                      onTouchEnd={() => handleButtonPress(competition.id, 'increment', false)}

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
                              {
                                isAccountLock ?
                                  <div className="banner-btn-rights-card">
                                    <button
                                      type="button"
                                      className="enter-btn"
                                      onClick={(e) => {
                                        e.stopPropagation();
                                        showErrorToast(`Your account has been locked!`);


                                      }}
                                    >
                                      ENTER
                                    </button>
                                  </div>
                                  :

                                  disbaleTickets ?

                                    <button type="button"
                                      className="enter-btn  "

                                      onClick={(e) => {
                                        e.stopPropagation();
                                        showErrorToast(`Tickets are temporarily disabled for this competition.`);

                                      }}
                                    >ENTER</button>

                                    :
                                    <div
                                      className="banner-btn-rights-card"

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
                                      }}
                                    >
                                      <button
                                        type="button"
                                        className="enter-btn  "
                                        // data-bs-toggle="modal"
                                        // onClick={() => {
                                        //   if (!isEnabledQuestion) {
                                        //     handleAddToCart(
                                        //       competition,
                                        //       cartItems,
                                        //       dispatch,
                                        //       quantities,
                                        //       cartKeys,
                                        //       purchasedTickets,
                                        //       isEnabledQuestion
                                        //     );
                                        //   }
                                        // }}
                                        onClick={() => {
                                          handleAddToCart(
                                            competition,
                                            cartItems,
                                            dispatch,
                                            quantities,
                                            cartKeys,
                                            purchasedTickets,
                                            isEnabledQuestion
                                          );

                                        }}
                                        disabled={disbaleTickets}
                                      >
                                        ENTER
                                      </button>
                                    </div>
                              }


                            </div>
                          </div>
                        )}
                    </div>
                  </div>
                </SwiperSlide>
              );
            }
            else if (isMobile) {
              return (
                <SwiperSlide
                  className="swiper-slide"
                  key={competition.id}
                  onClick={() => {
                    navigateCompetition(
                      competition.id,
                      competition.category,
                      competition.title,
                      navigate
                    );
                  }}
                >
                  <div className="Instant-slider-content">
                    <div className="Instant-image">
                      <img
                        src={competition.image}
                        alt="competition image"
                        loading="lazy"
                      />
                      <div className="top-tag">
                        {isNewlyLaunched && <h4>Just Launched</h4>}
                      </div>
                      {competition.promotional_messages && (
                        <div className="bottom-tag">
                          <h4>{competition.promotional_messages}</h4>
                        </div>
                      )}
                    </div>
                    <div className="Instant-text-area">
                      {!hideTimer ? (
                        <div className={`comp-one `}>
                          <div
                            className={`draw-btn ${drawDateTime > nowTime
                              ? ""
                              : "change-opacity"
                              }`}
                          >
                            <CountdownTimer
                              drawDate={competition.draw_date}
                              drawTime={competition.draw_time}
                            />
                          </div>
                          <div
                            className={
                              isDrawToday ||
                                isDrawTomorrow ||
                                drawDateTime < nowTime
                                ? "comp-onse"
                                : "comp-ones"
                            }
                          >
                            <div className="comps-clock">
                              <svg
                                width="10"
                                height="10"
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill={
                                    isDrawToday ||
                                      isDrawTomorrow ||
                                      drawDateTime < nowTime
                                      ? "#fff"
                                      : "#2CB4A5"
                                  }
                                ></path>
                              </svg>

                              <div className="comps-clock-txt">
                                <p>
                                  {isDrawTomorrow ? (
                                    <span>Draws tomorrow</span>
                                  ) : isDrawToday ? (
                                    <span>Draws today</span>
                                  ) : drawDateTime < nowTime ? (
                                    <span>Closed</span>
                                  ) : (
                                    <>
                                      Draw:{" "}
                                      <span>
                                        {(() => {
                                          const drawDate = new Date(
                                            competition.draw_date
                                          );
                                          const day = drawDate.getDate();
                                          const suffix = (day: number) => {
                                            if (day >= 11 && day <= 13)
                                              return "th";
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
                                            drawDate.toLocaleDateString(
                                              "en-GB",
                                              {
                                                weekday: "short",
                                              }
                                            ) +
                                            ` ${day}<sup>${suffix(
                                              day
                                            )}</sup> ` +
                                            drawDate.toLocaleDateString(
                                              "en-GB",
                                              {
                                                month: "short",
                                              }
                                            );
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
                        <div className="draw-btn hiddenTimerDiv">
                          <div className="comps-clock ">
                            <svg
                              width="10"
                              height="10"
                              viewBox="0 0 10 10"
                              fill="none"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path
                                d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                fill={
                                  isDrawToday ||
                                    isDrawTomorrow ||
                                    drawDateTime < nowTime
                                    ? "#fff"
                                    : "#2CB4A5"
                                }
                              ></path>
                            </svg>

                            <div className="comps-clock-txt">
                              <p>
                                {isDrawTomorrow ? (
                                  <span>Draws tomorrow</span>
                                ) : isDrawToday ? (
                                  <span>Draws today</span>
                                ) : new Date(competition.draw_date) <
                                  new Date() ? (
                                  <span>Closed</span>
                                ) : (
                                  <>
                                    Draw:{" "}
                                    <span>
                                      {(() => {
                                        const drawDate = new Date(
                                          competition.draw_date
                                        );
                                        const day = drawDate.getDate();
                                        const suffix = (day: number) => {
                                          if (day >= 11 && day <= 13)
                                            return "th";
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
                      )}
                      <div className="instant-main-txt cursor-pointer" onClick={() => {
                        navigateCompetition(
                          competition.id,
                          competition.category,
                          competition.title,
                          navigate
                        );
                      }}>
                        <h3>{truncateText(competition.title, 56)}</h3>

                        {/* <h4>
                        £{competition.price_per_ticket} <span>PER ENTRY</span>
                      </h4> */}

                        {competition.sale_price &&
                          competition.sale_price > 0 &&
                          competition.sale_price < competition.price_per_ticket &&
                          competition.sale_end_date >= new Date() ? (
                          <h4>
                            <span className="strikethrough-text">
                              {`£${competition.price_per_ticket}`}{" "}
                            </span>
                            {""}
                            {`£${competition.sale_price}`}{" "}
                            <span>PER ENTRY</span>
                          </h4>
                        ) : (
                          <h4>
                            {`£${competition.price_per_ticket}`}{" "}
                            <span>PER ENTRY</span>
                          </h4>
                        )}
                      </div>

                      <div
                        className={`main-all  ${disbaleTickets ? "change-opacity" : ""
                          }   ${drawDateTime < nowTime ||
                            competition?.category == "finished_and_sold_out" ||
                            competition?.total_sell_tickets ==
                            competition?.total_ticket_sold
                            ? "change-opacity"
                            : ""
                          }`}
                      >
                        <div className="secon">
                          <div
                            className="secon-shade"
                            style={{ width: `${soldPercentage}%` }}
                          ></div>

                          {!hideTicketCount ? (
                            <h5>
                              <div className="lef">
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
                                <h4>
                                  {!competition.total_ticket_sold
                                    ? 0
                                    : competition.total_ticket_sold}
                                </h4>
                              </div>
                              <div className="rgt">
                                <h4>{competition.total_sell_tickets}</h4>
                              </div>
                            </h5>
                          ) : (
                            ""
                          )}
                        </div>
                        <div className="per">
                          <h4>{soldPercentage}%</h4>
                        </div>
                      </div>

                      {drawDateTime < nowTime ||
                        competition?.category == "finished_and_sold_out" ||
                        competition?.total_sell_tickets ==
                        competition?.total_ticket_sold ? (
                        <div
                          className={`finish-btns ${competition?.total_sell_tickets ==
                            competition?.total_ticket_sold ||
                            drawDateTime > nowTime
                            ? "change-opacity"
                            : "change-opacity"
                            }`}
                        >
                          <div className={`banner-btnss  ${disbaleTickets ? "change-opacity" : ""}`}>
                            <div className="button-all-with-slider">
                              <div className="banner-btn-lefts-card">
                                <div className="input-button-slider-button-div">
                                  <div className="input-show-quantity">
                                    <span className="tickets-number">
                                      {competition?.total_sell_tickets ==
                                        competition?.total_ticket_sold &&
                                        drawDateTime > nowTime
                                        ? "SOLD OUT"
                                        : "CLOSED"}
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
                                      style={{
                                        backgroundColor: getColor(
                                          competition.id,
                                          "decrement"
                                        ),
                                      }}
                                      onMouseDown={() =>
                                        handleButtonPress(
                                          competition.id,
                                          "decrement",
                                          true
                                        )
                                      }
                                      onMouseUp={() =>
                                        handleButtonPress(
                                          competition.id,
                                          "decrement",
                                          false
                                        )
                                      }
                                      onTouchStart={() =>
                                        handleButtonPress(
                                          competition.id,
                                          "decrement",
                                          true
                                        )
                                      }
                                      onTouchEnd={() =>
                                        handleButtonPress(
                                          competition.id,
                                          "decrement",
                                          false
                                        )
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
                                        defaultValue={
                                          quantities[competition.id]
                                        }
                                        aria-label="Default"
                                        valueLabelDisplay="auto"
                                        min={1}
                                        max={Number(
                                          competition.max_ticket_per_user
                                        )}
                                        value={
                                          Number(quantities[competition.id]) ||
                                          1
                                        }
                                        onChange={(_, newValue) =>
                                          handleSliderChange(
                                            competition.id,
                                            newValue
                                          )
                                        }
                                        onChangeCommitted={() =>
                                          handleSliderChangeCommitted(
                                            competition.id
                                          )
                                        }
                                        sx={{
                                          color: `${getColor(
                                            competition.id,
                                            "slider"
                                          )} !important`,
                                          "& .MuiSlider-thumb": {
                                            backgroundColor: `${getColor(
                                              competition.id,
                                              "slider"
                                            )} !important`,
                                          },
                                          "& .MuiSlider-track": {
                                            backgroundColor: `${getColor(
                                              competition.id,
                                              "slider"
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
                                      style={{
                                        backgroundColor: getColor(
                                          competition.id,
                                          "increment"
                                        ),
                                      }}
                                      onMouseDown={() =>
                                        handleButtonPress(
                                          competition.id,
                                          "increment",
                                          true
                                        )
                                      }
                                      onMouseUp={() =>
                                        handleButtonPress(
                                          competition.id,
                                          "increment",
                                          false
                                        )
                                      }
                                      onTouchStart={() =>
                                        handleButtonPress(
                                          competition.id,
                                          "increment",
                                          true
                                        )
                                      }
                                      onTouchEnd={() =>
                                        handleButtonPress(
                                          competition.id,
                                          "increment",
                                          false
                                        )
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
                            </div>
                          </div>

                          <button type="button" className="closse-btn">
                            {competition?.total_sell_tickets ==
                              competition?.total_ticket_sold &&
                              drawDateTime > nowTime
                              ? "SOLD OUT"
                              : "CLOSED"}
                          </button>
                        </div>
                      ) : (
                        <div className={`banner-btnss  ${disbaleTickets ? "change-opacity" : ""}`}>
                          <div className="button-all-with-slider">
                            <div className="banner-btn-lefts-card">
                              <div className="input-button-slider-button-div">
                                <div className="input-show-quantity">
                                  <span className="tickets-number">
                                    {quantities[competition.id]}{" "}
                                    {quantities[competition.id] > 1
                                      ? ` Tickets`
                                      : ` Ticket`}
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
                                    style={{
                                      backgroundColor: getColor(
                                        competition.id,
                                        "decrement"
                                      ),
                                    }}
                                    onMouseDown={() =>
                                      handleButtonPress(
                                        competition.id,
                                        "decrement",
                                        true
                                      )
                                    }
                                    onMouseUp={() =>
                                      handleButtonPress(
                                        competition.id,
                                        "decrement",
                                        false
                                      )
                                    }
                                    onTouchStart={() =>
                                      handleButtonPress(
                                        competition.id,
                                        "decrement",
                                        true
                                      )
                                    }
                                    onTouchEnd={() =>
                                      handleButtonPress(
                                        competition.id,
                                        "decrement",
                                        false
                                      )
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
                                      max={Number(
                                        competition.max_ticket_per_user
                                      )}
                                      value={
                                        Number(quantities[competition.id]) || 1
                                      }
                                      onChange={(_, newValue) =>
                                        handleSliderChange(
                                          competition.id,
                                          newValue
                                        )
                                      }
                                      onChangeCommitted={() =>
                                        handleSliderChangeCommitted(
                                          competition.id
                                        )
                                      }
                                      sx={{
                                        color: `${getColor(
                                          competition.id,
                                          "slider"
                                        )} !important`,
                                        "& .MuiSlider-thumb": {
                                          backgroundColor: `${getColor(
                                            competition.id,
                                            "slider"
                                          )} !important`,
                                        },
                                        "& .MuiSlider-track": {
                                          backgroundColor: `${getColor(
                                            competition.id,
                                            "slider"
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
                                    style={{
                                      backgroundColor: getColor(
                                        competition.id,
                                        "increment"
                                      ),
                                    }}
                                    onMouseDown={() =>
                                      handleButtonPress(
                                        competition.id,
                                        "increment",
                                        true
                                      )
                                    }
                                    onMouseUp={() =>
                                      handleButtonPress(
                                        competition.id,
                                        "increment",
                                        false
                                      )
                                    }
                                    onTouchStart={() =>
                                      handleButtonPress(
                                        competition.id,
                                        "increment",
                                        true
                                      )
                                    }
                                    onTouchEnd={() =>
                                      handleButtonPress(
                                        competition.id,
                                        "increment",
                                        false
                                      )
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
                            {isAccountLock ? (
                              <div className="banner-btn-rights-card">
                                <button
                                  type="button"
                                  className="enter-btn"
                                  onClick={(e) => {
                                    e.stopPropagation();
                                    showErrorToast(
                                      `Your account has been locked!`
                                    );
                                  }}
                                >
                                  ENTER
                                </button>
                              </div>
                            ) : disbaleTickets ? (
                              <button
                                type="button"
                                className="enter-btn  "
                                onClick={(e) => {
                                  e.stopPropagation();
                                  showErrorToast(
                                    `Tickets are temporarily disabled for this competition.`
                                  );
                                }}
                              >
                                ENTER
                              </button>
                            ) : (
                              <div
                                className="banner-btn-rights-card"
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
                                }}
                              >
                                <button
                                  type="button"
                                  className="enter-btn  "
                                  onClick={() => {
                                    handleAddToCart(
                                      competition,
                                      cartItems,
                                      dispatch,
                                      quantities,
                                      cartKeys,
                                      purchasedTickets,
                                      isEnabledQuestion
                                    );
                                  }}
                                  disabled={disbaleTickets}
                                >
                                  ENTER
                                </button>
                              </div>
                            )}
                          </div>
                        </div>
                      )}
                    </div>
                  </div>
                </SwiperSlide>
              );
            }



          })}
        </Swiper>

        <div className="swiper-scrollbar" id={scrollbarId} />
      </div>

      <div className="instant-view-all-mob">
        <Link to={`/competitions/${category}`}>View All</Link>
      </div>
    </div>

  );
};

export default SwiperSlideComponent;
