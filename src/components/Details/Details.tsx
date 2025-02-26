import { CompetitionType, PurchasedTickets } from "../../types";
import { Navigation, Thumbs, FreeMode, Autoplay } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import "swiper/css/scrollbar";
import "swiper/css/pagination";
import "swiper/css/autoplay";
import CountdownTimer from "../../common/Countdown";
import ReactPlayer from "react-player";
import { Modal } from "bootstrap";
import Slider from "@mui/material/Slider";
import { Swiper as SwiperClass } from "swiper/types"; // Import the correct type
import { showErrorToast } from "../../showErrorToast";
const SLIDER_TRANSIITON_SPEED = import.meta.env.VITE_SLIDER_TRANSIITON_SPEED;
import { Tooltip } from 'react-tooltip';
import { getMediaUrl } from "../../utils/imageS3Url";


import {
  CART_HEADER,
  LEGAL_TERMS_ACTIVE_INDEX,
  LIVE_DRAW_INFO,
  NONCE_KEY,
  NONCE_TIMESTAMP,
  SLIDER_SPEED,
  SUGGESTED_TICKETS,
  UPDATE_CART_KEY,
  calculateCartQuantity,
  calculatePercentage,
  cartError,
  // checkExistingCart,
  fetchNonceValue,
  isDrawToday,
  isDrawTomorrow,
  oddsCalculator,
} from "../../utils";
import { useEffect, useState, useRef, useCallback } from "react";
import { useDispatch, useSelector } from "react-redux";
import { setFetching, setRecommendedComps } from "../../redux/slices";
import { useGetRecommendedCompsMutation } from "../../redux/queries";
import { RootState } from "../../redux/store";
// import axios from "axios";
import BasketModal from "../../common/BasketModal";
import axios from "axios";
import { addToCart, isAddingToCart } from "../../redux/slices/cartSlice";
// import { toast } from "react-hot-toast";
import { Link } from "react-router-dom";

interface PropsType {
  competition: CompetitionType;
  galleryImages: string[] | null;
  galleryVideos: string[] | null;
  sliderSorting: string[] | null;
  sliderSpeed: number;
  postalEntryInfo: string;
  isEnableRewardWin: boolean;
  handleSetCompetition: (qty: number) => void;
  activeTab: number;
  handleActiveTab: (tabNumber: number) => void;
}

const Details: React.FC<PropsType> = ({
  competition,
  galleryImages,
  sliderSpeed,
  postalEntryInfo,
  isEnableRewardWin,
  handleSetCompetition,
  galleryVideos,
  sliderSorting,
  handleActiveTab,
  activeTab
}) => {
  //* define hooks and import states from redux
  const cartItems = useSelector((state: RootState) => state.cart.cartItems);
  // const [activetab, setActivetTab] = useState<number>(1);
  const [quantity, setQuantity] = useState<number>(1);
  const [oddsVariables, setOddsVariables] = useState({
    totalTickets: 0,
    quantity: 0,
    basketQuantity: 0,
  });
  console.log("oddsVariables", oddsVariables);
  const [cartKeys] = useState(() => {
    const keys = localStorage.getItem(UPDATE_CART_KEY);
    return keys ? JSON.parse(keys) : {};
  });
  const suggested_tickets = parseInt(
    localStorage.getItem(SUGGESTED_TICKETS) as string
  );

  const [cartQuantity, setCartQuantity] = useState<number>(0);
  const [inputPadding, setInputPadding] = useState<number>(20);
  const { user } = useSelector((state: RootState) => state.userReducer);
  const [isAccountLocked, setIsAccountLock] = useState(false);
  const [isAccountLockedPeriod, setIsAccountLockPeriod] = useState("");

  const [divColorlefts, setDivColorleft] = useState("#2CB4A5");
  const [divColorRights, setDivColorRight] = useState("#2CB4A5");
  const [isSliderColor, setSliderColor] = useState("#2CB4A5");

  const [isClassActive, setIsClassActive] = useState(false);

  useEffect(() => {
    if (user && parseInt(user.lock_account)) {
      setIsAccountLock(true);
      setIsAccountLockPeriod(user.locking_period);
    }
  }, [user]);

  const { purchasedTickets } = useSelector(
    (state: RootState) => state.userReducer
  );

  //todo effect for updating cart quantity and odds calculation
  useEffect(() => {
    const purchasedTicketsCompetition = purchasedTickets.find(
      (item) => parseInt(item.competition_id) === Number(competition.id)
    ) as PurchasedTickets;
    const boughtTickets = parseInt(purchasedTicketsCompetition?.total_tickets);

    const quantitys = calculateCartQuantity(cartItems);
    const quantitySingle = cartItems.find((item) => item.id == competition.id);

    setCartQuantity(quantitys || 0);

    // const remainingTickets =
    //   Number(competition.total_sell_tickets) -
    //   Number(competition.total_ticket_sold);

    const remainingTickets = Number(competition.total_sell_tickets);

    const currentOdds = oddsCalculator(
      remainingTickets,
      (Number(quantitySingle?.quantity) || 0) + (Number(quantity || 0)) + (boughtTickets || 0)
    );

    setCurrentOdds(currentOdds);

    const recomnmendedOdds = oddsCalculator(
      remainingTickets,
      Number(quantitySingle?.quantity)
        ? Number(quantitySingle?.quantity) +
        suggested_tickets +
        (boughtTickets || 0) + (Number(quantity || 0))
        : suggested_tickets + (boughtTickets || 0)
    );

    console.log("remainingTickets", remainingTickets);
    console.log("quantity", quantity);
    console.log("boughtTickets", boughtTickets);
    console.log("recomnmendedOdds", recomnmendedOdds);

    setRecommendedOdds(recomnmendedOdds);
  }, [cartItems, quantity]);

  const dispatch = useDispatch();

  const [currentOdds, setCurrentOdds] = useState<any>(0);
  const [recomnmendedOdds, setRecommendedOdds] = useState<any>(0);

  // const { purchasedTickets } = useSelector(
  //   (state: RootState) => state.userReducer
  // );

  // initially set competition quantity
  useEffect(() => {
    if (
      Number(competition.quantity) &&
      Number(competition.quantity) > Number(competition.max_ticket_per_user)
    ) {
      setQuantity(Number(competition.max_ticket_per_user));
    } else if (Number(competition.quantity)) {
      setQuantity(Number(competition.quantity));
    } else {
      setQuantity(1);
    }
  }, [competition]);

  // const handleActiveTab = (index: number) => {
  //   if (activetab !== index) {
  //     setActivetTab(index);
  //   }

  //   // activetab === index ? setActivetTab(-1) : setActivetTab(index);
  // };

  competition.reward_wins.filter((item) => item.reward_open);

  //* effect for setting odds variable data
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
    }
  }, [competition]);

  //? quantity change function
  const handleQuantityChange = (operation: string) => {
    const currentQty = quantity;
    const ticketsAvailable =
      parseInt(competition.total_sell_tickets) -
      parseInt(competition.total_ticket_sold);
    if (currentQty === ticketsAvailable && operation === "increment") return;
    if (
      quantity === Number(competition.max_ticket_per_user) &&
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

    // const oddsResult = oddsCalculator(
    //   oddsVariables.totalTickets,
    //   // value,
    //   cartQuantity
    // );
    // const recomnmendedOdds = oddsCalculator(
    //   oddsVariables.totalTickets,
    //   cartQuantity + suggested_tickets
    // );
    // setCurrentOdds(oddsResult);
    // setRecommendedOdds(recomnmendedOdds);
  };

  //todo select recommended competitions from redux store
  const recommendComps = useSelector(
    (state: RootState) => state.competition.recommendComps
  );

  //? import query func for fetching recommended competition
  const [fetchRecommendedComps] = useGetRecommendedCompsMutation();

  const isEnabledQuestion = Number(competition.comp_question) ? true : false;
  const isEnabledQuestionGlobaly = Number(localStorage.getItem("SHOW_QUESTION"))
    ? true
    : false;
  console.log("isEnabledQuestionGlobaly--details", isEnabledQuestionGlobaly);

  //* handling modal open func
  const handleModalOpen = async (competition: CompetitionType) => {
    const isExistingComps =
      recommendComps.length > 0
        ? recommendComps.every((item) => item.category === competition.category)
        : false;
    if (isExistingComps) return;

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

  //* add to cart
  const handleAddToCart = async (
    competition: CompetitionType,
    isEnabledQuestion: any
  ) => {
    const purchasedTicketsCompetition = purchasedTickets.find(
      (item) => parseInt(item.competition_id) === Number(competition.id)
    ) as PurchasedTickets;
    const ticketsAvailable =
      parseInt(competition?.total_sell_tickets) -
      parseInt(competition?.total_ticket_sold);
    //first we need to check
    const alreadyInCart = cartItems.find(
      (item) =>
        item.competition_product_id === competition.competition_product_id
    );

    const totalTicketsInCart = alreadyInCart
      ? parseInt(alreadyInCart.quantity)
      : 0;
    if (alreadyInCart) {
      const ticketsQtyLeft =
        parseInt(competition.max_ticket_per_user) - totalTicketsInCart;
      const boughtTickets = parseInt(
        purchasedTicketsCompetition?.total_tickets
      );

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
          `Oops! It seems you've already have ${totalTicketsInCart + (boughtTickets || 0)
          } tickets for this competition in cart.`
        );
        return;
      }

      // if (quantity > ticketsQtyLeft) {
      //   dispatch(isAddingToCart(false));
      //   showErrorToast(
      //     `Oops! It seems you've already have ${totalTicketsInCart + (boughtTickets || 0)
      //     } tickets for this competition in cart. You can only get ${ticketsQtyLeft < ticketsAvailable
      //       ? ticketsQtyLeft
      //       : ticketsAvailable
      //     } more.`
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
          `Only ${ticketsQtyLeft < ticketsAvailable ? ticketsQtyLeft : ticketsAvailable } tickets available, ${totalTicketsInCart + (boughtTickets || 0)} tickets have been added to your basket.`
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
      const boughtTickets = parseInt(
        purchasedTicketsCompetition?.total_tickets
      );
      const ticketsQtyLeft =
        parseInt(competition.max_ticket_per_user) - (boughtTickets || 0);
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

    const globalShowQuestions = localStorage.getItem("SHOW_QUESTION") == "1";
    if (isEnabledQuestion && globalShowQuestions) {
      const modalElement = document.getElementById("enter");
      if (modalElement) {
        const modal = new Modal(modalElement);
        modal.show();
      }
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

    const modalElement = document.getElementById("exampleModal-3");
    if (modalElement) {
      const modal = new Modal(modalElement);
      modal.show();
    }

    const cart_header = localStorage.getItem(CART_HEADER) || undefined;

    const URL = import.meta.env.VITE_ADD_TO_CART_API;
    // const URL = "?rest_route=/wc/store/v1/cart/add-item";

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
            "X-WC-Store-api-nonce": nonceVal, //only for cgg live
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

  const savedSpeed = parseInt(localStorage.getItem(SLIDER_SPEED) as string);

  const disbaleTickets = parseInt(competition.disable_tickets) ? true : false;
  const hideTicketCount = parseInt(competition.hide_ticket_count)
    ? true
    : false;
  const hideTimer = parseInt(competition.hide_timer) ? true : false;

  const [spanPadding, setSpanRight] = useState<number>(24);
  // const [spanTopPos] = useState<number>(() => {
  //   if (navigator.userAgent.includes("Mac")) {
  //     return 20;
  //   } else {
  //     return 18;
  //   }
  // });

  useEffect(() => {
    if (quantity >= 1000 && quantity < 10000) {
      setSpanRight(16);
    } else if (quantity > 9999) {
      setSpanRight(3);
    } else {
      setSpanRight(20);
    }

    if (quantity >= 100) {
      // setSpanRight
      setInputPadding(4);
    } else if (quantity >= 10) {
      // setSpanRight(20);
      setInputPadding(20);
    } else {
      setInputPadding(24);
    }
  }, [quantity]);

  const liveDrawInfo = localStorage.getItem(LIVE_DRAW_INFO);

  const liveDrawInfoJSX = () => {
    if (competition.live_draw_info && parseInt(competition.live_draw)) {
      return (
        <div className="win-twenty-bait-main-left-bottom-left-txt">
          <h2>
            Live Draw{" "}
            <span>
              {" "}
              {new Date(competition.draw_date).toLocaleDateString("en-GB", {
                year: "numeric",
                month: "short",
                day: "2-digit",
              })}{" "}
              @ {competition.draw_time.split(":").slice(0, 2).join(":")} pm
            </span>
          </h2>

          <p
            dangerouslySetInnerHTML={{ __html: competition.live_draw_info }}
          ></p>
        </div>
      );
    } else if (liveDrawInfo) {
      return (
        <div className="win-twenty-bait-main-left-bottom-left-txt">
          <h2>
            Live Draw{" "}
            <span>
              {" "}
              {new Date(competition.draw_date).toLocaleDateString("en-GB", {
                year: "numeric",
                month: "short",
                day: "2-digit",
              })}{" "}
              @ {competition.draw_time.split(":").slice(0, 2).join(":")} pm
            </span>
          </h2>
          <p dangerouslySetInnerHTML={{ __html: liveDrawInfo }}></p>
        </div>
      );
    } else {
      <div className="win-twenty-bait-main-left-bottom-left-txt">
        <h2>
          Live Draw{" "}
          <span>
            {" "}
            {new Date(competition.draw_date).toLocaleDateString("en-GB", {
              year: "numeric",
              month: "short",
              day: "2-digit",
            })}{" "}
            @ {competition.draw_time.split(":").slice(0, 2).join(":")} pm
          </span>
        </h2>
        <p>
          Interactive live draw on our{" "}
          <a href="https://www.facebook.com/carpgeargiveaways/" target="_blank">
            Facebook
          </a>{" "}
          page and <a href="#">Youtube</a> channel
        </p>
      </div>;
    }
  };

  const liveDrawInfoJsxMob = () => {
    if (competition.live_draw_info && parseInt(competition.live_draw)) {
      return (
        <div className="mob-win-twenty-bait-main-bottom-one">
          <h2>
            Live Draw{" "}
            <span>
              {" "}
              {new Date(competition.draw_date).toLocaleDateString("en-GB", {
                year: "numeric",
                month: "short",
                day: "2-digit",
              })}{" "}
              @ {competition.draw_time.split(":").slice(0, 2).join(":")} pm
            </span>
          </h2>

          <p
            dangerouslySetInnerHTML={{ __html: competition.live_draw_info }}
          ></p>
        </div>
      )
    } else if (liveDrawInfo) {
      return (
        <div className="mob-win-twenty-bait-main-bottom-one">
          <h2>
            Live Draw{" "}
            <span>
              {" "}
              {new Date(competition.draw_date).toLocaleDateString("en-GB", {
                year: "numeric",
                month: "short",
                day: "2-digit",
              })}{" "}
              @ {competition.draw_time.split(":").slice(0, 2).join(":")} pm
            </span>
          </h2>
          <p dangerouslySetInnerHTML={{ __html: liveDrawInfo }}></p>
        </div>
      );
    } else {
      <div className="mob-win-twenty-bait-main-bottom-one">
        <h2>
          Live Draw{" "}
          <span>
            {" "}
            {new Date(competition.draw_date).toLocaleDateString("en-GB", {
              year: "numeric",
              month: "short",
              day: "2-digit",
            })}{" "}
            @ {competition.draw_time.split(":").slice(0, 2).join(":")} pm
          </span>
        </h2>
        <p>
          Interactive live draw on our{" "}
          <a href="https://www.facebook.com/carpgeargiveaways/" target="_blank">
            Facebook
          </a>{" "}
          page and <a href="#">Youtube</a> channel
        </p>
      </div>;
    }
  }

  // console.log('purchasedTickets+++', purchasedTickets);
  // Find the number of tickets purchased for the current competition
  const purchasedTicketData = purchasedTickets.find(
    (item) => item.competition_id === competition.id.toString()
  );

  const totalSoldPercentage = calculatePercentage(
    Number(competition.total_ticket_sold),
    Number(competition.total_sell_tickets)
  );

  const getUnlockMessage = () => {
    for (const element of competition.reward_wins) {
      const reward = element;
      if (!reward.reward_open) {
        return `Unlocks at ${reward.prcnt_available}%`;
      }
    }
    return "ALL PRIZES UNLOCKED! GOODLUCK!";
  };
  const unlockMessage = getUnlockMessage();

  const featureImage =
    competition && competition.image ? competition.image : "";

  interface GalleryVideo {
    video: string;
    thumb: string;
  }

  const galleryItems = [
    // Map gallery images
    ...(galleryImages || []).map((image, index) => ({
      type: "image",
      src: image,
      id: `img-${index}`,
      thumb: "", // No thumbnail for images
    })),

    // Map gallery videos
    ...((galleryVideos as unknown as GalleryVideo[]) || []).map(
      (video, index) => ({
        type: "video",
        src: video.video, // Video source
        id: `vid-${index}`,
        thumb: video.thumb, // Thumbnail for video
      })
    ),

    // Adding the feature image
    { type: "image", src: featureImage, id: "feature-image", thumb: "" }, // No thumbnail for feature image
  ];

  let sortedGalleryItems = [];
  if (sliderSorting && sliderSorting.length > 0) {
    sliderSorting.forEach((sortKey) => {
      if (sortKey === "Video URLs") {
        sortedGalleryItems.push(
          ...galleryItems.filter((item) => item.type === "video")
        );
      } else if (sortKey === "Feature Image") {
        const featureItem = galleryItems.find(
          (item) => item.id === "feature-image"
        );
        if (featureItem) sortedGalleryItems.push(featureItem);
      } else if (sortKey === "Gallery Images") {
        sortedGalleryItems.push(
          ...galleryItems.filter(
            (item) => item.type === "image" && item.id !== "feature-image"
          )
        );
      }
    });
  } else {
    // If sliderSorting is empty, keep the original order of galleryItems
    sortedGalleryItems.push(...galleryItems);
  }

  if (sortedGalleryItems && sortedGalleryItems.length > 0) {
    sortedGalleryItems = sortedGalleryItems.filter(
      (item) =>
        item.src &&
        (item.src.startsWith("http://") || item.src.startsWith("https://"))
    );
  }

  console.log("sortedGalleryItems", sortedGalleryItems);

  const swiperRef = useRef<any>(null);
  const [autoplayPaused, setAutoplayPaused] = useState(false);
  // const handleVideoPlayPause = () => {
  //   // Pause Swiper autoplay when video starts playing
  //   if (swiperRef.current && !autoplayPaused) {
  //     swiperRef?.current?.swiper.autoplay.stop();
  //     setAutoplayPaused(true);
  //   }
  // };

  // const handleVideoPause = () => {
  //   // Resume Swiper autoplay when video ends or is paused
  //   if (swiperRef.current && autoplayPaused) {
  //     swiperRef?.current?.swiper.autoplay.start();
  //     setAutoplayPaused(false);
  //   }
  // };

  const [isOpen, setIsOpen] = useState(false);

  const openModal = () => {
    setIsOpen(true);
  };

  const closeModal = () => {
    setIsOpen(false);
  };

  const rewardprizetext = localStorage.getItem("rewardprizetext");

  const handleChangeSlider = (_event: Event, newValue: number | number[]) => {
    const ticketsAvailable =
      parseInt(competition.total_sell_tickets) -
      parseInt(competition.total_ticket_sold);
    const maxTickets = Math.min(
      ticketsAvailable,
      Number(competition.max_ticket_per_user)
    );
    if (Number(newValue) > maxTickets) {
      showErrorToast(cartError());
      return;
    }

    setQuantity(newValue as number);
  };

  const handleSliderChangeCommitted = () => {
    setDivColorRight("#2CB4A5"); // Change to slider color when pressed
    setDivColorleft("#2CB4A5"); // Change to slider color when pressed
    setSliderColor("#2CB4A5");
  };

  // const [thumbsSwiper, setThumbsSwiper] = useState(null);
  // const handleThumbsSwiper = (swiper: SetStateAction<null>) => {
  //   console.log('Thumbs Swiper Instance:', swiper);
  //   setThumbsSwiper(swiper);
  // };

  const [thumbsSwiper, setThumbsSwiper] = useState<SwiperClass | null>(null);
  const handleThumbsSwiper = (swiper: SwiperClass | null) => {
    console.log("Thumbs Swiper Instance:", swiper);
    setThumbsSwiper(swiper);
  };

  console.log(inputPadding);
  console.log(spanPadding);

  // const handleVideoReady = (player: ReactPlayer) => {
  //   // Seek to 5 seconds when video is ready
  //   if (player) {
  //     player.seekTo(5); // Seek to 5 seconds
  //   }
  // };

  // Function to clean up unwanted tags and inline styles
  const cleanUnwantedTags = (htmlString: string) => {
    return (
      htmlString
        // Remove <colgroup>, <col> tags, and <style> blocks
        .replace(/<colgroup[^>]*>.*?<\/colgroup>/g, "")
        .replace(/<col[^>]*>/g, "")
        .replace(/<style[^>]*>.*?<\/style>/g, "")
        // Optionally clean empty <p> tags
        .replace(/<p>\s*<\/p>/g, "")
    );
  };

  // Optional function to remove inline styles from images, keeping the `src` intact
  // const cleanImageStyles = (htmlString:string) => {
  //   return htmlString.replace(/<img[^>]*style="[^"]*"([^>]*)>/g, '<img$1>');
  // };

  // Full cleaning process
  const processHTML = (htmlString: string) => {
    let cleanedHTML = cleanUnwantedTags(htmlString);
    // cleanedHTML = cleanImageStyles(cleanedHTML);
    return cleanedHTML;
  };

  const [isVideoPlaying, setIsVideoPlaying] = useState(false);
  const [activeIndex, setActiveIndex] = useState(0);

  useEffect(() => {
    const swiper = swiperRef.current?.swiper;
    if (swiper) {
      const slideChangeHandler = () => {
        if (isVideoPlaying) {
          swiper.slideTo(activeIndex, 0, false);
        } else {
          setActiveIndex(swiper.activeIndex);
        }
      };
      swiper.on("slideChange", slideChangeHandler);
      return () => {
        swiper.off("slideChange", slideChangeHandler);
      };
    }
  }, [isVideoPlaying, activeIndex]);

  const handleVideoPlay = useCallback(() => {
    setIsVideoPlaying(true);
    if (swiperRef.current && swiperRef.current.swiper) {
      swiperRef.current.swiper.autoplay.stop();
      swiperRef.current.swiper.allowSlideNext = false;
      swiperRef.current.swiper.allowSlidePrev = false;
      swiperRef.current.swiper.allowTouchMove = false;
    }
  }, []);

  const handleVideoPause = useCallback(() => {
    setIsVideoPlaying(false);
    if (swiperRef.current && swiperRef.current.swiper) {
      swiperRef.current.swiper.autoplay.start();
      swiperRef.current.swiper.allowSlideNext = true;
      swiperRef.current.swiper.allowSlidePrev = true;
      swiperRef.current.swiper.allowTouchMove = true;
    }
  }, []);

  const opacityDownTopSection = () => {
    setIsClassActive(true);
  };
  const opacityUpTopSection = () => {
    setIsClassActive(false);
  };

  const startDate = new Date(competition.sale_start_date + ' ' + competition.sale_start_time);
  const endDate = new Date(competition.sale_end_date + ' ' + competition.sale_end_time);

  const S3_BASE_URL = import.meta.env.VITE_STATIC_IMAGES_URL;

  return (
    <div>
      <BasketModal />
      <section className="win-twenty-bait-main">
        <div className="container">
          <div className="win-twenty-bait-main-all">
            <div className="row win-twenty-bait-align">
              <div className="col-lg-6 col-md-12">
                <div className="win-twenty-bait-main-left">
                  <div className="ticekt-text-div">
                    {purchasedTicketData ? (
                      <div className="ticket-info">
                        You currently have
                        <span>{purchasedTicketData.total_tickets} Tickets</span>
                        in this comp
                      </div>
                    ) : null}
                  </div>
                  <Swiper
                    ref={swiperRef}
                    thumbs={{ swiper: thumbsSwiper }}
                    className="swiper Single-comp-slider"
                    modules={[Autoplay, Thumbs, FreeMode]}
                    speed={SLIDER_TRANSIITON_SPEED}
                    autoplay={{
                      delay: savedSpeed || sliderSpeed,
                      disableOnInteraction: false,
                      pauseOnMouseEnter: true,
                    }}
                    loop={true}
                    freeMode={true}
                    init={true}
                    onSlideChange={() => {
                      if (autoplayPaused) {
                        swiperRef.current.swiper.autoplay.start();
                        setAutoplayPaused(false);
                      }
                    }}
                  // pagination={{ el: ".swiper-pagination", clickable: true }}
                  >
                    <div className="swiper-wrapper">
                      {sortedGalleryItems && sortedGalleryItems.length > 0 ? (
                        sortedGalleryItems.map((image, index) => (
                          <SwiperSlide className="swiper-slide" key={index}>
                            <div className="Single-comp-slider-data">
                              {image.type == "image" ? (
                                <img src={getMediaUrl(image.src)} alt="single competition" />
                              ) : (
                                <div className="swiper-video-container">
                                  <ReactPlayer
                                    url={image.src}
                                    className="react-player-video"
                                    controls
                                    width="100%"
                                    height="100%"
                                    onPlay={handleVideoPlay}
                                    onPause={handleVideoPause}
                                    onEnded={handleVideoPause}
                                    config={{
                                      youtube: {
                                        playerVars: { autoplay: 0 },
                                      },
                                    }}
                                  />
                                </div>
                              )}
                            </div>
                          </SwiperSlide>
                        ))
                      ) : (
                        <SwiperSlide className="swiper-slide">
                          <div className="Single-comp-slider-data">
                            <img
                              src={getMediaUrl(competition.image)}
                              alt="single competition"
                            />
                          </div>
                        </SwiperSlide>
                      )}
                    </div>
                    {/* <div className="swiper-pagination" /> */}
                  </Swiper>
                  {/* Thumbnail Swiper */}
                  <Swiper
                    onSwiper={handleThumbsSwiper}
                    spaceBetween={10}
                    slidesPerView={4}
                    freeMode={true}
                    modules={[FreeMode, Navigation, Thumbs]}
                    className="thumbnail-swiper"
                  >
                    {sortedGalleryItems && sortedGalleryItems.length > 0 ? (
                      sortedGalleryItems.map((image, index) => (
                        <SwiperSlide className="swiper-slide" key={index}>
                          {image.type === "image" ? (
                            <img src={getMediaUrl(image.src)} alt="thumbnail" />
                          ) : (
                            <div className="video-thumb">
                              {!image.thumb ? (
                                <img
                                  src={`${S3_BASE_URL}/images/blackDefault.png`}
                                  alt="thumbnail"
                                />
                              ) : (
                                <img src={getMediaUrl(image.thumb)} alt="thumbnail" />
                              )}
                              <div className="play-icon"></div>
                            </div>
                          )}
                        </SwiperSlide>
                      ))
                    ) : (
                      <SwiperSlide className="swiper-slide">
                        <img src={getMediaUrl(competition.image)} alt="thumbnail" />
                      </SwiperSlide>
                    )}
                  </Swiper>
                  <div className="swiper-pagination" />

                  <div className="win-twenty-bait-main-left-bottom">
                    {competition.instant_win_only == 0 ? (
                      <>
                        <div className="win-twenty-bait-main-left-bottom-left">
                          {liveDrawInfoJSX()}
                        </div>
                        <div className="win-twenty-bait-main-left-bottom-right">
                          {!hideTicketCount && (
                            <div className="win-twenty-bait-main-left-bottom-right-one">
                              <h2>
                                {parseInt(competition.total_sell_tickets) -
                                  parseInt(competition.total_ticket_sold)}
                              </h2>
                              <p>Tickets Available</p>
                            </div>
                          )}
                          <div className="win-twenty-bait-main-left-bottom-right-two">
                            <p>Maximum</p>
                            <h2>{competition.max_ticket_per_user}</h2>
                            <p>Per Person</p>
                          </div>
                        </div>
                      </>
                    ) : (
                      <div className="win-twenty-bait-main-only-two">
                        {!hideTicketCount && (
                          <div className="win-twenty-bait-main-left-bottom-only-one">
                            <h2>
                              {parseInt(competition.total_sell_tickets) -
                                parseInt(competition.total_ticket_sold)}
                            </h2>
                            <p>Tickets Available</p>
                          </div>
                        )}
                        <div className="win-twenty-bait-main-left-bottom-only-one">
                          <p>Maximum</p>
                          <h2>{competition.max_ticket_per_user}</h2>
                          <p>Per Person</p>
                        </div>
                      </div>
                    )}
                  </div>
                </div>
              </div>
              <div className="col-lg-6 col-md-12">
                <div className="win-twenty-bait-main-right">
                  <div className="win-twenty-bait-main-right-head">
                    <h1>{competition.title}</h1>
                    <p
                      dangerouslySetInnerHTML={{
                        __html: processHTML(competition.description),
                      }}
                    ></p>
                  </div>
                  <div className="win-twenty-bait-main-right-head-eur">
                    <div className="win-twenty-bait-main-right-head-eur-left">
                      {" "}
                      {


                        competition.sale_price &&
                          competition.sale_price > 0 &&
                          competition.sale_price < competition.price_per_ticket &&
                          competition.sale_end_date &&
                          competition.sale_start_date &&
                          endDate.getTime() >= new Date().getTime() &&
                          startDate.getTime() <= new Date().getTime() ? (


                          <h4>
                            <span className="strikethrough-text">
                              {`£${competition.price_per_ticket}`}{" "}
                            </span>
                            {""}
                            <span>{`£${competition.sale_price}`}</span>{" "}
                            <div className="per-entt">PER ENTRY</div>
                          </h4>
                        ) : (
                          <h4>
                            <span>{`£${competition.price_per_ticket}`}</span>{" "}
                            <div className="per-entt">PER ENTRY</div>
                          </h4>
                        )}
                      {/* <h4>
                        // <span>{`£${competition.price_per_ticket}`}</span> <div className="per-entt">PER ENTRY</div>
                      </h4> */}
                    </div>

                    {/* <div className="win-twenty-bait-main-right-head-eur-right">
                      <p>
                        Earn{" "}
                        <span>
                          {Number(competition.price_per_ticket) * 100}
                        </span>{" "}
                        Points when you Enter!
                      </p>
                    </div> */}

                  </div>
                  <div className="single-comp-text-area">
                    {!hideTimer && (
                      <div className="single-comp-one">
                        <div className="single-draw-btn">
                          <CountdownTimer
                            drawDate={competition.draw_date}
                            drawTime={competition.draw_time}
                            detailPageClass="single"
                          />
                        </div>
                        <div className="single-comp-ones">
                          <div className="single-comps-clock">
                            <svg
                              className="sing-clock"
                              width="17"
                              height="16"
                              viewBox="0 0 17 16"
                              fill="none"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path
                                d="M9.64095 7.33334V4C9.64095 3.82319 9.57071 3.65362 9.44569 3.5286C9.32066 3.40357 9.15109 3.33334 8.97428 3.33334C8.79747 3.33334 8.6279 3.40357 8.50288 3.5286C8.37785 3.65362 8.30762 3.82319 8.30762 4V8C8.30762 8.17681 8.37785 8.34638 8.50288 8.47141C8.6279 8.59643 8.79747 8.66667 8.97428 8.66667H12.3076C12.4844 8.66667 12.654 8.59643 12.779 8.47141C12.904 8.34638 12.9743 8.17681 12.9743 8C12.9743 7.82319 12.904 7.65362 12.779 7.5286C12.654 7.40357 12.4844 7.33334 12.3076 7.33334H9.64095ZM8.97428 14.6667C5.29228 14.6667 2.30762 11.682 2.30762 8C2.30762 4.318 5.29228 1.33334 8.97428 1.33334C12.6563 1.33334 15.6409 4.318 15.6409 8C15.6409 11.682 12.6563 14.6667 8.97428 14.6667Z"
                                fill="#2CB4A5"
                              ></path>
                            </svg>

                            <svg
                              className="sing-clock-one"
                              width="13"
                              height="12"
                              viewBox="0 0 13 12"
                              fill="none"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path
                                d="M7.37231 5.5V3C7.37231 2.86739 7.31964 2.74021 7.22587 2.64645C7.1321 2.55268 7.00492 2.5 6.87231 2.5C6.73971 2.5 6.61253 2.55268 6.51876 2.64645C6.42499 2.74021 6.37231 2.86739 6.37231 3V6C6.37231 6.13261 6.42499 6.25979 6.51876 6.35355C6.61253 6.44732 6.73971 6.5 6.87231 6.5H9.37231C9.50492 6.5 9.6321 6.44732 9.72587 6.35355C9.81964 6.25979 9.87231 6.13261 9.87231 6C9.87231 5.86739 9.81964 5.74021 9.72587 5.64645C9.6321 5.55268 9.50492 5.5 9.37231 5.5H7.37231ZM6.87231 11C4.11081 11 1.87231 8.7615 1.87231 6C1.87231 3.2385 4.11081 1 6.87231 1C9.63381 1 11.8723 3.2385 11.8723 6C11.8723 8.7615 9.63381 11 6.87231 11Z"
                                fill="#2CB4A5"
                              ></path>
                            </svg>

                            <div className="single-comps-clock-txt">
                              <p>
                                {" "}
                                {isDrawTomorrow(competition.draw_date) ? (
                                  <span>Draws tomorrow</span>
                                ) : isDrawToday(competition.draw_date) ? (
                                  <span>Draws today</span>
                                ) : new Date(competition.draw_date) <
                                  new Date() ? (
                                  <span>Closed</span>
                                ) : (
                                  <>
                                    Draw:{" "}
                                    <span className="for-line-rgt">
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
                                {/* <span className="slash-straight-comp">|</span> */}
                                {/* <span className="for-line-lef">
                                {competition.draw_time
                                  .split(":")
                                  .slice(0, 2)
                                  .join(":")}
                                pm
                              </span> */}
                                <span className="for-line-lef">
                                  {(() => {
                                    const drawTime = competition.draw_time
                                      .split(":")
                                      .slice(0, 2)
                                      .join(":");
                                    const drawHour = parseInt(
                                      drawTime.split(":")[0],
                                      10
                                    );
                                    const amOrPm = drawHour >= 12 ? "pm" : "am";
                                    return drawTime + amOrPm;
                                  })()}
                                </span>
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                    )}

                    <div className="single-comp-progress-all">
                      <div className="single-progs-26">
                        <div
                          className="single-progs-26-shade"
                          style={{
                            width: `${calculatePercentage(
                              Number(competition.total_ticket_sold),
                              Number(competition.total_sell_tickets)
                            )}%`,
                          }}
                        ></div>
                        {!hideTicketCount && (
                          <h5>
                            <div className="single-progs-lef">
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
                                />
                              </svg>
                              <h4>{competition.total_ticket_sold} </h4>
                            </div>
                            <div className="single-progs-rgt">
                              <h4>{competition.total_sell_tickets}</h4>
                            </div>
                          </h5>
                        )}
                      </div>
                      <div className="single-progs-per">
                        <h4>
                          {calculatePercentage(
                            Number(competition.total_ticket_sold),
                            Number(competition.total_sell_tickets)
                          )}
                          %
                        </h4>
                      </div>
                    </div>

                    {isEnableRewardWin && (
                      <div className="multisteps">
                        <div className="multisteps-all">
                          <div
                            className="multisteps-one-all"

                          >
                            {competition.reward_wins.map((reward, index) => (
                              <>
                                {" "}
                                <div
                                  id={`clickable-${index}`} // Use unique ids for each reward step
                                  className={`multisteps-one-left one-right  num-select ${reward.reward_open && "active"
                                    }`}
                                  key={reward.id}
                                  onClick={openModal}
                                >
                                  <h4>{index + 1}</h4>
                                </div>
                                {(() => {
                                  // Find the last active reward (reward_open === true)
                                  const lastActiveRewardIndex =
                                    competition.reward_wins
                                      .map((reward, index) =>
                                        reward.reward_open ? index : null
                                      )
                                      .filter((index) => index !== null) // Filter out non-active indices
                                      .pop(); // Get the last active index, or undefined if none active

                                  const tooltipIndex =
                                    lastActiveRewardIndex !== undefined
                                      ? lastActiveRewardIndex // Show for the last active reward
                                      : 0; // Fallback to the first reward if no active rewards

                                  return (
                                    <Tooltip
                                      anchorSelect={`#clickable-${tooltipIndex}`}
                                      clickable
                                      isOpen={true}
                                      place="bottom-start"
                                      className="tooltip-single-competiton"
                                    >
                                      <div className="single-steps-tier__">
                                        <div className="single-steps-tier-txt__">
                                          <h5>
                                            {totalSoldPercentage <
                                              Number(
                                                competition.reward_wins.find(
                                                  (reward) => !reward.reward_open
                                                )?.prcnt_available
                                              )
                                              ? unlockMessage
                                              : "ALL PRIZES UNLOCKED! GOODLUCK!"}
                                            {"  "}
                                            {"  "}
                                            <a href="#reward-scroll">
                                              <span className="view-tier">
                                                View prizes
                                              </span>
                                            </a>
                                          </h5>
                                        </div>
                                      </div>
                                    </Tooltip>
                                  );
                                })()}
                              </>
                            ))}

                            {isOpen && (
                              <div
                                className="popup-overlay"
                                onClick={closeModal}
                              >
                                <div
                                  className="popup-content"
                                  onClick={(e) => e.stopPropagation()}
                                >
                                  <span
                                    className="close-btn"
                                    onClick={closeModal}
                                  >
                                    &times;
                                  </span>
                                  <div className="popup-body">
                                    <div className="popup-icon"></div>
                                    <div className="popup-icon-text"
                                      dangerouslySetInnerHTML={{
                                        __html:
                                          rewardprizetext ||
                                          "As each Reward Win tier is unlocked, all tickets purchased from preceding tiers are automatically entered into the draw for the newly unlocked prize. Winners will be drawn during the next Facebook live event and notified via email.",
                                      }}
                                    ></div>
                                  </div>
                                </div>
                              </div>
                            )}
                          </div>
                        </div>

                      </div>
                    )}

                    {/* {isEnableRewardWin && (
                      <div className="single-steps-tier">
                        <div className="single-steps-tier-txt">
                          <h5>
                            {totalSoldPercentage <
                              Number(
                                competition.reward_wins.find(
                                  (reward) => !reward.reward_open
                                )?.prcnt_available
                              )
                              ? unlockMessage
                              : "ALL PRIZES UNLOCKED! GOODLUCK!"}

                            {"  "}
                            {"  "}
                            <a href="#reward-scroll">
                              <span className="view-tier">View prizes</span>
                            </a>
                          </h5>
                        </div>
                      </div>
                    )} */}

                    {
                      competition?.total_sell_tickets !=
                        competition?.total_ticket_sold &&
                        new Date(competition.draw_date) > new Date() ? (
                        <div
                          className={`onlin-entry   ${isClassActive ? "change-opacity" : ""
                            } ${disbaleTickets ? "change-opacity" : ""}  `}
                        >
                          <div className="online-entry-all">
                            <div className="input-div">
                              <div className="ticketNumebrInput">
                                <span className="ticketnumber">{quantity}</span>
                                <span className="ticketnumbername">
                                  Tickets
                                </span>
                              </div>
                            </div>

                            <div className="ticketSliderMainDiv">
                              <a
                                className="quantity__minus"
                                onClick={() =>
                                  handleQuantityChange("decrement")
                                }
                                style={{ backgroundColor: divColorlefts }}
                                onMouseDown={() => {
                                  setDivColorleft("#2CB4A5"); // Change to slider color when pressed
                                  setSliderColor("#2CB4A5");
                                }}
                                onMouseUp={() => {
                                  setDivColorleft("#2CB4A5"); // Revert to original color when released
                                  setSliderColor("#2CB4A5");
                                }}
                                onTouchStart={() => {
                                  setDivColorleft("#2CB4A5"); // For mobile touch
                                  setSliderColor("#2CB4A5");
                                }}
                                onTouchEnd={() => {
                                  setDivColorleft("#2CB4A5"); // For mobile touch release
                                  setSliderColor("#2CB4A5");
                                }}
                              >
                                <span>
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
                                </span>
                              </a>

                              <Slider
                                defaultValue={Number(quantity)}
                                aria-label="Default"
                                valueLabelDisplay="auto"
                                min={1}
                                max={Number(competition?.max_ticket_per_user)}
                                value={quantity}
                                onChange={handleChangeSlider}
                                onChangeCommitted={handleSliderChangeCommitted}
                                sx={{
                                  color: isSliderColor,
                                  "& .MuiSlider-thumb": {
                                    backgroundColor: isSliderColor,
                                  },
                                  "& .MuiSlider-track": {
                                    backgroundColor: isSliderColor,
                                  },
                                }}
                              />

                              <a
                                className="quantity__plus"
                                onClick={() =>
                                  handleQuantityChange("increment")
                                }
                                style={{ backgroundColor: divColorRights }}
                                onMouseDown={() => {
                                  setDivColorRight("#2CB4A5"); // Change to slider color when pressed
                                  setSliderColor("#2CB4A5");
                                }}
                                onMouseUp={() => {
                                  setDivColorRight("#2CB4A5"); // Revert to original color when released
                                  setSliderColor("#2CB4A5");
                                }}
                                onTouchStart={() => {
                                  setDivColorRight("#2CB4A5"); // For mobile touch
                                  setSliderColor("#2CB4A5");
                                }}
                                onTouchEnd={() => {
                                  setDivColorRight("#2CB4A5"); // For mobile touch release
                                  setSliderColor("#2CB4A5");
                                }}
                              >
                                <span>
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
                                </span>
                              </a>
                            </div>

                            <div className="addToBasketMainDiv">
                              <div className="entry-basket">
                                {isAccountLocked ? (
                                  <button
                                    type="button"
                                    className="add-entry-basket"
                                    onClick={(e) => {
                                      e.stopPropagation();
                                      showErrorToast(
                                        `Your account is locked for ${isAccountLockedPeriod}!`
                                      );
                                    }}
                                  >
                                    Add to Basket
                                  </button>
                                ) : disbaleTickets ? (
                                  <button
                                    type="button"
                                    className="add-entry-basket"
                                    data-bs-toggle="modal"
                                    onClick={(e) => {
                                      e.stopPropagation();
                                      showErrorToast(
                                        `Tickets are temporarily disabled for this competition.`
                                      );
                                    }}
                                  >
                                    Add to Basket
                                  </button>
                                ) : (
                                  <button
                                    type="button"
                                    className="add-entry-basket"
                                    data-bs-toggle="modal"
                                    onClick={() => {
                                      handleSetCompetition(quantity);
                                      handleModalOpen(competition);
                                      handleAddToCart(
                                        competition,
                                        isEnabledQuestion
                                      );
                                    }}
                                    disabled={disbaleTickets}
                                  >
                                    Add to Basket
                                  </button>
                                )}
                              </div>
                            </div>

                            <div className="entry-btm-txt">
                              <div className="entry-ticket">
                                <svg
                                  width="21"
                                  height="13"
                                  viewBox="0 0 21 13"
                                  fill="none"
                                  xmlns="http://www.w3.org/2000/svg"
                                >
                                  <path
                                    d="M20.5 5.17835V2.74219C20.5 1.77999 19.7437 1 18.8108 1H2.18914C1.25629 0.99996 0.5 1.77995 0.5 2.74215V5.19459C1.3577 5.32165 2.01773 6.08036 2.01773 7.0001C2.01773 7.91984 1.3577 8.67864 0.5 8.80537V11.2581C0.5 12.22 1.25629 13 2.18914 13H18.8108C19.7436 13 20.5 12.22 20.5 11.2581V8.82161C19.5614 8.77633 18.8135 7.97938 18.8135 7.00014C18.8136 6.02094 19.5615 5.224 20.5 5.17835ZM5.24668 11.9008H4.66555V10.3377H5.24668V11.9008ZM5.24668 9.1546H4.66555V7.59152H5.24668V9.1546ZM5.24668 6.40844H4.66555V4.845H5.24668V6.40844ZM5.24668 3.66228H4.66555V2.09916H5.24668V3.66228Z"
                                    fill="#8f9191"
                                  ></path>
                                </svg>
                              </div>
                              <div className="entry-ticket-txt">
                                <h4>
                                  {" "}
                                  <span>
                                    {competition.max_ticket_per_user}
                                  </span>{" "}
                                  Maximum Tickets Per Person
                                </h4>
                              </div>
                            </div>

                          </div>
                        </div>
                      ) : (
                        ""
                      )
                      // <div className="onlin-entry">
                      //   <div className="finish-btns-details-page"><button type="button" className="close-btn">
                      //     {competition?.total_sell_tickets == competition?.total_ticket_sold && new Date(competition.draw_date) > new Date() ? 'SOLD OUT' : 'CLOSED'}
                      //   </button></div>
                      // </div>
                    }
                    {competition?.total_sell_tickets !=
                      competition?.total_ticket_sold &&
                      new Date(competition.draw_date) > new Date() ? (
                      <div className="onlin-entry  onlin-entry-desktop">
                        <div className="online-entry-all" id="clickTabOpenDiv">
                          <div className="tab">
                            <button
                              className={`tablinks ${activeTab === 1 && "active"
                                }`}
                              onClick={() => {
                                handleActiveTab(1);
                                opacityUpTopSection(); // Add your new function here
                              }}
                            >
                              Online Entry
                            </button>
                            <button
                              className={`tablinks ${activeTab === 2 && "active"
                                }`}
                              onClick={() => {
                                handleActiveTab(2);
                                opacityDownTopSection(); // Add your new function here
                              }}
                            >
                              Free Postal Entry
                            </button>
                          </div>
                          {/* Tab content */}
                          {
                            // activetab === 1 && (
                            //   <div id="London" className="tabcontent">
                            //     <div className="online-entry-contnt">
                            //       <div className="online-entry-contnt-lft">
                            //         <div className="quantity">
                            //           <a
                            //             className="quantity__minus"
                            //             onClick={() =>
                            //               handleQuantityChange("decrement")
                            //             }
                            //           >
                            //             <span>
                            //               <svg
                            //                 width="15"
                            //                 height="3"
                            //                 viewBox="0 0 15 3"
                            //                 fill="none"
                            //                 xmlns="http://www.w3.org/2000/svg"
                            //               >
                            //                 <path
                            //                   d="M1.5 0.5H13.5C13.7652 0.5 14.0196 0.605357 14.2071 0.792893C14.3946 0.98043 14.5 1.23478 14.5 1.5C14.5 1.76522 14.3946 2.01957 14.2071 2.20711C14.0196 2.39464 13.7652 2.5 13.5 2.5H1.5C1.23478 2.5 0.98043 2.39464 0.792893 2.20711C0.605357 2.01957 0.5 1.76522 0.5 1.5C0.5 1.23478 0.605357 0.98043 0.792893 0.792893C0.98043 0.605357 1.23478 0.5 1.5 0.5Z"
                            //                   fill="#202323"
                            //                 ></path>
                            //               </svg>
                            //             </span>
                            //           </a>
                            //           <div className="input-div-bold">
                            //             <input
                            //               name="quantity"
                            //               type="text"
                            //               className="quantity__input"
                            //               value={`${quantity}`}
                            //               onChange={(
                            //                 e: React.ChangeEvent<HTMLInputElement>
                            //               ) => {
                            //                 const value = parseInt(e.target.value);
                            //                 if (!value) {
                            //                   setQuantity(0);
                            //                   return;
                            //                 }
                            //                 setQuantity(value);
                            //                 if (value > Number(competition.max_ticket_per_user)) {
                            //                   setQuantity(Number(competition.max_ticket_per_user));
                            //                 }
                            //               }}
                            //               // readOnly
                            //               style={{
                            //                 paddingLeft: `${inputPadding}px`,
                            //               }}
                            //             />
                            //             <span
                            //               style={{
                            //                 right: `${spanPadding}px`,
                            //                 // top: `${spanTopPos}px`,
                            //               }}
                            //             >
                            //               Tickets
                            //             </span>
                            //           </div>
                            //           {/* <div className="bold-tickets-txt">
                            //         <p>
                            //           <span>{quantity}</span>Tickets
                            //         </p>
                            //       </div> */}
                            //           <a
                            //             className="quantity__plus"
                            //             onClick={() =>
                            //               handleQuantityChange("increment")
                            //             }
                            //           >
                            //             <span>
                            //               <svg
                            //                 width="15"
                            //                 height="15"
                            //                 viewBox="0 0 15 15"
                            //                 fill="none"
                            //                 xmlns="http://www.w3.org/2000/svg"
                            //               >
                            //                 <path
                            //                   d="M8.83331 6.5V1.5C8.83331 1.23478 8.72796 0.98043 8.54042 0.792893C8.35288 0.605357 8.09853 0.5 7.83331 0.5C7.5681 0.5 7.31374 0.605357 7.12621 0.792893C6.93867 0.98043 6.83331 1.23478 6.83331 1.5V6.5H1.83331C1.5681 6.5 1.31374 6.60536 1.12621 6.79289C0.93867 6.98043 0.833313 7.23478 0.833313 7.5C0.833313 7.76522 0.93867 8.01957 1.12621 8.20711C1.31374 8.39464 1.5681 8.5 1.83331 8.5H6.83331V13.5C6.83331 13.7652 6.93867 14.0196 7.12621 14.2071C7.31374 14.3946 7.5681 14.5 7.83331 14.5C8.09853 14.5 8.35288 14.3946 8.54042 14.2071C8.72796 14.0196 8.83331 13.7652 8.83331 13.5V8.5H13.8333C14.0985 8.5 14.3529 8.39464 14.5404 8.20711C14.728 8.01957 14.8333 7.76522 14.8333 7.5C14.8333 7.23478 14.728 6.98043 14.5404 6.79289C14.3529 6.60536 14.0985 6.5 13.8333 6.5H8.83331Z"
                            //                   fill="#202323"
                            //                 ></path>
                            //               </svg>
                            //             </span>
                            //           </a>
                            //         </div>
                            //       </div>
                            //       <div className="online-entry-contnt-rgt">
                            //         <div className="entry-basket">
                            //           {
                            //             isAccountLocked ?
                            //               <button
                            //                 type="button"
                            //                 className="add-entry-basket"
                            //                 onClick={(e) => {
                            //                   e.stopPropagation();
                            //                   showErrorToast(`Your account is locked for ${isAccountLockedPeriod}!`);
                            //                 }}
                            //               >
                            //                 Add to Basket
                            //               </button>
                            //               :
                            //               <button
                            //                 type="button"
                            //                 className="add-entry-basket"
                            //                 data-bs-toggle="modal"
                            //                 // data-bs-target={
                            //                 //    isEnabledQuestion
                            //                 //     ? "#enter"
                            //                 //     : "#exampleModal-3"
                            //                 // }
                            //                 onClick={() => {
                            //                   handleSetCompetition(quantity);
                            //                   handleModalOpen(competition);
                            //                   handleAddToCart(competition, isEnabledQuestion);
                            //                   // if (!isEnabledQuestion) {
                            //                   //   handleAddToCart(competition);
                            //                   // }
                            //                 }}
                            //                 disabled={disbaleTickets}
                            //               >
                            //                 Add to Basket
                            //               </button>
                            //           }
                            //         </div>
                            //       </div>
                            //     </div>
                            //     <div className="entry-btm-txt">
                            //       <div className="entry-ticket">
                            //         <svg
                            //           width={21}
                            //           height={13}
                            //           viewBox="0 0 21 13"
                            //           fill="none"
                            //           xmlns="http://www.w3.org/2000/svg"
                            //         >
                            //           <path
                            //             d="M20.5 5.17835V2.74219C20.5 1.77999 19.7437 1 18.8108 1H2.18914C1.25629 0.99996 0.5 1.77995 0.5 2.74215V5.19459C1.3577 5.32165 2.01773 6.08036 2.01773 7.0001C2.01773 7.91984 1.3577 8.67864 0.5 8.80537V11.2581C0.5 12.22 1.25629 13 2.18914 13H18.8108C19.7436 13 20.5 12.22 20.5 11.2581V8.82161C19.5614 8.77633 18.8135 7.97938 18.8135 7.00014C18.8136 6.02094 19.5615 5.224 20.5 5.17835ZM5.24668 11.9008H4.66555V10.3377H5.24668V11.9008ZM5.24668 9.1546H4.66555V7.59152H5.24668V9.1546ZM5.24668 6.40844H4.66555V4.845H5.24668V6.40844ZM5.24668 3.66228H4.66555V2.09916H5.24668V3.66228Z"
                            //             fill="#8f9191"
                            //           />
                            //         </svg>
                            //       </div>
                            //       <div className="entry-ticket-txt">
                            //         <h4>
                            //           {" "}
                            //           <span>
                            //             {competition.max_ticket_per_user}
                            //           </span>{" "}
                            //           Maximum Tickets Per Person
                            //         </h4>
                            //       </div>
                            //     </div>
                            //   </div>
                            // )
                          }
                          {activeTab === 2 && (
                            <div id="Paris" className="tabcontent">
                              <div className="online-entry-contnt">
                                <div className="">
                                  <p className="title-free-postal" style={{ color: "white", fontWeight: "900", fontFamily: "Mozaic GEO" }}>FREE POSTAL ENTRY</p>
                                  <p
                                    style={{ color: "white" }}
                                    dangerouslySetInnerHTML={{
                                      __html: postalEntryInfo,
                                    }}
                                  ></p>
                                  <div className="entry-btm-txt">
                                    <div className="entry-ticket">
                                      <svg
                                        width="21"
                                        height="13"
                                        viewBox="0 0 21 13"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                      >
                                        <path
                                          d="M20.5 5.17835V2.74219C20.5 1.77999 19.7437 1 18.8108 1H2.18914C1.25629 0.99996 0.5 1.77995 0.5 2.74215V5.19459C1.3577 5.32165 2.01773 6.08036 2.01773 7.0001C2.01773 7.91984 1.3577 8.67864 0.5 8.80537V11.2581C0.5 12.22 1.25629 13 2.18914 13H18.8108C19.7436 13 20.5 12.22 20.5 11.2581V8.82161C19.5614 8.77633 18.8135 7.97938 18.8135 7.00014C18.8136 6.02094 19.5615 5.224 20.5 5.17835ZM5.24668 11.9008H4.66555V10.3377H5.24668V11.9008ZM5.24668 9.1546H4.66555V7.59152H5.24668V9.1546ZM5.24668 6.40844H4.66555V4.845H5.24668V6.40844ZM5.24668 3.66228H4.66555V2.09916H5.24668V3.66228Z"
                                          fill="#8f9191"
                                        ></path>
                                      </svg>
                                    </div>
                                    <div className="entry-ticket-txt">
                                      <h4>
                                        {" "}
                                        <span>
                                          {competition.max_ticket_per_user}
                                        </span>{" "}
                                        Maximum Tickets Per Person
                                      </h4>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              {/* <div className="entry-btm-txt">
                                  <div className="entry-ticket">
                                    <svg
                                      width={21}
                                      height={13}
                                      viewBox="0 0 21 13"
                                      fill="none"
                                      xmlns="http://www.w3.org/2000/svg"
                                    >
                                      <path
                                        d="M20.5 5.17835V2.74219C20.5 1.77999 19.7437 1 18.8108 1H2.18914C1.25629 0.99996 0.5 1.77995 0.5 2.74215V5.19459C1.3577 5.32165 2.01773 6.08036 2.01773 7.0001C2.01773 7.91984 1.3577 8.67864 0.5 8.80537V11.2581C0.5 12.22 1.25629 13 2.18914 13H18.8108C19.7436 13 20.5 12.22 20.5 11.2581V8.82161C19.5614 8.77633 18.8135 7.97938 18.8135 7.00014C18.8136 6.02094 19.5615 5.224 20.5 5.17835ZM5.24668 11.9008H4.66555V10.3377H5.24668V11.9008ZM5.24668 9.1546H4.66555V7.59152H5.24668V9.1546ZM5.24668 6.40844H4.66555V4.845H5.24668V6.40844ZM5.24668 3.66228H4.66555V2.09916H5.24668V3.66228Z"
                                        fill="#8f9191"
                                      />
                                    </svg>
                                  </div>
                                  <div className="entry-ticket-txt">
                                    <h4>
                                      {" "}
                                      <span>
                                        {competition.max_ticket_per_user}
                                      </span>{" "}
                                      Maximum Tickets Per Person
                                    </h4>
                                  </div>
                                </div> */}
                            </div>
                          )}
                        </div>
                      </div>
                    ) : (
                      <div className="onlin-entry">
                        <div className="finish-btns-details-page">
                          <button type="button" className="close-btn">
                            {competition?.total_sell_tickets ==
                              competition?.total_ticket_sold &&
                              new Date(competition.draw_date) > new Date()
                              ? "SOLD OUT"
                              : "CLOSED"}
                          </button>
                        </div>
                      </div>
                    )}

                    <div className="oods-calculator">
                      <div className="oods-calculator-all">
                        <div className="oods-calculator-head">
                          <div className="oods-ixon">
                            <svg
                              width="21"
                              height="20"
                              viewBox="0 0 21 20"
                              fill="none"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <g clipPath="url(#clip0_565_10276)">
                                <path
                                  d="M11.5 0C12.2956 0 13.0587 0.31607 13.6213 0.87868C14.1839 1.44129 14.5 2.20435 14.5 3V6H17.5C18.2956 6 19.0587 6.31607 19.6213 6.87868C20.1839 7.44129 20.5 8.20435 20.5 9V17C20.5 17.7956 20.1839 18.5587 19.6213 19.1213C19.0587 19.6839 18.2956 20 17.5 20H9.5C8.70435 20 7.94129 19.6839 7.37868 19.1213C6.81607 18.5587 6.5 17.7956 6.5 17V14H3.5C2.70435 14 1.94129 13.6839 1.37868 13.1213C0.81607 12.5587 0.5 11.7956 0.5 11V3C0.5 2.20435 0.81607 1.44129 1.37868 0.87868C1.94129 0.31607 2.70435 0 3.5 0L11.5 0ZM10.5 15C10.2348 15 9.98043 15.1054 9.79289 15.2929C9.60536 15.4804 9.5 15.7348 9.5 16C9.5 16.2652 9.60536 16.5196 9.79289 16.7071C9.98043 16.8946 10.2348 17 10.5 17C10.7652 17 11.0196 16.8946 11.2071 16.7071C11.3946 16.5196 11.5 16.2652 11.5 16C11.5 15.7348 11.3946 15.4804 11.2071 15.2929C11.0196 15.1054 10.7652 15 10.5 15ZM16.5 15C16.2348 15 15.9804 15.1054 15.7929 15.2929C15.6054 15.4804 15.5 15.7348 15.5 16C15.5 16.2652 15.6054 16.5196 15.7929 16.7071C15.9804 16.8946 16.2348 17 16.5 17C16.7652 17 17.0196 16.8946 17.2071 16.7071C17.3946 16.5196 17.5 16.2652 17.5 16C17.5 15.7348 17.3946 15.4804 17.2071 15.2929C17.0196 15.1054 16.7652 15 16.5 15ZM13.5 12C13.2348 12 12.9804 12.1054 12.7929 12.2929C12.6054 12.4804 12.5 12.7348 12.5 13C12.5 13.2652 12.6054 13.5196 12.7929 13.7071C12.9804 13.8946 13.2348 14 13.5 14C13.7652 14 14.0196 13.8946 14.2071 13.7071C14.3946 13.5196 14.5 13.2652 14.5 13C14.5 12.7348 14.3946 12.4804 14.2071 12.2929C14.0196 12.1054 13.7652 12 13.5 12ZM10.5 9C10.2348 9 9.98043 9.10536 9.79289 9.29289C9.60536 9.48043 9.5 9.73478 9.5 10C9.5 10.2652 9.60536 10.5196 9.79289 10.7071C9.98043 10.8946 10.2348 11 10.5 11C10.7652 11 11.0196 10.8946 11.2071 10.7071C11.3946 10.5196 11.5 10.2652 11.5 10C11.5 9.73478 11.3946 9.48043 11.2071 9.29289C11.0196 9.10536 10.7652 9 10.5 9ZM16.5 9C16.2348 9 15.9804 9.10536 15.7929 9.29289C15.6054 9.48043 15.5 9.73478 15.5 10C15.5 10.2652 15.6054 10.5196 15.7929 10.7071C15.9804 10.8946 16.2348 11 16.5 11C16.7652 11 17.0196 10.8946 17.2071 10.7071C17.3946 10.5196 17.5 10.2652 17.5 10C17.5 9.73478 17.3946 9.48043 17.2071 9.29289C17.0196 9.10536 16.7652 9 16.5 9ZM5.013 8.993C4.74778 8.993 4.49343 9.09836 4.30589 9.28589C4.11836 9.47343 4.013 9.72778 4.013 9.993C4.013 10.2582 4.11836 10.5126 4.30589 10.7001C4.49343 10.8876 4.74778 10.993 5.013 10.993C5.27822 10.993 5.53257 10.8876 5.72011 10.7001C5.90764 10.5126 6.013 10.2582 6.013 9.993C6.013 9.72778 5.90764 9.47343 5.72011 9.28589C5.53257 9.09836 5.27822 8.993 5.013 8.993ZM5.013 5.993C4.74778 5.993 4.49343 6.09836 4.30589 6.28589C4.11836 6.47343 4.013 6.72778 4.013 6.993C4.013 7.25822 4.11836 7.51257 4.30589 7.70011C4.49343 7.88764 4.74778 7.993 5.013 7.993C5.27822 7.993 5.53257 7.88764 5.72011 7.70011C5.90764 7.51257 6.013 7.25822 6.013 6.993C6.013 6.72778 5.90764 6.47343 5.72011 6.28589C5.53257 6.09836 5.27822 5.993 5.013 5.993ZM5.013 2.993C4.74778 2.993 4.49343 3.09836 4.30589 3.28589C4.11836 3.47343 4.013 3.72778 4.013 3.993C4.013 4.25822 4.11836 4.51257 4.30589 4.70011C4.49343 4.88764 4.74778 4.993 5.013 4.993C5.27822 4.993 5.53257 4.88764 5.72011 4.70011C5.90764 4.51257 6.013 4.25822 6.013 3.993C6.013 3.72778 5.90764 3.47343 5.72011 3.28589C5.53257 3.09836 5.27822 2.993 5.013 2.993ZM10.013 2.993C9.74778 2.993 9.49343 3.09836 9.30589 3.28589C9.11836 3.47343 9.013 3.72778 9.013 3.993C9.013 4.25822 9.11836 4.51257 9.30589 4.70011C9.49343 4.88764 9.74778 4.993 10.013 4.993C10.2782 4.993 10.5326 4.88764 10.7201 4.70011C10.9076 4.51257 11.013 4.25822 11.013 3.993C11.013 3.72778 10.9076 3.47343 10.7201 3.28589C10.5326 3.09836 10.2782 2.993 10.013 2.993Z"
                                  fill="white"
                                />
                              </g>
                              <defs>
                                <clipPath id="clip0_565_10276">
                                  <rect
                                    width="20"
                                    height="20"
                                    fill="white"
                                    transform="translate(0.5)"
                                  />
                                </clipPath>
                              </defs>
                            </svg>
                          </div>
                          <div className="oods-head-txt">
                            <h2>MAIN DRAW ODDS CALCULATOR</h2>
                          </div>
                        </div>
                        <div className="ood-calculator-data">
                          <div className="ood-calculator-data-one">
                            <div className="ood-calculator-data-left">
                              <p>🐟 Your current odds are </p>
                            </div>
                            <div className="ood-bts">
                              <p>
                                {/* 1 <span className="slash">/</span> {currentOdds} */}
                                {currentOdds}
                              </p>
                            </div>
                          </div>
                          <div className="ood-calculator-data-two">
                            <div className="ood-calculator-data-left">
                              <p>
                                🎣 <span>{suggested_tickets}</span> more tickets
                                and they go up to{" "}
                              </p>
                            </div>
                            <div className="ood-btss">
                              <p>
                                {/* 1 <span className="slashh">/</span>{" "}
                                {recomnmendedOdds} */}
                                {recomnmendedOdds}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div className="mob-win-twenty-bait-main-bottom">
                      <div className="mob-win-twenty-bait-main-bottom-all">
                        {liveDrawInfoJsxMob()}
                        <div className="mob-win-twenty-bait-main-bottom-second">
                          <h2>
                            {" "}
                            <span>{parseInt(competition.total_sell_tickets) -
                              parseInt(competition.total_ticket_sold)} </span>{" "}
                            Tickets Available
                          </h2>
                        </div>
                        <div className="mob-win-twenty-bait-main-bottom-second-one">
                          <h2>
                            Maximum{" "}
                            <span>{competition.max_ticket_per_user} </span>{" "}
                            Tickets Per Person
                          </h2>
                        </div>
                      </div>
                    </div>
                    <div className="all-order-are">
                      <p>
                        All orders are subject to our{" "}
                        <Link
                          to="/legal-terms"
                          onClick={() =>
                            localStorage.setItem(LEGAL_TERMS_ACTIVE_INDEX, "1")
                          }
                        >
                          Terms
                        </Link>{" "}
                        &amp;{" "}
                        <Link
                          to="/legal-terms"
                          onClick={() =>
                            localStorage.setItem(LEGAL_TERMS_ACTIVE_INDEX, "3")
                          }
                        >
                          Privacy.
                        </Link>{" "}
                        For free postal entry route{" "}
                        <button
                          style={{
                            background: "transparent",
                            border: "none",
                            textDecoration: "underline",
                            color: "#fff",
                          }}
                          onClick={() => handleActiveTab(2)}
                        >
                          see here.
                        </button>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Details;
