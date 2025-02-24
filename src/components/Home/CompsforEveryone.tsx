import { useGetCompetitionMutation } from "../../redux/queries";
import { useEffect, useState,useRef } from "react";
import { CompetitionType, QunatityType } from "../../types";
import Loader from "../../common/Loader";
import SwiperSlideComponent from "./SwiperSlide";
import { Link } from "react-router-dom";
import { useSelector } from "react-redux";
import {
  UPDATE_CART_KEY,
   handleAddToCart,
  navigateCompetition,
} from "../../utils";
// import { toast } from "react-hot-toast";
import { RootState } from "../../redux/store";
import { Element } from 'react-scroll';
import { showErrorToast } from '../../showErrorToast';




const CACHE_KEY = "bigGear";
// const CACHE_TIMEOUT = 1 * 1000; // 10 minutes in milliseconds
const CACHE_TIMEOUT = import.meta.env.GLOBAL_CACHE_TIMEOUT


const CompsforEveryone = () => {

  const swiperRef = useRef<any | null>(null);
  const [canScrollPrev, setCanScrollPrev] = useState(false);
  const [canScrollNext, setCanScrollNext] = useState(true);

  const handleScrollNext = () => {
    if (swiperRef.current) {
      swiperRef.current.slideNext();
    }
  };

  const handleScrollPrev = () => {
    if (swiperRef.current) {
      swiperRef.current.slidePrev();
    }
  };

  const onSwiperInit = (swiper: any) => {
    swiperRef.current = swiper;
    swiper.on('slideChange', () => {
      setCanScrollPrev(!swiper.isBeginning);
      setCanScrollNext(!swiper.isEnd);
    });
  };

  const [mutate, { isLoading }] = useGetCompetitionMutation();
  const [quantities, setQuantities] = useState<QunatityType>({});
  const [competitions, setCompetitions] = useState<CompetitionType[]>([]);
  const [cartKeys, setCartKeys] = useState<{ [key: number]: { key: string } }>(
    {}
  );
  const cartItems = useSelector((state: RootState) => state.cart.cartItems);
  useEffect(() => {
    const keys = localStorage.getItem(UPDATE_CART_KEY) as string;
    const parsedKeys = keys ? JSON.parse(keys) : {};
    setCartKeys(parsedKeys);
  }, [cartItems]);

  //* quanitity setter function
  const handleQuantitySetter = (competitions: CompetitionType[]) => {
    const initialQuantities: QunatityType = {};
    competitions.forEach((competition) => {
      initialQuantities[competition.id] = parseInt(competition.quantity);
      setQuantities(initialQuantities);
    });
  };

  useEffect(() => {
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
    const fetchInstantWinCompetitons = async () => {
      try {
        const res: any = await mutate({
          limit: 5,
          category: "the_big_gear",
          order_by: "id",
          order: "desc",
          token: import.meta.env.VITE_TOKEN,
          status: "Open",
        });
        if (!res.error) {
          setCompetitions(res.data.data);
          handleQuantitySetter(res.data.data);
          localStorage.setItem(CACHE_KEY, JSON.stringify(res.data.data));
          localStorage.setItem(
            `${CACHE_KEY}_timestamp`,
            currentTime.toString()
          );
        }
      } catch (error) {
        console.log(error);
      }
    };
    fetchInstantWinCompetitons();
  }, []);

  //todo handle quantity change of a competition
  const handleQuantityChange = (
    id: number,
    newQuantity: number,
    toBeUpdated: string
  ) => {
    const competition = competitions.find((comp) => comp.id === id);
    if (
      Number(competition?.max_ticket_per_user) === quantities[id] &&
      toBeUpdated === "increment"
    ) {
      // toast.error(cartError());
      showErrorToast('You have entered the maximum number of tickets');

      return;
    }
    const currentQty = quantities[id];
    const newQty =
      toBeUpdated === "increment"
        ? currentQty + newQuantity
        : currentQty <= 1
        ? currentQty
        : currentQty - newQuantity;
    setQuantities({ ...quantities, [id]: newQty });
  };

  const handleQuantityChangeInput = (id: number, value: number) => {
    let parsedValue: number;
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

  if (isLoading) {
    return <Loader />;
  }

  return (
    <Element name="comps-for-all-section-scroll">

    <div>
      <div className="instant-all-opert" id="comps-for-everyone">
        <div className="container">
          <div className="Instant-wins-two-heading">
            <div className="Instant-wins-two-center">
              <h2>Comps for Everyone</h2>
            </div>
          </div>

          <div className="instant-view-all">
          <button
              className={`scroll-button left ${canScrollPrev ? '' : 'disabled'}`}
              onClick={handleScrollPrev}
              disabled={!canScrollPrev}
            >
              <svg width="30" height="34" viewBox="0 0 30 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.4564 17L21.3958 25.9394C21.7248 26.28 21.9068 26.7362 21.9027 27.2097C21.8986 27.6833 21.7087 28.1362 21.3738 28.4711C21.039 28.8059 20.586 28.9958 20.1125 28.9999C19.639 29.004 19.1828 28.822 18.8422 28.493L8.62597 18.2768C8.28741 17.9381 8.09721 17.4789 8.09721 17C8.09721 16.5211 8.28741 16.0619 8.62597 15.7232L18.8422 5.50695C19.1828 5.17798 19.639 4.99595 20.1125 5.00007C20.586 5.00418 21.039 5.19411 21.3738 5.52895C21.7087 5.86379 21.8986 6.31674 21.9027 6.79026C21.9068 7.26377 21.7248 7.71996 21.3958 8.06056L12.4564 17Z" fill="#EEC273" />
              </svg>
            </button>

            <div>
              <Link to="/competitions/singular_competition">View All</Link>

            </div>
            <button
              className={`scroll-button right ${canScrollNext ? '' : 'disabled'}`}
              onClick={handleScrollNext}
              disabled={!canScrollNext}
            >
              <svg width="30" height="34" viewBox="0 0 30 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.5436 17L8.60418 8.06056C8.27521 7.71995 8.09318 7.26377 8.0973 6.79025C8.10141 6.31674 8.29134 5.86379 8.62618 5.52895C8.96102 5.19411 9.41397 5.00418 9.88748 5.00007C10.361 4.99595 10.8172 5.17798 11.1578 5.50695L21.374 15.7232C21.7126 16.0619 21.9028 16.5211 21.9028 17C21.9028 17.4789 21.7126 17.9381 21.374 18.2768L11.1578 28.493C10.8172 28.822 10.361 29.004 9.88748 28.9999C9.41397 28.9958 8.96102 28.8059 8.62618 28.471C8.29134 28.1362 8.10141 27.6833 8.0973 27.2097C8.09318 26.7362 8.27521 26.28 8.60418 25.9394L17.5436 17Z" fill="#EEC273" />
              </svg>
            </button>
          </div>
          {competitions.length > 0 ? (
            <SwiperSlideComponent
              handleQuantityChange={handleQuantityChange}
              quantities={quantities}
              competitions={competitions}
              scrollbarId="big-gear"
              category="the_big_gear"
              navigateCompetition={navigateCompetition}
              handleAddToCart={handleAddToCart}
              handleQuantitiyChangeInput={handleQuantityChangeInput}
              cartKeys={cartKeys}
              onSwiperInit={onSwiperInit}

            />
          ) : (
            <div style={{ margin: "150px 0" }}>
              <h4
                style={{
                  color: "white",
                  textAlign: "center",
                }}
              >
                The Competitions are not available right now!
              </h4>
            </div>
          )}
        </div>
      </div>
    </div>

    </Element>

  );
};

export default CompsforEveryone;
