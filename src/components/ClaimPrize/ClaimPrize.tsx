import { useEffect, useState } from "react";
import axios from "axios";
import { AUTH_TOKEN_KEY, decryptToken, TOKEN } from "../../utils";
import { useLocation, useNavigate } from 'react-router-dom';
import { useSelector } from "react-redux";
import { RootState } from "../../redux/store";




import { FilloutStandardEmbed } from '@fillout/react';
import '@fillout/react/style.css';
const ClaimPrize: React.FC = () => {

  const { user, isAuthenticating } = useSelector(
    (state: RootState) => state.userReducer
  );

  type DataType = {
    message: string,
    success: string,
  } | any;

  const location = useLocation();
  const queryParams = new URLSearchParams(location.search);

  const [loading, setLoading] = useState<boolean>(true);
  const [data, setData] = useState<DataType>([]);

  const competition_name = queryParams.get('competition_name') ?? '';
  const competition_type = queryParams.get('competition_type') ?? '';
  const prize_name = queryParams.get('prize_name') ?? '';
  const prize_id = queryParams.get('prize_id') ?? '';
  const competition_id = queryParams.get('competition_id') ?? '';
  const order = queryParams.get('order') ?? '';
  const ticket_number = queryParams.get('ticket_number') ?? '';
  const user_id = queryParams.get('user_id') ?? '';

  const navigate = useNavigate();
  const formID = import.meta.env.VITE_FILLOUT_ID;
  console.log('formID',formID);


  useEffect(() => {
    if (isAuthenticating) return;
    if (!user) {
      navigate("/auth/login");
    }

    console.log('calim form user', user);
  }, [user, isAuthenticating]);


  useEffect(() => {
    setLoading(true);
    fetchClaimPrizeStatus();
  }, []);

  const fetchClaimPrizeStatus = async () => {
    try {
      const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
      const token = decryptToken(encodedToken);

      const res = await axios.post(
        "?rest_route=/api/v1/checkClaimPrize",
        {
          token,
          competition_type: competition_type,
          order: order,
          prize_id: prize_id,
          ticket_number: ticket_number,
          competition_id: competition_id,
          user_id: user_id

        },
        { headers: { Authorization: TOKEN } }
      );
      if (res.data.success) {
        setData(res.data);
      }
      setLoading(false);
    } catch (error) {
      console.log(error);
      setLoading(false);


    } finally {
      setLoading(false);


    }
  };



  const redirectTo = () => {
    navigate('/account/financial-details');
  }

  return (
    <>
      {/* winner-confirmation-banner-start */}
      <div className="comp-banner">
        <div className="prize-banner-txt">
          <h2>You’re a Winner!</h2>
        </div>
      </div>
      {/* winner-confirmation-banner-end */}
      {/* External-section-start */}
      <div
        style={{
          width: '100%',
          height: '1100px',
        }}
      >

        {loading ? (
          <div className="basket-loader-container">
            <svg viewBox="25 25 50 50" className="loader-svg">
              <circle r={20} cy={50} cx={50} className="loader" />
            </svg>
          </div>
        ) : data && data.message === 'invalid' ? (
          <div style={{ marginTop: "100px" }}>
            <h4 className="text-white text-center text-uppercase" style={{ fontWeight: 800 }}>
              You have already claimed the prize!
            </h4>
          </div>
        ) : data && data.message === 'accountDetailsMissing' ? (
          <div style={{ marginTop: "100px" }} className="mainDivErrorPayment">
            <h4 className="text-white text-center text-uppercase" style={{ fontWeight: 800 }}>
              Before you can claim a prize, you must enter your Payout Details.
            </h4> <br></br>
            <span onClick={redirectTo} className="redirect_button_claim_form">Enter Payout Details</span>
          </div>
        ) : (
          <FilloutStandardEmbed
            filloutId= {formID}
            inheritParameters
            parameters={{
              competition_name: competition_name,
              competition_type: competition_type,
              prize_name: prize_name,
              prize_id: prize_id,
              competition_id: competition_id,
              order: order,
              ticket_number: ticket_number,
              claim_type: 'Prize',
              user_id: user_id,
            }}
          />
        )}
      </div>
    </>
  );
};

export default ClaimPrize;
