import axios from "axios";
import {
  CompetitionType,
  PurchasedTickets,
  QunatityType,
  RewardsType,
  User,
} from "../types";
import { Dispatch } from "@reduxjs/toolkit";
import { addToCart, isAddingToCart } from "../redux/slices/cartSlice";
import { NavigateFunction } from "react-router";
import { setIsAuthenticating, setUserState } from "../redux/slices/userSlice";
// import toast from "react-hot-toast";
import { Modal } from 'bootstrap';
import { showErrorToast } from '../showErrorToast';


export const RewardObj: RewardsType = {
  id: "",
  competition_id: "",
  title: "",
  type: "",
  value: "",
  prcnt_available: "",
  image: "",
  reward_open: false,
  quantity: "",
  full_name: "",
  ticket_number: 0,
  user_id: null,
};

export const competitionObj: CompetitionType = {
  id: 0,
  category: "",
  draw_date: "",
  draw_time: "",
  closing_date: "",
  closing_time: "",
  description: "",
  image: "",
  gallery_images: "",
  price_per_ticket: "",
  quantity: "",
  title: "",
  status: "",
  total_sell_tickets: "",
  total_ticket_sold: "",
  created_at: "",
  max_ticket_per_user: "",
  reward_wins: [],
  instant_wins: [],
  instant_wins_tickets: [],
  enable_instant_wins: "",
  enable_reward_wins: "",
  comp_question: "",
  question: "",
  question_options: "",
  competition_product_id: 0,
  hide_ticket_count: "",
  hide_timer: "",
  disable_tickets: "",
  live_draw_info: "",
  live_draw: '',

};

export const userObj: User = {
  billing_address_1: "",
  billing_address_2: "",
  billing_city: "",
  billing_company: "",
  billing_country: "",
  billing_email: "",
  billing_first_name: "",
  billing_last_name: "",
  billing_postcode: "",
  billing_state: "",
  description: "",
  email: "",
  first_name: "",
  last_name: "",
  limit_duration: "",
  limit_value: "",
  lockout_period: "",
  name: "",
  nickname: "",
  token: "",
  current_spending: "",
  lock_account: "",
  locking_period: "",
  billing_phone: '',
  confirmPassword: '',
  currentPassword: '',
  newPassword: '',
};

export const calculatePercentage = (
  totalSold: number,
  totalTickets: number
) => {
  if (!totalSold) return 0;
  const percentage = Math.floor((totalSold / totalTickets) * 100);
  return percentage;
};

//? functions to check if the competition is drawing today or tommorow
export const isDrawTomorrow = (drawDate: string): boolean => {
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  return new Date(drawDate).toDateString() === tomorrow.toDateString();
};

export const isDrawToday = (drawDate: string): boolean => {
  return new Date(drawDate).toDateString() === new Date().toDateString();
};

//! function to check if the competition is added in past 48  hours
export const isNewlyLaunched = (createdAt: string): boolean => {
  const createdDate = new Date(createdAt);
  const currentTime = new Date();
  const diffInMs = currentTime.getTime() - createdDate.getTime();
  const diffInHours = diffInMs / (1000 * 60 * 60); // Convert milliseconds to hours
  return diffInHours <= 48;
};

export const truncateText = (text: string | null, maxLength: number) => {
  if (text && text.length > maxLength) {
    return text.substring(0, maxLength) + "...";
  }
  return text;
};

// Function to calculate the greatest common divisor (GCD)
function gcd(a: number, b: number): number {
  return b ? gcd(b, a % b) : a;
}

// Function to simplify a fraction
function simplifyFraction(numerator: number, denominator: number): [number, number] {
  const commonDivisor = gcd(numerator, denominator);
  return [numerator / commonDivisor, denominator / commonDivisor];
}

//* odds calculator funtion
export function oddsCalculator(
  totalTickets: number,
  // quantity: number,
  basketQuantity: number
) {
  // const differenceOfTickets = totalTickets - quantity;
  if (basketQuantity <= 0) {
    return "0/1"; // Handle cases where the basket quantity is zero or negative
  }

  const [simplifiedNumerator, simplifiedDenominator] = simplifyFraction(basketQuantity, totalTickets);

  return `${simplifiedNumerator}/${simplifiedDenominator}`;
}

export const calculateRemainingTime = (drawDate: string, drawTime: string) => {
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

export const calculateTotalPrice = (cartItems: CompetitionType[]) => {
  return cartItems.reduce((totalPrice, item) => {
    const itemQty = Number(item.quantity) || 1;
    let itemPrice = 0;

    const startDate = new Date(item.sale_start_date + ' ' + item.sale_start_time);
    const endDate = new Date(item.sale_end_date + ' ' + item.sale_end_time);

    if (
      // item.sale_price && item.sale_price > 0 &&  item.price_per_ticket >   item.sale_price &&  new Date(item.sale_end_date) >= new Date() &&  new Date(item.sale_start_date) <= new Date()
      item.sale_price &&
      item.sale_price > 0 &&
      item.sale_price < item.price_per_ticket &&
      item.sale_end_date &&
      item.sale_start_date &&
      endDate.getTime() >= new Date().getTime() &&
      startDate.getTime() <= new Date().getTime()

    ) {

      itemPrice = itemQty * Number(item.sale_price);

    } else {
      itemPrice = itemQty * Number(item.price_per_ticket);

    }

    return totalPrice + itemPrice;

  }, 0);
};


export const discountedCalculateTotalPrice = (cartItems: CompetitionType[]) => {
  return cartItems.reduce((totalPrice, item) => {
    if (!item.totals) {
      return 999999999;
    }
    const minorUnit = Math.pow(10, item.totals.currency_minor_unit);
    const itemPrice = Number(item.totals.line_total) / minorUnit;
    return totalPrice + itemPrice;
  }, 0);

};


export const quantitySetter = (competitions: CompetitionType[]) => {
  const initialQuantities: QunatityType = {};
  competitions.forEach((item) => {
    initialQuantities[item.id] = parseInt(item.quantity)
      ? parseInt(item.quantity)
      : 1;
  });
  return initialQuantities;
};

export const fetchNonceValue = async (): Promise<
  null | boolean | { nonce: string; cartToken: string }
> => {
  try {
    const response = await axios.get("?rest_route=/wc/store/v1/cart", {
      headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` },
    });
    console.log(response);
    const nonce = response.headers["x-wc-store-api-nonce"];
    const cartToken = response.headers["cart-token"];
    if (response.status === 200) return { nonce, cartToken };
    return null;
  } catch (error) {
    console.log(`Error in getting nonce value ${error}`);
    return false;
  }
};

export const TOKEN = `Bearer ${import.meta.env.VITE_TOKEN}`;

export const NONCE_KEY = "NONCE";

export const NONCE_TIMESTAMP = "NONCE_TIMESTAMP";

export const CART_TOKEN_KEY = "CART_TOKEN_KEY";

export const AUTH_TOKEN_KEY = "AUTH_TOKEN_KEY";

export const UPDATE_CART_KEY = "UPDATE_CART_KEY";

export const CART_HEADER = "CART_HEADER";

export const COMPETITIONS_TO_BE_FETCHED = "COMPETITIONS_TO_BE_FETCHED";

export const FETCHED_COMPETITION = "FETCHED_COMPETITION";

export const SLIDER_SPEED = "SLIDER_SPEED";

export const SUGGESTED_TICKETS = "SUGGESTED_TICKETS";

export const COMPS_QUEST = "COMPS_QUEST";

export const LEGAL_TERMS_ACTIVE_INDEX = "LEGAL_TERMS_ACTIVE_INDEX";

export const ANNOUCMENT = "ANNOUCMENT";

export const LIVE_DRAW_INFO = "LIVE_DRAW_INFO";

export const MAIN_COMPETITION_INFO = "MAIN_COMPETITION_INFO";

export const DESKTOP_HEIGHT = "DESKTOP_HEIGHT";

export const MOBILE_HEIGHT = "MOBILE_HEIGHT";

export const TABLET_HEIGHT = "TABLET_HEIGHT";

export const SHOW_QUESTION = "SHOW_QUESTION";

// export const encryptToken = (token: string): string => {
//   const encodedToken = CryptoJS.AES.encrypt(token, AUTH_TOKEN_KEY).toString();
//   return encodedToken;
// };

export const encryptToken = (token: string): string => btoa(token);

// export const decryptToken = (encodedToken: string): string => {
//   const bytes = CryptoJS.AES.decrypt(encodedToken, AUTH_TOKEN_KEY);
//   const originalText = bytes.toString(CryptoJS.enc.Utf8);
//   return originalText;
// };

export const decryptToken = (encodedToken: string): string =>
  atob(encodedToken);

export const calculateCartQuantity = (items: CompetitionType[]) => {
  if (items.length === 0) return;
  const totalQuantity = items.reduce(
    (acc, item) => parseInt(item.quantity) + acc,
    0
  );
  return totalQuantity;
};

export const handleAddToCart = async (
  competition: CompetitionType,
  cartItems: CompetitionType[],
  dispatch: Dispatch,
  quantities: QunatityType,
  cartKeys: { [key: number]: { key: string } },
  purchasedTickets: PurchasedTickets[],
  isEnabledQuestion: boolean = false, // Default value is set to false
  checkModel: any = 0 // Default value is set to false

) => {

  console.log('isEnabledQuestion+++++on instant win lick', isEnabledQuestion)
  const quantity = quantities[competition.id];
  const alreadyInCart = cartItems.find((item) => item.competition_product_id === competition.competition_product_id);
  const totalTicketsInCart = alreadyInCart ? parseInt(alreadyInCart.quantity) : 0;
  const purchasedTicketsCompetition = purchasedTickets.find((item) => parseInt(item.competition_id) === Number(competition.id)) as PurchasedTickets;
  const ticketsAvailable = parseInt(competition?.total_sell_tickets) - parseInt(competition?.total_ticket_sold);


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
        `Oops! It seems you've already have ${totalTicketsInCart + (boughtTickets || 0)} tickets for this competition in cart.`
      );
      return;
    }

    // if (quantity > ticketsQtyLeft) {
    //   dispatch(isAddingToCart(false));
    //   showErrorToast(
    //     `Oops! It seems you've already have ${totalTicketsInCart + (boughtTickets || 0)} tickets for this competition in cart. You can only get ${ticketsQtyLeft < ticketsAvailable ? ticketsQtyLeft : ticketsAvailable} more.`
    //   );
    //   return;
    // }
    if ((quantity + totalTicketsInCart + (boughtTickets || 0)) > ticketsQtyLeft) {
      dispatch(isAddingToCart(false));

      showErrorToast(
        `Only ${ticketsQtyLeft < ticketsAvailable ? ticketsQtyLeft : ticketsAvailable} tickets are available. You have already bought ${boughtTickets || 0} and added ${totalTicketsInCart} to your basket, so you cannot add more.`
      );

      return;
    }


    if (quantity > ticketsQtyLeft) {
      dispatch(isAddingToCart(false));
      showErrorToast(
        `Only ${ticketsQtyLeft < ticketsAvailable ? ticketsQtyLeft : ticketsAvailable}  tickets available, ${totalTicketsInCart + (boughtTickets || 0)} tickets have been added to your basket.`
      );
      return;
    }

  }


  if (ticketsAvailable === 0) {
    dispatch(isAddingToCart(false));
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


  const globalShowQuestions = localStorage.getItem('SHOW_QUESTION') == '1';
  if (isEnabledQuestion && globalShowQuestions) {


    const modalElements = document.getElementById('enter');
    if (modalElements) {
      const modal = new Modal(modalElements);
      modal.show();
    }

    console.log('isEnabledQuestion:', isEnabledQuestion);
    console.log('globalShowQuestions:', globalShowQuestions);
    console.log('Modal element:', modalElements);

    dispatch(isAddingToCart(false));
    return;
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

  if (checkModel != 1) {
    const modalElement = document.getElementById('exampleModal-3');
    if (modalElement) {
      const modal = new Modal(modalElement);
      modal.show();
    }
  }


  try {
    const URL = import.meta.env.VITE_ADD_TO_CART_API;
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
          "X-WC-Store-api-nonce": nonceVal,       // only for cgg live
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

// export const navigateCompetition = (
//   navigate: NavigateFunction,
//   id: number,
//   category: string
// ) => {
//   navigate(`/competition/details/${id}/${category}`);
// };

export const cartError = (): string => {
  return `You have entered the maximum number of tickets`;
};

export const encodeBase64String = (str: string): string => atob(str);

export const checkAuth = async (token: string, dispatch: Dispatch) => {
  dispatch(setIsAuthenticating(true));
  try {
    const response = await axios.post(
      "?rest_route=/api/v1/check-auth",
      {
        token,
      },
      { headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` } }
    );

    if (response.data.success) {
      dispatch(setUserState(response.data.data));
      const encodedToken = encryptToken(response.data.data.token);
      localStorage.setItem(AUTH_TOKEN_KEY, encodedToken);
    }
  } catch (error) {
    console.log(error);
  } finally {
    dispatch(setIsAuthenticating(true));
  }
};

export function getTodayDate(): string {
  const date = new Date();
  let year = date.getFullYear().toString();
  let month = (date.getMonth() + 1).toString();
  let day = date.getDate().toString();
  // pad single digit months and days with zeros
  month.length === 1 && (month = `0${month}`);
  day.length === 1 && (day = `0${day}`);
  return `${year}-${month}-${day}`;
}

export const checkExistingCart = async (
  dispatch: Dispatch,
  competition: CompetitionType
) => {
  const nonce = localStorage.getItem(NONCE_KEY);
  const cart_header = localStorage.getItem(CART_HEADER);
  try {
    const URL = import.meta.env.VITE_GET_CART;

    const res = await axios.post(
      URL,
      { nonce, cart_header },
      {
        headers: {
          Authorization: TOKEN,
        },
      }
    );

    const itemInCart = res.data.some(
      (item: any) => Number(item.competition.id) === Number(competition.id)
    );

    if (itemInCart) {
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
      dispatch(addToCart(items));
      return true;
    } else {
      return false;
    }
  } catch (error) {
    console.log(error);
  }
};

export const navigateCompetition = (
  id: number,
  category: string,
  competitionName: string,
  navigate: NavigateFunction
) => {
  const splitName = competitionName.split(/\W+/);
  const editedName = splitName.join("-");

  const competitionsToBeFetched = JSON.parse(
    localStorage.getItem(COMPETITIONS_TO_BE_FETCHED) as string
  );

  const newCompetitionToAddToLocalStorage = { id: id, category };

  const newCompsToFetched = {
    ...competitionsToBeFetched,
    ...newCompetitionToAddToLocalStorage,
  };
  localStorage.setItem(
    COMPETITIONS_TO_BE_FETCHED,
    JSON.stringify(newCompsToFetched)
  );

  localStorage.setItem(
    FETCHED_COMPETITION,
    JSON.stringify(newCompetitionToAddToLocalStorage)
  );

  navigate(`/competition/details/${editedName}-${id}`);
};
