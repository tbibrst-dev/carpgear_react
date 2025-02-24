import { useSelector } from "react-redux";
import { RootState } from "../../redux/store";
import { useEffect, useState } from "react";
import { User } from "../../types";

const Billing = () => {
  const { user } = useSelector((state: RootState) => state.userReducer);
  const [userDetails, setUserDetails] = useState<User>();

  console.log("user", user);
  useEffect(() => {
    if (user) {
      setUserDetails(user);
    }
  }, [user]);

  return (
    <div className="billing-section-right">
      <div className="billing-input-container">
        <div className="billing-input">
          <label htmlFor="firstName">first name</label>
          <input
            type="text"
            placeholder="First Name"
            value={userDetails?.first_name}
          />
        </div>
        <div className="billing-input">
          <label htmlFor="lastName">last name</label>
          <input
            type="text"
            value={userDetails?.last_name}
            placeholder="Last Name"
          />
        </div>
      </div>
      {/* inputs */}
      <div className="billing-input-container mt-5">
        <div className="billing-input">
          <label htmlFor="phone">contact details</label>
          <input type="text" id="phone" placeholder="Phone*" />
        </div>
        <div className="billing-input">
          <input
            type="text"
            id="email"
            className="email"
            placeholder="Email Address*"
          />
        </div>
      </div>
      <div className="mt-4">
        <div className="billing-input-location">
          <label htmlFor="">street address</label>
          <input type="text" id="phone" placeholder="House Name / No." />
          <input type="text" id="phone" placeholder="Street Address*" />
          <input type="text" id="phone" placeholder="Town / City*" />
          <div className="bottom">
            <input type="text" placeholder="Postcode*" />
            <input type="text" placeholder="Country (Optional)" />
          </div>
        </div>
      </div>
      <div className="bottom-country">
        <h6>
          country region: <span>United Kingdom</span>
        </h6>
      </div>
      <button className="billing-section-button">save changes</button>
    </div>
  );
};

export default Billing;
