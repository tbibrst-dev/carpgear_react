import "./main.css";
import { Suspense, lazy, useEffect, useState } from "react";
import Loader from "./common/Loader";
import { Route, Routes, useLocation, useNavigate } from "react-router-dom";
import routes from "./routes";
import ScrollOnMount from "./common/ScrollOnMount";
import Home from "./pages/Home/Home";
import axios from "axios";
// import TawkMessengerReact from '@tawk.to/tawk-messenger-react';
const ResponsibleGaming = lazy(
  () => import("./components/Account/Responsible-Gaming")
);
import { useDispatch, useSelector } from "react-redux";
import {
  setIsAuthenticating,
  setPurchasedTickets,
  setUserState,
} from "./redux/slices/userSlice";
import {
  ANNOUCMENT,
  AUTH_TOKEN_KEY,
  // CART_HEADER,  // only  for local and stack
  LIVE_DRAW_INFO,
  MAIN_COMPETITION_INFO,
  NONCE_KEY,
  SUGGESTED_TICKETS,
  TOKEN,
  UPDATE_CART_KEY,
  DESKTOP_HEIGHT,
  MOBILE_HEIGHT,
  TABLET_HEIGHT,
  decryptToken,
  encryptToken,
  SHOW_QUESTION,
  SLIDER_SPEED
} from "./utils";
import { addToCart, isAddingToCart } from "./redux/slices/cartSlice";
import { Toaster } from "react-hot-toast";
import { RootState } from "./redux/store";
import UserDetails from "./components/Account/UserPage";
import UserBillingDetails from "./components/Account/UserBillingDetails";
import Points from "./components/Account/Points";
import Cred from "./components/Account/Cred";
import Coupons from "./components/Account/Coupons";
import OrdersPage from "./components/Account/OrdersPage";
import OrdersDetailPage from "./components/Account/OrdersDetailPage";

import CookieConsent, { resetCookieConsentValue } from "react-cookie-consent";
import SocialProof from "./components/SocialProof/socialproof";
import InjectFrontendScripts from './InjectFrontendScripts';
import UserFinancialDetails from "./components/Account/UserFinancialDetails";




const DefaultLayout = lazy(() => import("./layout/DefaultLayout"));
const AccountPage = lazy(() => import("./pages/AccountPage/AccountPage"));
const Account = lazy(() => import("./components/Account/Account"));

axios.defaults.baseURL = import.meta.env.VITE_SERVER_URL;

function App() {
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const { user } = useSelector((state: RootState) => state.userReducer);
  const [, setCartKeys] = useState<{ [key: number]: { key: string } }>({});
  const { pathname } = useLocation();
  useEffect(() => {
    const keys = localStorage.getItem(UPDATE_CART_KEY) as string;
    const parsedKeys = keys ? JSON.parse(keys) : {};
    setCartKeys(parsedKeys);
  }, []);

  const checkAuth = async (token: string) => {
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
      dispatch(setIsAuthenticating(false));
    }
  };

  useEffect(() => {
    const nonce = localStorage.getItem(NONCE_KEY);
    // const cart_header = localStorage.getItem(CART_HEADER); // only for local and stack

    const fetchCartDetails = async () => {
      dispatch(isAddingToCart(true));
      try {

        // const URL = "?rest_route=/wc/store/v1/cart/items";
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

        // console.log("initial cart", res);
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
    if (!pathname.includes("confirmation")) {
      fetchCartDetails();
    }

    async function fetchPurchasedCompetitions() {
      try {
        const response = await axios.post(
          "?rest_route=/api/v1/get_purchased_competition",
          {
            token: user?.token,
          },
          {
            headers: {
              Authorization: TOKEN,
            },
          }
        );
        dispatch(setPurchasedTickets(response.data.data));
      } catch (error) {
        console.log(error);
      }
    }

    if (user) {
      fetchPurchasedCompetitions();
    }
  }, [user]);

  // console.log("purchased tickets", purchasedTickets);
  // const [scriptText, setScriptText] = useState('');
  const [scriptContent, setScriptContent] = useState<string>('');
  useEffect(() => {
    const fetchGlobalSetting = async () => {
      try {
        const response = await axios.get("?rest_route=/api/v1/getsettings", {
          headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` },
        });

        if (response.data.success) {
          localStorage.setItem(
            SUGGESTED_TICKETS,
            response.data.data.suggested_tickets
          );
          localStorage.setItem(ANNOUCMENT, response.data.data.announcement);
          localStorage.setItem(
            LIVE_DRAW_INFO,
            response.data.data.live_draw_info
          );
          localStorage.setItem(
            MAIN_COMPETITION_INFO,
            response.data.data.main_competition
          );
          localStorage.setItem(
            DESKTOP_HEIGHT,
            response.data.data.slider_height_desktop
          );
          localStorage.setItem(
            MOBILE_HEIGHT,
            response.data.data.slider_height_mobile
          );
          localStorage.setItem(
            TABLET_HEIGHT,
            response.data.data.slider_height_tablet
          );
          localStorage.setItem(
            SHOW_QUESTION,
            response.data.data.show_question
          );

          localStorage.setItem(
            SLIDER_SPEED,
            response.data.data.slider_speed
          );

          localStorage.setItem(
            'rewardprizetext',
            response.data.data.reward_prize_info
          );

          setScriptContent(response.data.data.frontend_scripts); 

        }
      } catch (error) {
        console.log(error);
      }
    };
    fetchGlobalSetting();
  }, []);

  useEffect(() => {
    const fetchALLWinnersAndPrizeValue = async () => {
      try {
        const response = await axios.get("?rest_route=/api/v1/get_realtime_winners_prize_value", {
          headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` },
        });

        if (response.data.success) {
       

          localStorage.setItem(
            'totalPrizeValue',
            response.data.totalPrizeValue
          );

          localStorage.setItem(
            'totalWinner',
            response.data.totalWinner
          );

        }
      } catch (error) {
        console.log(error);
      }
    };
    fetchALLWinnersAndPrizeValue();
  }, []);
  
  useEffect(() => {
    const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
    if (encodedToken) {
      const token = decryptToken(encodedToken);
      checkAuth(token);
    }
  }, []);



  return (
    <>
      <Toaster
        position="top-right"
        containerClassName="toaster-container"
        toastOptions={{
          error: {
            style: {
              backgroundColor: "#fe5042",
              color: "#fff",
            },
            iconTheme: {
              primary: "#fe5042",
              secondary: "#fff",
            },
          },
          success: {
            style: {
              backgroundColor: "green",
              color: "#fff",
            },
            iconTheme: {
              primary: "green",
              secondary: "#fff",
            },
          },
          duration: 4000,
        }}
      />
      <ScrollOnMount />
      <InjectFrontendScripts scriptContent={scriptContent} />
      <SocialProof></SocialProof>
      <div className="cookieconsnet">
        <CookieConsent
          containerClasses="cookie-notice-container"
          enableDeclineButton
          declineButtonText="Privacy Policy"
          location="bottom"
          buttonText="OK"
          cookieName="cggcookie"
          style={{ background: "#2B373B" }}
          buttonStyle={{
            color: "#2B373B",
            fontSize: "15px",
            fontWeight: "600",
            borderRadius: "4px",
            padding: "8px 8px 5px 8px",
            background: "#ffbb41",
            margin: "0 6px  0 6px",
          }}
          declineButtonStyle={{
            color: "#2B373B",
            fontSize: "15px",
            fontWeight: "600",
            borderRadius: "4px",
            padding: "8px 8px 5px 8px",
            background: "#ffbb41",
            margin: "0 6px  0 6px",
          }}
          expires={7}
          overlay
          setDeclineCookie={false}
          buttonClasses="accept_button"
          flipButtons
          hideOnDecline={false}
          onDecline={() => {
            resetCookieConsentValue();
            navigate("/legal-terms?tab=3");
          }}
          contentClasses="cn-text-container"
          buttonWrapperClasses="cn-buttons-container"
        >
          We use cookies to ensure that we give you the best experience on our
          app & website. If you continue we will assume you accept our updated
          Policy.
        </CookieConsent>
      </div>

      {/* <TawkMessengerReact
                propertyId="667bed64eaf3bd8d4d1481ee"
                widgetId="1i1a1ekda"/> */}
      <Routes>
        <Route
          path="/"
          element={
            <Suspense fallback={<Loader />}>
              <DefaultLayout />
            </Suspense>
          }
        >
          <Route index element={<Home />} />
          {routes.map((route) => {
            const { path, name, component: Component } = route;
            return (
              <Route
                key={name}
                path={path}
                element={
                  <Suspense fallback={<Loader />}>
                    <Component />
                  </Suspense>
                }
              />
            );
          })}
          <Route path="/account" element={<AccountPage />}>
            <Route index element={<Account />} />
            <Route path="/account/responsible/gaming" element={<ResponsibleGaming />} />
            <Route path="/account/points" element={<Points />} />
            <Route path="/account/cred" element={<Cred />} />
            <Route path="/account/coupons" element={<Coupons />} />
            <Route path="/account/details" element={<UserDetails />} />
            <Route path="/account/financial-details" element={<UserFinancialDetails />} />
            <Route path="/account/billing" element={<UserBillingDetails />} />
            <Route path="/account/recent/orders" element={<OrdersPage />} />
            <Route path="/account/recent/orders/order-detail/:order_id" element={<OrdersDetailPage />} />
          </Route>
        </Route>
      </Routes>
    </>
  );
}

export default App;
