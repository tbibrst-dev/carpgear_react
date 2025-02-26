import { useNavigate } from "react-router";
import { RootState } from "../redux/store";
import { useDispatch, useSelector } from "react-redux";
import { useCallback, useEffect, useState } from "react";
import { QunatityType } from "../types";
import { UPDATE_CART_KEY, cartError, handleAddToCart } from "../utils";
// import { toast } from "react-hot-toast";
// import { isMobile } from 'react-device-detect';
import Slider from '@mui/material/Slider';
import { showErrorToast } from '../showErrorToast';




const BasketModal = () => {
  const navigate = useNavigate();
  const { isAdding, cartItems } = useSelector((state: RootState) => state.cart);
  const dispatch = useDispatch();
  const competitionState = useSelector((state: RootState) => state.competition);
  const { recommendComps, isLoading } = competitionState;
  const [quantities, setQuantities] = useState<QunatityType>({});
  const keys = localStorage.getItem(UPDATE_CART_KEY) as string;
  const cartKeys = keys ? JSON.parse(keys) : {};
  const { purchasedTickets } = useSelector(
    (state: RootState) => state.userReducer
  );

  useEffect(() => {
    const initialQuantities: QunatityType = {};
    recommendComps.forEach((competition) => {
      initialQuantities[competition.id] = parseInt(competition.quantity)
        ? parseInt(competition.quantity)
        : 1;
      setQuantities(initialQuantities);
    });
  }, [recommendComps]);

  const handleQuantityChange = (
    id: number,
    newQuantity: number,
    action: "increment" | "decrement"
  ) => {
    const competition = recommendComps.find((comp) => comp.id === id);
    if (
      Number(competition?.max_ticket_per_user) === quantities[id] &&
      action === "increment"
    ) {
      // toast.error(cartError());
      showErrorToast(cartError());
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

  //TODO: change input value when user type
  // const handleQuantityChangeInput = (id: number, value: number) => {
  //   let parsedValue: number;

  //   if (isNaN(value)) return;
  //   //* check if user input is not more than max ticket per user
  //   const competition = recommendComps.find(
  //     (item) => item.id === id
  //   ) as CompetitionType;
  //   if (value > parseInt(competition.max_ticket_per_user)) {
  //     setQuantities((prevQuantities) => ({
  //       ...prevQuantities,
  //       [id]: parseInt(competition.max_ticket_per_user),
  //     }));
  //     return;
  //   }

  //   if (!value) {
  //     parsedValue = 0;
  //   } else {
  //     parsedValue = value;
  //   }
  //   setQuantities((prevQuantities) => ({
  //     ...prevQuantities,
  //     [id]: parsedValue,
  //   }));
  // };

  const checkModel = 1;


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
        handleQuantityChange(id, Math.abs(difference), action);
      }
    }
    setActiveColors(prev => ({ ...prev, [id]: true }));
  };

  const handleSliderChangeCommitted = (id: any) => {
    setActiveColors(prev => ({ ...prev, [id]: false }));
  };

  return (
    <div
      className="modal fade"
      id="exampleModal-3"
      tabIndex={-1}
      aria-labelledby="exampleModalLabel-3"
      aria-hidden="true"
    >
      <div className="modal-dialog ">
        <div className="modal-content">
          <div className="modal-header">
            <button
              type="button"
              className="pop-view-basket"
              onClick={() => navigate("/cart")}
              data-bs-dismiss="modal"
              aria-label="Close"
              disabled={isAdding}
            >
              {isAdding ? "Please wait..." : "View Basket"}
            </button>
            <button
              type="button"
              className="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            />
            <div className="pop-backet-title">
              <h4>You might also like</h4>
            </div>
            {isLoading ? (
              <h5 style={{ color: "white", textAlign: "center" }}>
                Please wait...
              </h5>
            ) : recommendComps.length > 0 ? (
              recommendComps.map((competition, index) => {
                const disbaleTickets = parseInt(competition.disable_tickets);
                console.log('+++++++++',disbaleTickets);
                // Skip rendering if tickets are disabled
                if (disbaleTickets) return null;
                return (
                  <div
                    key={competition.id}
                    className={`${index === 0 ? "pop-basket-items" : "pop-basket-items-one"
                      }`}
                  >
                    <div className="basket-mob-all">
                      <div className="basket-image-win">
                        <img src={competition.image} alt="competition image" />
                      </div>
                      <div className="win-confirmation-mob-all-txt">
                        <div className="basket-main-txt-win">
                          <h3>{competition.title}</h3>
                          {/* <h4>
                          £{competition.price_per_ticket}{" "}
                          <span>PER ENTRY</span>
                        </h4> */}
                          {
                            competition.sale_price && competition.sale_price > 0 && competition.price_per_ticket > competition.sale_price && new Date(competition.sale_end_date) >= new Date() && new Date(competition.sale_start_date) <= new Date() ?
                              <h4>
                                <span className="strikethrough-text">{`£${competition.price_per_ticket}`} </span>{""}
                                {`£${competition.sale_price}`}  <span>PER ENTRY</span>
                              </h4>
                              :
                              <h4>
                                {`£${competition.price_per_ticket}`} <span>PER ENTRY</span>
                              </h4>

                          }
                        </div>
                        <div className="basket-btnss-basket">
                          <div className="button-all-basket">
                            <div className="basket-btn-lefts-card-basket">
                              <div className="basket-section-left-data-btns">
                                {/* <form>
                                <div className="basket-section-left-btn-pop">
                                  <input
                                    // type="number"
                                    className="text-spce-basket"
                                    value={quantities[competition.id]}
                                    onChange={(
                                      e: React.ChangeEvent<HTMLInputElement>
                                    ) =>
                                      handleQuantityChangeInput(
                                        competition.id,
                                        Number(e.target.value)
                                      )
                                    }
                                    disabled={isAdding}
                                  />
                                  <button
                                    type="button"
                                    className="min-basket"
                                    onClick={() =>
                                      handleQuantityChange(
                                        competition.id,
                                        1,
                                        "decrement"
                                      )
                                    }
                                    disabled={isAdding}
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
                                      ></path>
                                    </svg>
                                  </button>
                                  <button
                                    type="button"
                                    className="plus-basket"
                                    onClick={() =>
                                      handleQuantityChange(
                                        competition.id,
                                        1,
                                        "increment"
                                      )
                                    }
                                    disabled={isAdding || disbaleTickets}
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
                                      ></path>
                                    </svg>
                                  </button>
                                </div>
                              </form> */}
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
                                        handleQuantityChange(
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
                                        handleQuantityChange(
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
                            </div>
                            <div className="basket-btn-rights-card-basket">
                              <button
                                type="button"
                                className="enter-btn-basket"
                                onClick={() =>
                                  handleAddToCart(
                                    competition,
                                    cartItems,
                                    dispatch,
                                    quantities,
                                    cartKeys,
                                    purchasedTickets,
                                    false,
                                    checkModel
                                  )
                                }
                                disabled={isAdding}
                              >
                                ADD
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                );

              })
            ) : (
              <h5 style={{ color: "white" }}>No recommended comps available</h5>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default BasketModal;
