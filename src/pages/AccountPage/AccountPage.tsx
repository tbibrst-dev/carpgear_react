import { Outlet, useLocation, useNavigate } from "react-router";
import Sidebar from "../../components/Account/Sidebar";
import { useSelector } from "react-redux";
import { RootState } from "../../redux/store";
import { useEffect } from "react";

const AccountPage = () => {
  const { user, isAuthenticating, isLoading } = useSelector(
    (state: RootState) => state.userReducer
  );
  const navigate = useNavigate();
  const { pathname } = useLocation();

  function getHeader() {
    if (pathname === "/account") {
      return "tickets";
    }
    if (pathname === "/account/responsible/gaming") {
      return "responsible gaming";
    }
    if (pathname === "/account/details") {
      return "account details";
    }
    if (pathname === "/account/points") {
      return " points";
    }
    if (pathname === "/account/recent/orders") {
      return "recent orders";
    }
    if (pathname.includes("/account/recent/orders/order-detail")) {
      return "recent orders";
    }
    if (pathname === "/account/billing") {
      return "billing";
    }

    if (pathname === "/account/coupons") {
      return "coupons";
    }
    if (pathname === "/account/cred") {
      return "cgg cred";
    }

  }

  const heading = getHeader();
  const container =
    pathname === "/account" ? "ticket-gaming-all" : "responsible-gaming-all";

  useEffect(() => {
    if (isAuthenticating) return;
    if (!user) {
      navigate("/auth/login");
    }
  }, [user, isAuthenticating]);


 

  return (
    <div>
      <div className="ticket-banner">
        <div className="ticket-banner-all">
          <div className="ticket-click-heading">
            <h2>{heading}</h2>
          </div>
        </div>
      </div>
      <div
        className={`responsible-gaming ${
          isLoading && "responsible-gaming-fade"
        }`}
      >
        <div className={container}>
          <Sidebar />
          <Outlet />
        </div>
      </div>
    </div>
  );
};

export default AccountPage;
