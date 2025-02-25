import axios from "axios";
import { useDispatch, useSelector } from "react-redux";
import { useLocation, useNavigate } from "react-router";
import { Link } from "react-router-dom";
import { RootState } from "../../redux/store";
import {
  setPurchasedTickets,
  setUserState,
} from "../../redux/slices/userSlice";
import { AUTH_TOKEN_KEY } from "../../utils";
import { useState, useEffect } from "react";
import { CometChatUIKit } from "@cometchat/chat-uikit-react";



const Sidebar = () => {
  const { pathname } = useLocation();
  const user = useSelector((state: RootState) => state.userReducer.user);
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const token = import.meta.env.VITE_TOKEN; // Store token securely in .env file


  const handleLogout = async () => {
    try {
      const response = await axios.get(
        `?rest_route=/api/v1/logout&token=${user?.token}`,{
          headers: {
            Authorization: `Bearer ${token}`,
          }}

      );
      if (response.data.status === "TRUE") {

        // localStorage.clear();
        navigate("/");
        dispatch(setUserState(null));
        localStorage.removeItem(AUTH_TOKEN_KEY);
        dispatch(setPurchasedTickets([]));
        const responseid = await CometChatUIKit.logout();
        console.log('responseid', responseid);
      }
    } catch (error) {
      console.log(error);
    }
  };


  const [clicked, setClicked] = useState<boolean>(false);
  const [sideMenuActive, setSideMenuActive] = useState<boolean>(false);

  const handleClick = () => {
    setClicked(!clicked);
    setSideMenuActive(!sideMenuActive);
  };

  useEffect(() => {
    setTimeout(() => {
      setSideMenuActive(true);
    }, 100);
  }, []);

  return (

    <div className={`responsible-gaming-left-side ${sideMenuActive ? 'slideInSide' : ''}`}>
      <button type="button" className={`ticket-side-btn ${clicked ? 'clicked' : ''}`} onClick={handleClick}>
        <div className="tick-sys-show">
          <svg
            className="angle"
            width={10}
            height={17}
            viewBox="0 0 10 17"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M1.25 1L8.75 8.5L1.25 16"
              stroke="#0F1010"
              strokeWidth={2}
              strokeLinecap="round"
              strokeLinejoin="round"
            />
          </svg>
          <svg
            className="crose"
            width={18}
            height={17}
            viewBox="0 0 18 17"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M8.96289 8.5L1.46289 1M8.96289 8.5L16.4629 16M8.96289 8.5L16.4629 1M8.96289 8.5L1.46289 16"
              stroke="#0F1010"
              strokeWidth={2}
              strokeLinecap="round"
              strokeLinejoin="round"
            />
          </svg>
        </div>
      </button>
      <div className="responsible-gaming-btns" >
        <div className="responsive-btn">
          <Link
            to="/account"
            className={`ticket-btn ${pathname === "/account" && "selected"}`}
            onClick={handleClick}
          >
            <svg
              width={16}
              height={9}
              viewBox="0 0 16 9"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M16 3.13736V1.31504C16 0.595294 15.3949 0.0118408 14.6487 0.0118408H1.35131C0.605031 0.0118107 0 0.595264 0 1.31501V3.14951C0.686156 3.24455 1.21419 3.81209 1.21419 4.50008C1.21419 5.18807 0.686156 5.75567 0 5.85047V7.6852C0 8.40471 0.605031 8.98817 1.35131 8.98817H14.6486C15.3949 8.98817 16 8.40471 16 7.6852V5.86261C15.2492 5.82874 14.6508 5.2326 14.6508 4.50011C14.6508 3.76764 15.2492 3.1715 16 3.13736ZM3.79734 8.16596H3.33244V6.99671H3.79734V8.16596ZM3.79734 6.1117H3.33244V4.94247H3.79734V6.1117ZM3.79734 4.0575H3.33244V2.888H3.79734V4.0575ZM3.79734 2.0033H3.33244V0.834045H3.79734V2.0033Z"
                fill="white"
              />
            </svg>
            <p>Tickets</p>
          </Link>
        </div>
        <div className="responsive-btn active">
          <Link
            to="/account/recent/orders"
            className={`ticket-btn ${(pathname === "/account/recent/orders" || pathname.includes('/account/recent/orders/order-detail')) && "selected"
              }`}
            onClick={handleClick}
          >
            <svg
              width={22}
              height={23}
              viewBox="0 0 22 23"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M11.916 3.25C9.72798 3.25 7.62956 4.11919 6.08238 5.66637C4.53521 7.21354 3.66602 9.31196 3.66602 11.5H0.916016L4.48185 15.0658L4.54602 15.1942L8.24935 11.5H5.49935C5.49935 7.9525 8.36852 5.08333 11.916 5.08333C15.4635 5.08333 18.3327 7.9525 18.3327 11.5C18.3327 15.0475 15.4635 17.9167 11.916 17.9167C10.1468 17.9167 8.54268 17.1925 7.38768 16.0283L6.08602 17.33C6.84998 18.0982 7.75846 18.7075 8.75907 19.1229C9.75969 19.5382 10.8326 19.7514 11.916 19.75C14.1041 19.75 16.2025 18.8808 17.7496 17.3336C19.2968 15.7865 20.166 13.688 20.166 11.5C20.166 9.31196 19.2968 7.21354 17.7496 5.66637C16.2025 4.11919 14.1041 3.25 11.916 3.25ZM10.9993 7.83333V12.4167L14.8952 14.7267L15.601 13.5533L12.3743 11.6375V7.83333H10.9993Z"
                fill="white"
              />
            </svg>
            <p>Recent Orders</p>
          </Link>
        </div>
        <div className="responsive-btn">
          <Link
            to="/account/cred"
            className={`ticket-btn ${(pathname === "/account/cred") && "selected"
              }`}
            onClick={handleClick}
          >
            <svg
              width={16}
              height={11}
              viewBox="0 0 16 11"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M5.01323 2.31945C6.10212 1.40278 7.57157 0.611115 9.33268 0.611115C11.0938 0.611115 12.5632 1.40278 13.6521 2.31945C14.7382 3.23611 15.5105 4.33056 15.9021 5.09167C16.0327 5.34723 16.0327 5.65 15.9021 5.90556C15.5105 6.66667 14.7382 7.76111 13.6521 8.67778C12.5632 9.59723 11.0938 10.3889 9.33268 10.3889C7.57157 10.3889 6.10212 9.59723 5.01323 8.68056C4.56323 8.3 4.16601 7.88889 3.82712 7.48334L1.33546 8.93334C0.988234 9.13612 0.546568 9.08056 0.260457 8.79723C-0.0256546 8.51389 -0.0839879 8.075 0.116012 7.725L1.38823 5.5L0.116012 3.275C-0.0839879 2.925 -0.0228768 2.48611 0.263234 2.20278C0.549345 1.91945 0.988234 1.86389 1.33823 2.06667L3.8299 3.51945C4.16879 3.11389 4.56601 2.70278 5.01601 2.32223L5.01323 2.31945ZM12.4438 5.5C12.4438 5.26426 12.3501 5.03816 12.1834 4.87146C12.0167 4.70477 11.7906 4.61111 11.5549 4.61111C11.3192 4.61111 11.0931 4.70477 10.9264 4.87146C10.7597 5.03816 10.666 5.26426 10.666 5.5C10.666 5.73575 10.7597 5.96184 10.9264 6.12854C11.0931 6.29524 11.3192 6.38889 11.5549 6.38889C11.7906 6.38889 12.0167 6.29524 12.1834 6.12854C12.3501 5.96184 12.4438 5.73575 12.4438 5.5Z"
                fill="white"
              />
            </svg>
            <p>CGG Cred</p>
          </Link>
        </div>
        <div className="responsive-btn">
          <Link
            to="/account/points"
            className={`ticket-btn ${pathname === "/account/points" && "selected"
              }`}
            onClick={handleClick}
          >
            <svg
              width={22}
              height={21}
              viewBox="0 0 22 21"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M11.0008 15.7125L5.85661 18.4167L6.83911 12.6883L2.67578 8.63166L8.42745 7.79583L11.0008 2.58333L13.5733 7.79583L19.3249 8.63166L15.1624 12.6883L16.1458 18.4167L11.0008 15.7125Z"
                fill="white"
              />
            </svg>
            <p>Points</p>
          </Link>
        </div>

        {/* <div className="responsive-btn">
            <Link
              to="/account/responsible/gaming"
              className={`ticket-btn ${
                pathname === "/account/responsible/gaming" && "selected"
              }`}
              onClick={handleClick}
            >
              <svg
                width={18}
                height={11}
                viewBox="0 0 18 11"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M5.4 0.0999985C2.41875 0.0999985 0 2.51875 0 5.5C0 8.48125 2.41875 10.9 5.4 10.9H12.6C15.5812 10.9 18 8.48125 18 5.5C18 2.51875 15.5812 0.0999985 12.6 0.0999985H5.4ZM13.95 3.025C14.2484 3.025 14.5345 3.14353 14.7455 3.3545C14.9565 3.56548 15.075 3.85163 15.075 4.15C15.075 4.44837 14.9565 4.73452 14.7455 4.94549C14.5345 5.15647 14.2484 5.275 13.95 5.275C13.6516 5.275 13.3655 5.15647 13.1545 4.94549C12.9435 4.73452 12.825 4.44837 12.825 4.15C12.825 3.85163 12.9435 3.56548 13.1545 3.3545C13.3655 3.14353 13.6516 3.025 13.95 3.025ZM11.025 6.85C11.025 6.55163 11.1435 6.26548 11.3545 6.0545C11.5655 5.84352 11.8516 5.725 12.15 5.725C12.4484 5.725 12.7345 5.84352 12.9455 6.0545C13.1565 6.26548 13.275 6.55163 13.275 6.85C13.275 7.14837 13.1565 7.43452 12.9455 7.64549C12.7345 7.85647 12.4484 7.975 12.15 7.975C11.8516 7.975 11.5655 7.85647 11.3545 7.64549C11.1435 7.43452 11.025 7.14837 11.025 6.85ZM4.725 3.925C4.725 3.55094 5.02594 3.25 5.4 3.25C5.77406 3.25 6.075 3.55094 6.075 3.925V4.825H6.975C7.34906 4.825 7.65 5.12594 7.65 5.5C7.65 5.87406 7.34906 6.175 6.975 6.175H6.075V7.075C6.075 7.44906 5.77406 7.75 5.4 7.75C5.02594 7.75 4.725 7.44906 4.725 7.075V6.175H3.825C3.45094 6.175 3.15 5.87406 3.15 5.5C3.15 5.12594 3.45094 4.825 3.825 4.825H4.725V3.925Z"
                  fill="#fff"
                />
              </svg>
              <p>Responsible Gaming</p>
            </Link>
          </div> */}

        <div className="responsive-btn">
          <Link
            to="/account/coupons"
            className={`ticket-btn ${pathname === "/account/coupons" && "selected"
              }`}
            onClick={handleClick}
          >
            <svg
              width={18}
              height={14}
              viewBox="0 0 18 14"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M13.42 11.32C14.12 11.32 14.58 10.76 14.58 10.06C14.58 9.36 14.14 8.82 13.42 8.82C12.72 8.82 12.28 9.36 12.28 10.06C12.28 10.76 12.72 11.32 13.42 11.32ZM4.14 4.96C4.84 4.96 5.3 4.42 5.3 3.7C5.3 3 4.86 2.48 4.14 2.48C3.44 2.48 3 3 3 3.7C3 4.42 3.44 4.96 4.14 4.96ZM13.42 13.7C11.22 13.7 9.74 12.16 9.74 10.08C9.74 7.96 11.22 6.42 13.42 6.42C15.62 6.42 17.14 7.96 17.14 10.08C17.14 12.16 15.62 13.7 13.42 13.7ZM4.14 7.34C1.94 7.34 0.46 5.82 0.46 3.72C0.46 1.6 1.94 0.059999 4.14 0.059999C6.32 0.059999 7.86 1.6 7.86 3.72C7.86 5.82 6.32 7.34 4.14 7.34ZM4.16 13.52V13.24L11.32 0.239999H13.48V0.48L6.3 13.52H4.16Z"
                fill="white"
              />
            </svg>
            <p>Coupons</p>
          </Link>
        </div>
        <div className="responsive-btn">
          <Link
            to="/account/billing"
            className={`ticket-btn ${pathname === "/account/billing" && "selected"
              }`}
            onClick={handleClick}
          >
            <svg
              width={18}
              height={15}
              viewBox="0 0 18 15"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M2 0.5C0.896875 0.5 0 1.39688 0 2.5V12.5C0 13.6031 0.896875 14.5 2 14.5H16C17.1031 14.5 18 13.6031 18 12.5V2.5C18 1.39688 17.1031 0.5 16 0.5H2ZM4.5 8.5H6.5C7.88125 8.5 9 9.61875 9 11C9 11.275 8.775 11.5 8.5 11.5H2.5C2.225 11.5 2 11.275 2 11C2 9.61875 3.11875 8.5 4.5 8.5ZM3.5 5.5C3.5 4.96957 3.71071 4.46086 4.08579 4.08579C4.46086 3.71071 4.96957 3.5 5.5 3.5C6.03043 3.5 6.53914 3.71071 6.91421 4.08579C7.28929 4.46086 7.5 4.96957 7.5 5.5C7.5 6.03043 7.28929 6.53914 6.91421 6.91421C6.53914 7.28929 6.03043 7.5 5.5 7.5C4.96957 7.5 4.46086 7.28929 4.08579 6.91421C3.71071 6.53914 3.5 6.03043 3.5 5.5ZM11.5 4.5H15.5C15.775 4.5 16 4.725 16 5C16 5.275 15.775 5.5 15.5 5.5H11.5C11.225 5.5 11 5.275 11 5C11 4.725 11.225 4.5 11.5 4.5ZM11.5 6.5H15.5C15.775 6.5 16 6.725 16 7C16 7.275 15.775 7.5 15.5 7.5H11.5C11.225 7.5 11 7.275 11 7C11 6.725 11.225 6.5 11.5 6.5ZM11.5 8.5H15.5C15.775 8.5 16 8.725 16 9C16 9.275 15.775 9.5 15.5 9.5H11.5C11.225 9.5 11 9.275 11 9C11 8.725 11.225 8.5 11.5 8.5Z"
                fill="white"
              />
            </svg>
            <p>Billing</p>
          </Link>
        </div>
        <div className="responsive-btn">
          <Link
            to="/account/details"
            className={`ticket-btn ${pathname === "/account/details" && "selected"
              }`}
            onClick={handleClick}
          >
            <svg
              width={16}
              height={21}
              viewBox="0 0 16 21"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M9.6 12.0764H6.4C4.7032 12.0783 3.07644 12.7532 1.87662 13.953C0.676802 15.1528 0.00190585 16.7796 1.67847e-07 18.4764C-0.000109668 18.646 0.0536889 18.8112 0.153628 18.9483C0.253566 19.0853 0.394473 19.187 0.556 19.2388C2.98547 19.8673 5.4916 20.1493 8 20.0764C10.5084 20.1493 13.0145 19.8673 15.444 19.2388C15.6055 19.187 15.7464 19.0853 15.8464 18.9483C15.9463 18.8112 16.0001 18.646 16 18.4764C15.9981 16.7796 15.3232 15.1528 14.1234 13.953C12.9236 12.7532 11.2968 12.0783 9.6 12.0764Z"
                fill="white"
              />
              <path
                d="M7.99961 10.4764C10.4796 10.4764 12.3996 7.70038 12.3996 5.31238C12.3996 4.14542 11.936 3.02627 11.1109 2.20111C10.2857 1.37595 9.16656 0.912376 7.99961 0.912376C6.83266 0.912376 5.7135 1.37595 4.88834 2.20111C4.06318 3.02627 3.59961 4.14542 3.59961 5.31238C3.59961 7.70038 5.51961 10.4764 7.99961 10.4764Z"
                fill="white"
              />
            </svg>
            <p>Account Details</p>
          </Link>
        </div>


        <div className="responsive-btn">
          <Link
            to="/account/financial-details"
            className={`ticket-btn ${pathname === "/account/financial-details" && "selected"
              }`}
            onClick={handleClick}
          >
            <svg
              width={16}
              height={21}
              viewBox="0 0 16 21"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M9.6 12.0764H6.4C4.7032 12.0783 3.07644 12.7532 1.87662 13.953C0.676802 15.1528 0.00190585 16.7796 1.67847e-07 18.4764C-0.000109668 18.646 0.0536889 18.8112 0.153628 18.9483C0.253566 19.0853 0.394473 19.187 0.556 19.2388C2.98547 19.8673 5.4916 20.1493 8 20.0764C10.5084 20.1493 13.0145 19.8673 15.444 19.2388C15.6055 19.187 15.7464 19.0853 15.8464 18.9483C15.9463 18.8112 16.0001 18.646 16 18.4764C15.9981 16.7796 15.3232 15.1528 14.1234 13.953C12.9236 12.7532 11.2968 12.0783 9.6 12.0764Z"
                fill="white"
              />
              <path
                d="M7.99961 10.4764C10.4796 10.4764 12.3996 7.70038 12.3996 5.31238C12.3996 4.14542 11.936 3.02627 11.1109 2.20111C10.2857 1.37595 9.16656 0.912376 7.99961 0.912376C6.83266 0.912376 5.7135 1.37595 4.88834 2.20111C4.06318 3.02627 3.59961 4.14542 3.59961 5.31238C3.59961 7.70038 5.51961 10.4764 7.99961 10.4764Z"
                fill="white"
              />
            </svg>
            <p>Payout Details</p>
          </Link>
        </div>

        <div className="responsive-btn">
          <button className="ticket-btn" onClick={handleLogout}>
            <svg
              width={22}
              height={21}
              viewBox="0 0 22 21"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M12.6 12.0764H9.4C7.7032 12.0783 6.07644 12.7532 4.87662 13.953C3.6768 15.1528 3.00191 16.7796 3 18.4764C2.99989 18.646 3.05369 18.8112 3.15363 18.9483C3.25357 19.0853 3.39447 19.187 3.556 19.2388C5.98547 19.8673 8.4916 20.1493 11 20.0764C13.5084 20.1493 16.0145 19.8673 18.444 19.2388C18.6055 19.187 18.7464 19.0853 18.8464 18.9483C18.9463 18.8112 19.0001 18.646 19 18.4764C18.9981 16.7796 18.3232 15.1528 17.1234 13.953C15.9236 12.7532 14.2968 12.0783 12.6 12.0764Z"
                fill="white"
              />
              <path
                d="M10.9996 10.4764C13.4796 10.4764 15.3996 7.70037 15.3996 5.31237C15.3996 4.14542 14.936 3.02626 14.1109 2.2011C13.2857 1.37594 12.1666 0.912369 10.9996 0.912369C9.83266 0.912369 8.7135 1.37594 7.88834 2.2011C7.06318 3.02626 6.59961 4.14542 6.59961 5.31237C6.59961 7.70037 8.51961 10.4764 10.9996 10.4764Z"
                fill="white"
              />
            </svg>
            <p>Logout</p>
          </button>
        </div>
      </div>
    </div>

  );
};

export default Sidebar;
