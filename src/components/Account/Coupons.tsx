// import { useSelector } from "react-redux";
// import { RootState } from "../../redux/store";
// import { useEffect, useState } from "react";
import getCoupons from "./hooks/getCoupons";
import CouponsUI from "./CouponsUI";
import { AUTH_TOKEN_KEY, decryptToken } from "../../utils";

const Coupons = () => {
  //   const { user } = useSelector((state: RootState) => state.userReducer);
  const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
  let token;
  if (encodedToken) {
    token = decryptToken(encodedToken);
  }

  const { isLoading, avialablePoints, pointLogs } = getCoupons(
    token as string
  );

  if (isLoading) {
    return (
      <div className="basket-loader-container">
        <svg viewBox="25 25 50 50" className="loader-svg">
          <circle r={20} cy={50} cx={50} className="loader" />
        </svg>
      </div>
    );
  }

  return <CouponsUI pointsLogs={pointLogs} points={avialablePoints} />;
};

export default Coupons;
