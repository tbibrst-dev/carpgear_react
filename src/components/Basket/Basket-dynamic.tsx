import { useEffect, useState } from "react";
import {
  CompetitionType,
  PurchasedTickets,
  Question,
  QunatityType,
} from "../../types";
import {
  CART_HEADER,
  COMPS_QUEST,
  NONCE_KEY,
  NONCE_TIMESTAMP,
  UPDATE_CART_KEY,
  calculateTotalPrice,
  discountedCalculateTotalPrice,
  cartError,
  fetchNonceValue,
} from "../../utils";
import axios from "axios";
import {
  removeItemFromCart,
  setIsDeleting,
  setIsUpdating,
  updateCartQty,
} from "../../redux/slices/cartSlice";
import { useDispatch, useSelector } from "react-redux";
import { RootState } from "../../redux/store";
import { TrashIcon } from "@heroicons/react/16/solid";
import { toast } from "react-hot-toast";
import { NavigateFunction, useNavigate } from "react-router";
import { showErrorToast } from '../../showErrorToast';
import { addToCart, isAddingToCart } from "../../redux/slices/cartSlice";



type PropsTypes = {
  cartItems: CompetitionType[];
  quantities: QunatityType;
  handleQuantityChange: (
    id: number,
    newQuantity: number,
    action: "increment" | "decrement"
  ) => void;
  quantityChangeInput: (value: number, id: number) => void;
  navigateCompetition: (
    id: number,
    category: string,
    competitionName: string,
    navigate: NavigateFunction
  ) => void;
};

const BasketDynamic: React.FC<PropsTypes> = ({
  cartItems,
  quantities,
  quantityChangeInput,
  navigateCompetition,
}) => {
  //* set required hooks


  const [lineHeight] = useState<number>(() => {
    if (navigator.userAgent.includes("Mac")) {
      return 40;
    } else {
      return 32;
    }
  });

  const dispatch = useDispatch();
  const { isAdding, isUpdating, isDeleting } = useSelector(
    (state: RootState) => state.cart
  );
  const [totalPrice, setTotalPrice] = useState<number | string>(0);
  const [discountedTotalPrice, setDiscountedTotalPrice] = useState<number>(0);
  const [cartKeys, setCartKeys] = useState<{ [key: number]: { key: string } }>(
    {}
  );
  const { user, purchasedTickets } = useSelector(
    (state: RootState) => state.userReducer
  );
  const [answers, setAnswers] = useState<Question>(() =>
    JSON.parse(localStorage.getItem(COMPS_QUEST) as string)
  );
  const navigate = useNavigate();
  const [isAccountLocked, setIsAccountLocked] = useState(false);
  const [isApplyCoupon, setIsApplyCoupon] = useState(false);
  const [couponCode, setCouponCode] = useState<string>("");

  const [couponMessage, setCouponMessage] = useState('');
  const [discount, setDiscount] = useState(0);
  console.log('discount', discount);
  console.log('isApplyCoupon', isApplyCoupon);



  useEffect(() => {
    if (user && parseInt(user.lock_account)) {
      setIsAccountLocked(true);
    }
  }, [user]);

  useEffect(() => {
    const keys = localStorage.getItem(UPDATE_CART_KEY) as string;
    const parsedKeys = keys ? JSON.parse(keys) : {};
    setCartKeys(parsedKeys);

    const answers = JSON.parse(localStorage.getItem(COMPS_QUEST) as string);
    setAnswers(answers);
    fetchCartDetails();

  }, []); // Empty dependency array ensures this effect runs only once after component mount

  //! effect to calculate the cart price
  useEffect(() => {
    ;

    const cartPrice = calculateTotalPrice(cartItems);
    const cartDiscountedPrice = discountedCalculateTotalPrice(cartItems);
    setTotalPrice(cartPrice.toFixed(2));
    setDiscountedTotalPrice(cartDiscountedPrice);
  }, [cartItems]);


  const fetchCartDetails = async () => {
    console.log('+++++++++++++++++++++');
    const nonce = localStorage.getItem(NONCE_KEY);
    // const cart_header = localStorage.getItem(CART_HEADER); // only for local and stack

    dispatch(isAddingToCart(true));
    try {

      const URL = import.meta.env.VITE_GET_CART;

      // this is for local and stack
      // const res = await axios.post(
      //   URL,
      //   {
      //     nonce,
      //     cart_header,
      //   },
      //   { headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` } }
      // );

      //only for cgg live
      const res = await axios.get(
        URL,

        {
          headers: {
            "X-WC-Store-api-nonce": nonce,
            Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`,
          },
        }
      );

      if (res.status === 200) {
        const items = res.data.map((item: any) => {
          const competition = item.competition;
          competition.quantity = item.quantity.toString();
          competition.totals = item.totals;
          const competitionWithKey = { ...competition, key: item.key };
          return competitionWithKey;
        });

        const newKeys: { [key: number]: { key: string } } = {};
        items.forEach((item: any) => {
          newKeys[item.id as number] = { key: item.key };
        });
        localStorage.setItem(UPDATE_CART_KEY, JSON.stringify(newKeys));
        setCartKeys(newKeys);
        dispatch(addToCart(items));
      }
    } catch (error) {
      console.log(error);
    } finally {
      dispatch(isAddingToCart(false));
    }
  };

  const checkCartForExpiredCompetitions = (cartItems: any) => {
    const currentDate = new Date();
    let hasExpiredItems = false;

    // Check if any competition has an expired draw date
    cartItems.forEach((item: any) => {
      const drawDateTime = new Date(`${item.draw_date}T${item.draw_time}`);

      if (currentDate > drawDateTime) {
        hasExpiredItems = true;
        toast.error(`The tickets for ${item.title} in your basket are no longer available. We have removed these from your basket`);
        handleRemoveCart(item)
      }


    });

    // Return a flag indicating if there are expired items
    return hasExpiredItems;
  };

  const checkMaxticketincart = async (cartItems: any) => {
    // Check if any competition has an expired draw date
    cartItems.forEach((item: any) => {
      console.log('item.title', item.title);
      const max_ticket_per_user = Number(item.max_ticket_per_user);
      let competitionQty = Number(item.quantity);
      if (competitionQty > max_ticket_per_user) {
        updateCart(item, null);
      }
    });
  };

  //TODO: update cart function
  const updateCart = async (
    competition: CompetitionType,
    action: "increment" | "decrement" | null,
    newQty?: number
  ) => {
    if (Number(competition.quantity) === 1 && action == "decrement") return;

    const max_ticket_per_user = Number(competition.max_ticket_per_user);
    let competitionQty = Number(competition.quantity);

    const purchasedTicketsCompetition = purchasedTickets.find(
      (item) => parseInt(item.competition_id) === Number(competition.id)
    ) as PurchasedTickets;

    if (purchasedTicketsCompetition) {
      const boughtTickets = parseInt(
        purchasedTicketsCompetition?.total_tickets
      );

      const ticketsQtyLeft =
        parseInt(competition.max_ticket_per_user) - boughtTickets;

      if (ticketsQtyLeft < 0) {
        showErrorToast(
          "Oops! It seems you,ve purchased the maximum tickets for this competition"
        );
        return;
      }

      if (newQty && newQty > ticketsQtyLeft) {
        showErrorToast(
          `Oops! It seems you've already bought ${boughtTickets} tickets for this competition. You can only get ${ticketsQtyLeft} more.`
        );
        return;
      }

      if (
        (competitionQty === ticketsQtyLeft ||
          competitionQty > ticketsQtyLeft) &&
        action === "increment"
      ) {
        showErrorToast(
          `Oops! It seems you've already bought ${boughtTickets} tickets for this competition. You can only get ${ticketsQtyLeft} more.`
        );
        return;
      }
    }

    if (competitionQty === max_ticket_per_user && action === "increment") {
      showErrorToast(cartError());
      return;
    }

    if (newQty === competitionQty) {
      return;
    }

    dispatch(setIsUpdating(true));

    let nonce = localStorage.getItem(NONCE_KEY);
    const cartKey = cartKeys[competition.id];
    const cartHeader = localStorage.getItem(CART_HEADER);

    const storedTimestamp = localStorage.getItem(NONCE_TIMESTAMP) as string;

    //* chekc if nonce exist if not refetch the nonce if nonce exists check timestamp
    if (!nonce || !storedTimestamp) {
      const res: any = await fetchNonceValue();
      nonce = res.nonce;
      const timestamp = Date.now();
      localStorage.setItem(NONCE_KEY, res.nonce);
      localStorage.setItem(NONCE_TIMESTAMP, timestamp.toString());
    } else {
      const timestampDiff = Date.now() - parseInt(storedTimestamp, 10);
      if (timestampDiff > 11 * 60 * 60 * 1000) {
        const res: any = await fetchNonceValue();
        nonce = res.nonce;
        const timestamp = Date.now();
        localStorage.setItem(NONCE_KEY, res.nonce);
        localStorage.setItem(NONCE_TIMESTAMP, timestamp.toString());
      }
    }

    if (action === "increment") {
      competitionQty++;
    } else if (action === "decrement") {
      competitionQty--;
    }

    const updateURL = import.meta.env.VITE_UPDATE_CART_API;
    // const updateURL = "?rest_route=/wc/store/v1/cart/update-item";
    try {
      const res = await axios.post(
        updateURL,
        {
          key: cartKey.key,
          id: competition.competition_product_id,
          quantity: newQty ? newQty : competitionQty,
          nonce,
          cart_header: cartHeader,
        },
        {
          headers: {
            "X-WC-Store-api-nonce": nonce,  //only for cgg live
            Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`,
          },
        }
      );

      if (res.status === 200) {
        const items = res.data.items;
        const competitionToBeUpdated = items.find(
          (item: any) => item.competition.id === competition.id
        );

        // const item = res.data.items[0];
        const qty = competitionToBeUpdated.quantity;
        const totals = competitionToBeUpdated.totals;
        dispatch(updateCartQty({ id: competition.id, qty, totals }));

      }
    } catch (error) {
      console.log(error);
    } finally {
      dispatch(setIsUpdating(false));

    }
  };

  const handleRemoveCart = async (competition: CompetitionType) => {
    const cartKey = cartKeys[competition.id];
    const cart_header = localStorage.getItem(CART_HEADER);
    dispatch(setIsDeleting(true));

    let nonce = localStorage.getItem(NONCE_KEY);

    const storedTimestamp = localStorage.getItem(NONCE_TIMESTAMP) as string;

    //* chekc if nonce exist if not refetch the nonce if nonce exists check timestamp
    if (!nonce || !storedTimestamp) {
      const res: any = await fetchNonceValue();
      nonce = res.nonce;
      const timestamp = Date.now();
      localStorage.setItem(NONCE_KEY, res.nonce);
      localStorage.setItem(NONCE_TIMESTAMP, timestamp.toString());
    } else {
      const timestampDiff = Date.now() - parseInt(storedTimestamp, 10);
      if (timestampDiff > 11 * 60 * 60 * 1000) {
        const res: any = await fetchNonceValue();
        nonce = res.nonce;
        const timestamp = Date.now();
        localStorage.setItem(NONCE_KEY, res.nonce);
        localStorage.setItem(NONCE_TIMESTAMP, timestamp.toString());
      }
    }

    // const deleteURL = "?rest_route=/wc/store/v1/cart/remove-item";
    const deleteURL = import.meta.env.VITE_REMOVE_ITEM_API;

    try {
      const response = await axios.post(
        deleteURL,
        {
          key: cartKey.key,
          cart_header,
          nonce,
        },
        {
          headers: {
            "X-WC-Store-api-nonce": nonce,    //only for cgg live
            Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`,
          },
        }
      );
      if (response.status === 200) {
        dispatch(removeItemFromCart(competition.id));
        const updatedAnswers = { ...answers };
        delete updatedAnswers[competition.id];
        console.log(updatedAnswers);
        setAnswers(updatedAnswers);
        localStorage.setItem(COMPS_QUEST, JSON.stringify(updatedAnswers));
        if (cartItems.length === 0) {
          localStorage.removeItem(CART_HEADER);
        }

        // window.location.reload();
      }
    } catch (error) {
      console.log(error);
    } finally {
      dispatch(setIsDeleting(false));
    }
  };



  const handleCheckout = async () => {
    // if (user) {
    //   const totalCartPrice = cartItems.reduce(
    //     (acc, item) =>
    //       Number(item.price_per_ticket) * Number(item.quantity) + acc,
    //     0
    //   );
    //   const currentSpending = parseInt(user.current_spending);
    //   const spendingLimit = parseInt(user.limit_value);
    //   const duration = user.limit_duration;
    //   const pendingLimit = spendingLimit - currentSpending;

    //   if (pendingLimit === 0) {
    //     showErrorToast(`You have exceeded your spending limit
    //     `);
    //     return;
    //   }
    //   if (totalCartPrice > pendingLimit) {
    //     if (pendingLimit < 0) {
    //       showErrorToast(
    //         `Your current spending limit is £${spendingLimit} ${duration} and you,ve spent £${currentSpending}.`
    //       );
    //     } else {
    //       showErrorToast(
    //         `Your current spending limit is £${spendingLimit} ${duration} and you,ve spent £${currentSpending}. You can only spend £${pendingLimit} more`
    //       );
    //     }

    //     return;
    //   }
    // }

    const hasExpiredItems = checkCartForExpiredCompetitions(cartItems);
    if (hasExpiredItems) {
      // Prevent checkout or show additional UI messages if needed
      // showErrorToast("Some tickets in your cart have expired. Please review your cart.");
      return;
    }


    await checkMaxticketincart(cartItems);


    var form = document.createElement("form");

    form.method = "POST";
    form.action = import.meta.env.VITE_REDIRECT_URL;

    const nonce = localStorage.getItem(NONCE_KEY) as string;
    const cart_header = localStorage.getItem(CART_HEADER) as string;

    const params: { [key: string]: string } = {
      nonce,
      cart_header,
      token: user?.token || "",
      answers: JSON.stringify(answers),
    };

    // Add hidden input fields for each parameter
    for (var key in params) {
      if (params.hasOwnProperty(key)) {
        var input = document.createElement("input");
        input.type = "hidden";
        input.name = key;
        input.value = params[key];
        form.appendChild(input);
      }
    }

    // Append the form to the body and submit it
    document.body.appendChild(form);
    form.submit();
  };

  if (isAdding && cartItems.length === 0) {
    return (
      <div className="cart-loading">
        <h1>Please wait...</h1>
      </div>
    );
  }



  const handleApplyCoupon = async (cartItems: any) => {
    setIsApplyCoupon(true);
    if (!couponCode) {
      setIsApplyCoupon(false);
      setCouponMessage(`<span class="text-danger">Please enter a coupon code.</span>`);
      return;
    }
    const updateURL = "?rest_route=/wc/store/v1/cart/apply-coupon";
    let nonce = localStorage.getItem(NONCE_KEY);

    const storedTimestamp = localStorage.getItem(NONCE_TIMESTAMP) as string;
    //* chekc if nonce exist if not refetch the nonce if nonce exists check timestamp
    if (!nonce || !storedTimestamp) {
      const res: any = await fetchNonceValue();
      nonce = res.nonce;
      const timestamp = Date.now();
      localStorage.setItem(NONCE_KEY, res.nonce);
      localStorage.setItem(NONCE_TIMESTAMP, timestamp.toString());
    } else {
      const timestampDiff = Date.now() - parseInt(storedTimestamp, 10);
      if (timestampDiff > 11 * 60 * 60 * 1000) {
        const res: any = await fetchNonceValue();
        nonce = res.nonce;
        const timestamp = Date.now();
        localStorage.setItem(NONCE_KEY, res.nonce);
        localStorage.setItem(NONCE_TIMESTAMP, timestamp.toString());
      }
    }

    try {
      const response: any = await axios.post(
        updateURL,
        {
          code: couponCode,
        },
        {
          headers: {
            "X-WC-Store-api-nonce": nonce,  //only for cgg live
            Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`,
          },
        }
      );
      if (response && response.data && response.data.coupons) {
        setIsApplyCoupon(false);
        setDiscount(Number(response.data.coupons[0].totals.total_discount));
        setCouponMessage(`<span class="text-success">Coupon ${couponCode} applied!</span>`);
        const cartDiscountedPrice = discountedCalculateTotalPrice(cartItems);
        setDiscountedTotalPrice(cartDiscountedPrice);
        setDiscountedTotalPrice(Number(cartDiscountedPrice) - (Number(response.data.coupons[0].totals.total_discount) / 100));


      } else {
        setCouponMessage(`<span class="text-danger">Coupon ${couponCode} is no longer valid</span>`);
        setIsApplyCoupon(false);

      }
    } catch (error) {
      console.log(error);
      setCouponMessage(`<span class="text-danger">Coupon ${couponCode} is no longer valid</span>`);
      setIsApplyCoupon(false);

    }
  };


  return (
    <div>
      {/* {isUpdating || isDeleting || isApplyCoupon ? (
        <div className="basket-loader-container">
          <svg viewBox="25 25 50 50" className="loader-svg">
            <circle r={20} cy={50} cx={50} className="loader" />
          </svg>
        </div>
      ) : null} */}
      <div
        className={`basket-section ${(isUpdating || isDeleting) && "basket-section-opac"
          }`}
      >
        <div className="container">
          {cartItems.length > 0 ? (
            <div className="basket-section-all">
              <div className="basket-section-left">
                {cartItems.map((item, index) => {

                  let totalPrice = 0;
                  let strikePrice = 0;
                  let isSaleActive = false;
                  // Number(item.price_per_ticket) * Number(item.quantity);

                  const startDate = new Date(item.sale_start_date + ' ' + item.sale_start_time);
                  const endDate = new Date(item.sale_end_date + ' ' + item.sale_end_time);
                  if (


                    // item.sale_price &&
                    // item.sale_price > 0 &&
                    // item.sale_price < item.price_per_ticket &&
                    // new Date(item.sale_start_date) <= new Date() &&
                    // new Date(item.sale_end_date) >= new Date()

                            item.sale_price &&
                            item.sale_price > 0 &&
                            item.sale_price < item.price_per_ticket &&
                            item.sale_end_date &&
                            item.sale_start_date &&
                            endDate.getTime() >= new Date().getTime() &&
                            startDate.getTime() <= new Date().getTime()


                  ) {
                    isSaleActive = true;
                    totalPrice = Number(item.price_per_ticket) * Number(item.quantity);
                    strikePrice = Number(item.sale_price) * Number(item.quantity);
                  } else {
                    totalPrice = Number(item.price_per_ticket) * Number(item.quantity);
                  }
                  return (
                    <div
                      className={`${index === 0
                        ? "basket-section-left-data"
                        : "basket-section-left-data-one"
                        }`}
                      key={item.id}
                    >
                      <div
                        className="basket-section-left-data-pic"
                        onClick={() =>
                          navigateCompetition(item.id, item.category, item.title, navigate)
                        }
                      >
                        <img src={item.image} alt="cart item image" />
                      </div>
                      <div className="basket-section-left-data-txt">
                        {
                          item && Number(item.total_ticket_sold) > 0 ?
                            <div className="ticketSoldDiv">
                              <span className="ticketSoldSpan">{item.total_ticket_sold}+ shoppers have bought this</span>
                            </div>
                            : <div className="ticketSoldDivv">
                              <span className="ticketSoldSpan"></span>
                            </div>
                        }


                        <h2
                          onClick={() =>
                            navigateCompetition(item.id, item.category, item.title, navigate)
                          }
                        >
                          {item.title}
                        </h2>
                        <p>
                          {isSaleActive ? (
                            <>
                              <span className="cart-strike-price" style={{ textDecoration: "line-through", color: "white" }}>
                              £{totalPrice.toFixed(2)}

                              </span>{" "}
                              <span>
                              £{strikePrice.toFixed(2)}

                                </span> Total
                                
                            </>
                          ) : (
                            <><span>£{totalPrice.toFixed(2)}</span> Total</>
                           )}
                        </p>
                        <div className="basket-section-left-data-btns">
                          <form>
                            <div className="basket-section-left-btn">
                              <button
                                type="button"
                                className="min"
                                onClick={
                                  () => updateCart(item, "decrement")
                                  // handleQuantityChange(item.id, 1, "decrement")
                                }
                                disabled={isUpdating}
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
                              <input
                                className="text-spce"
                                value={quantities[item.id]}
                                onChange={(
                                  e: React.ChangeEvent<HTMLInputElement>
                                ) => {
                                  const value = e.target.value;
                                  let parsedValue: number;

                                  // Check if the input value is empty or not a valid number
                                  if (!value || isNaN(parseInt(value))) {
                                    parsedValue = 0; // Treat empty or invalid input as 0
                                  } else {
                                    parsedValue = parseInt(value);
                                  }

                                  quantityChangeInput(parsedValue, item.id);
                                }}
                                onBlur={(e) => {
                                  const value = e.target.value;
                                  let parsedValue: number;
                                  if (!value || isNaN(parseInt(value))) {
                                    parsedValue = 0;
                                  } else {
                                    parsedValue = parseInt(value);
                                  }
                                  if (!parsedValue) return;
                                  updateCart(item, null, parsedValue);
                                }}
                                onKeyDown={(e) => {
                                  if (e.key === "Enter") {
                                    e.preventDefault();
                                    const value = e.currentTarget.value;
                                    let parsedValue: number;
                                    if (!value || isNaN(parseInt(value))) {
                                      parsedValue = 0;
                                    } else {
                                      parsedValue = parseInt(value);
                                    }
                                    if (parsedValue > 0) {
                                      updateCart(item, null, parsedValue);
                                    }
                                  }
                                }}
                                disabled={isUpdating}
                              />
                              <button
                                type="button"
                                className="plus"
                                onClick={() => {
                                  updateCart(item, "increment");
                                }}
                                disabled={isUpdating}
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
                              <button
                                className="cart-delete"
                                onClick={(e) => {
                                  e.preventDefault();
                                  handleRemoveCart(item);
                                }}
                              >
                                <TrashIcon
                                  className="text-dark"
                                  width={18}
                                  height={18}
                                  fontWeight={100}
                                />
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>

              <div className="basket-section-right">
                <div className="basket-section-right-first">
                  <div className="coupon-text">
                    <span>COUPON</span>
                  </div>

                  <div className="coupon-input">
                    <div className="coupon-input-button">
                      <input
                        type="text"
                        placeholder="Enter a coupon code"
                        id="coupon-code"
                        value={couponCode}
                        onChange={(e) => {
                          setCouponCode(e.target.value);
                          setCouponMessage('');
                        }}
                        className="form-control"
                      />
                      {
                        isAccountLocked ?
                          <button
                            type="button"
                            className="basket-discount"
                            onClick={(e) => {
                              e.stopPropagation();
                              showErrorToast(`Your account has been locked`);
                            }}
                          > APPLY
                          </button>
                          :
                          <button type="button"
                            className="basket-discount"
                            onClick={() => {
                              handleApplyCoupon(cartItems);
                            }}
                          >
                            APPLY
                          </button>

                      }

                    </div>

                  </div>
                  <div className="coupon-error-text">
                    {couponMessage && <div
                      className="coupon-error-text-check"
                      dangerouslySetInnerHTML={{ __html: couponMessage }}
                    />}

                  </div>

                </div>

                <div className="basket-section-right-second">
                  <div className="basket-section-right-total">
                    <div className="basket-total">
                      <p style={{
                        lineHeight: `${lineHeight}px`,
                      }}>Total</p>
                    </div>
                    <div className="basket-rate">
                      {discountedTotalPrice != 999999999 && Number(discountedTotalPrice) < Number(totalPrice) ? (
                        <p>
                          <span style={{ textDecoration: 'line-through' }} className="cart-strike-price">
                            £{Number(totalPrice).toFixed(2)}
                          </span>
                          {' '} £{(Number(discountedTotalPrice)).toFixed(2)}
                        </p>
                      ) : (
                        <p>£{Number(totalPrice).toFixed(2)}</p>
                      )}

                    </div>
                  </div>
                  <div className="basket-proceed">
                    {
                      isAccountLocked ?
                        <button
                          type="button"
                          className="basket-proceed"
                          onClick={(e) => {
                            e.stopPropagation();
                            showErrorToast(`Your account has been locked`);
                          }}
                        > Proceed to Checkout
                        </button>
                        :
                        <button
                          type="button"
                          className="basket-proceed"
                          onClick={() => {
                            handleCheckout();
                          }}
                        >
                          Proceed to Checkout
                        </button>
                    }

                  </div>
                </div>
              </div>


            </div>
          ) : (
            <div style={{ margin: "100px" }}>
              <h1
                style={{
                  color: "white",
                  textAlign: "center",
                  fontWeight: 800,
                }}
              >
                Your cart is empty
              </h1>
              <p className="empty-cart-message">
                Looks like you have not added anything to your cart. Go ahead and check out our comps!
              </p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default BasketDynamic;
