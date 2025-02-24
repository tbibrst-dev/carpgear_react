import { useEffect, useState } from "react";
import {
  AUTH_TOKEN_KEY,
  CART_HEADER,
  COMPS_QUEST,
  NONCE_KEY,
  NONCE_TIMESTAMP,
  TOKEN,
  UPDATE_CART_KEY,
  cartError,
  checkAuth,
  encodeBase64String,
  navigateCompetition,
  fetchNonceValue
} from "../../utils";
import { useDispatch } from "react-redux";
import {
  CompetitionType,
  InstantWinDetails,
  OrderDetails,
  QunatityType,
} from "../../types";
import { emptyCart } from "../../redux/slices/cartSlice";
import ConfirmationTwo from "../../components/Confirmation-two/Confirmation-two";
import toast from "react-hot-toast";
import axios from "axios";
import Loader from "../../common/Loader";

const ConfirmationPage = () => {
  const params = new URLSearchParams(location.search);
  const dispatch = useDispatch();
  const [orderDetails, setOrderDetails] = useState<OrderDetails>(
    {} as OrderDetails
  );
  const [quantities, setQuantities] = useState<QunatityType>({});
  const [competitions, setCompetitions] = useState<CompetitionType[]>([]);
  const [cartKeys, setCartKeys] = useState<{ [key: number]: { key: string } }>(
    {}
  );

  const [isFetching, setIsFetching] = useState<boolean>(true);
  const [instantWins, setInstantWins] = useState<InstantWinDetails[]>([]);

  const [isInstantWinner, setIsInstantWinner] = useState<boolean>(false);

  function formatDate(dateString: string) {
    const date = new Date(dateString);
    let day: string | number = date.getDate();
    let month: string | number = date.getMonth() + 1;
    let year: string | number = date.getFullYear();

    year = year.toString().slice(-2);
    day = (day < 10 ? "0" : "") + day;
    month = (month < 10 ? "0" : "") + month;

    const formattedDate = day + "." + month + "." + year;
    return formattedDate;
  }

  const cart_header = localStorage.getItem(CART_HEADER);
  let nonce = localStorage.getItem(NONCE_KEY);
  const storedTimestamp = localStorage.getItem(NONCE_TIMESTAMP) as string;

  useEffect(() => {

    setTimeout(() => {
      console.log('function fire');
      emptycart();
    }, 2000);

    //* decode the query params string and get the order details
    function orderConfirmationDetails() {
      const encodedString = params.get("order");
      const decodedString = encodeBase64String(encodedString ?? "");
      const parsedObject: OrderDetails = JSON.parse(decodedString);
      const orderDate = formatDate(parsedObject.order_created.date);
      const encodeToken = encodeBase64String(parsedObject.token);
      localStorage.setItem(AUTH_TOKEN_KEY, encodeToken);
      checkAuth(parsedObject.token, dispatch);
      setOrderDetails({
        ...parsedObject,
        order_created: {
          date: orderDate,
          timezone: parsedObject.order_created.timezone,
          timezone_type: parsedObject.order_created.timezone_type,
        },
      });
      if (parsedObject.instant_winner) {
        localStorage.removeItem(COMPS_QUEST);
        fetchInstantWinsDetails(
          parsedObject.token,
          parsedObject.comps,
          parsedObject.instant_wins
        );
      } else {
        fetchUpsellsCompetition(parsedObject.comps, parsedObject.categories);
      }
    }

    //? fetch upsell competition if user is not instant winner
    const fetchUpsellsCompetition = async (
      comps: string,
      categories: string
    ) => {
      try {
        const response = await axios.post(
          "?rest_route=/api/v1/getOtherComps",
          {
            limit: 5,
            order: "asc",
            order_by: "draw_date",
            ids: comps,
            categories,
            status: "Open",
          },
          { headers: { Authorization: TOKEN } }
        );

        if (response.data.success === "true") {
          console.log(response);
          setCompetitions(response.data.data);
          handleQuantitySetter(response.data.data);
        }
      } catch (error) {
        console.log(error);
      } finally {
        setIsFetching(false);
      }
    };

    //TODO: fetch instant win details if the user is instant winner
    const fetchInstantWinsDetails = async (
      token: string,
      competitions: string,
      instant_wins: string
    ) => {
      try {
        const response = await axios.post(
          "?rest_route=/api/v1/check_competition_prize",
          {
            token,
            competitions,
            instant_wins,
          },
          {
            headers: {
              Authorization: TOKEN,
            },
          }
        );
        if (response.data.success === "true") {
          setInstantWins(response.data.data);
          setIsInstantWinner(response.data.won_instant);
        }
      } catch (error) {
        console.log(error);
      } finally {
        setIsFetching(false);
      }
    };

    fetchInstantWinsDetails;
    orderConfirmationDetails();
  }, []);

  useEffect(() => {
    const keys = localStorage.getItem(UPDATE_CART_KEY) as string;
    const parsedKeys = keys ? JSON.parse(keys) : {};
    setCartKeys(parsedKeys);
  }, []);
  //* quanitity setter function
  const handleQuantitySetter = (competitions: CompetitionType[]) => {
    const initialQuantities: QunatityType = {};
    competitions.forEach((competition) => {
      initialQuantities[competition.id] = parseInt(competition.quantity);
      setQuantities(initialQuantities);
    });
  };

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
      toast.error(cartError());
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
    if (isNaN(value)) return;
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


  async function emptycart() {
    const keys = localStorage.getItem(UPDATE_CART_KEY) as string;
    const parsedKeys = keys ? JSON.parse(keys) : {};
    console.log('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++parsedKeys', parsedKeys)
    // const cartKeyss = Object.values(parsedKeys); // Ensure cartKeys is an array
    const cartKeysArray: { key: string }[] = Object.values(parsedKeys);
    console.log('+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++cartKeyss', cartKeysArray)
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
    const deleteURL = import.meta.env.VITE_REMOVE_ITEM_API;
    // console.log('cartKeys', cartKeyss);

    try {
      const removePromises = cartKeysArray.map((cartKey) => {
        return axios.post(
          deleteURL,
          {
            key: cartKey.key,
            cart_header,
            nonce,
          },
          {
            headers: {
              "X-WC-Store-api-nonce": nonce,
              Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`,
            },
          }
        );
      });

      // Wait for all promises to resolve
      await Promise.all(removePromises);


      // localStorage.removeItem(CART_HEADER);
      // localStorage.removeIte;

    } catch (error) {
      console.error('Error removing all items from cart:', error);
    } finally {
      console.log('Done:');
      localStorage.removeItem(CART_HEADER);
      localStorage.removeItem(NONCE_KEY);
      localStorage.removeItem(NONCE_TIMESTAMP);
      localStorage.removeItem(COMPS_QUEST);
      dispatch(emptyCart());

    }
  }


  if (isFetching) {
    return <Loader />;
  }

  return (
    <div>
      <ConfirmationTwo
        order={orderDetails}
        cartKeys={cartKeys}
        navigateCompetition={navigateCompetition}
        handleQuantityChange={handleQuantityChange}
        handleQuantityChangeInput={handleQuantityChangeInput}
        quantities={quantities}
        competitions={competitions}
        isFetching={isFetching}
        isInstantWinner={isInstantWinner}
        instantWinDetails={instantWins}
      />
    </div>
  );
};

export default ConfirmationPage;
