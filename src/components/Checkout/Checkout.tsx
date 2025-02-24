import { useState } from "react";

const Checkout = () => {
  const [activeIndex, setActiveIndex] = useState<number>(0);
  const [cardIndex, setCardIndex] = useState<number>(0);
  const [activePoints, setActivePoints] = useState<boolean>(false);

  const handleActiveIndex = (index: number) => {
    activeIndex === index ? setActiveIndex(-1) : setActiveIndex(index);
  };

  const handleCardIndex = (index: number) => {
    cardIndex === index ? setCardIndex(-1) : setCardIndex(index);
  };

  return (
    <div>
      <div className="checkout-banner">
        <div className="checkout-banner-content">
          <p>Don’t have an account?</p>
          <button type="button" className="check-login">
            <svg
              width={14}
              height={15}
              viewBox="0 0 14 15"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
              className="profile-icon-hide"
            >
              <path
                d="M8.25187 8.73312H5.74864C4.4213 8.73461 3.14876 9.26256 2.21019 10.2011C1.27162 11.1397 0.743678 12.4122 0.742188 13.7396C0.742102 13.8723 0.784186 14.0015 0.862364 14.1087C0.940542 14.2159 1.05077 14.2955 1.17712 14.336C3.0776 14.8277 5.03803 15.0482 7.00025 14.9912C8.96247 15.0482 10.9229 14.8277 12.8234 14.336C12.9497 14.2955 13.06 14.2159 13.1381 14.1087C13.2163 14.0015 13.2584 13.8723 13.2583 13.7396C13.2568 12.4122 12.7289 11.1397 11.7903 10.2011C10.8517 9.26256 9.5792 8.73461 8.25187 8.73312Z"
                fill="#0F1010"
              />
              <path
                d="M7.00053 7.48152C8.94053 7.48152 10.4425 5.30997 10.4425 3.44194C10.4425 2.52908 10.0798 1.65361 9.43435 1.00812C8.78886 0.362632 7.91339 0 7.00053 0C6.08767 0 5.2122 0.362632 4.56671 1.00812C3.92122 1.65361 3.55859 2.52908 3.55859 3.44194C3.55859 5.30997 5.06053 7.48152 7.00053 7.48152Z"
                fill="#0F1010"
              />
            </svg>
            HiDe Login
          </button>
        </div>
      </div>

      <div className="checkout-section">
        <div className="container">
          <div className="checkout-section-all">
            <div className="checkout-section-left">
              <div className="checkout-section-login  mob-log-section-show">
                <form action="">
                  <div className="checkout-details-head">
                    <h4>LOGIN</h4>
                  </div>
                  <div className="checkout-details-filed">
                    <div className="name-area">
                      <div className="date-fields">
                        <input placeholder="Username / Email" />
                      </div>
                      <div className="date-fields">
                        <input type="password" placeholder="Password" />
                      </div>
                    </div>
                  </div>
                  <div className="log-on">
                    <div className="form-group">
                      <input type="checkbox" id="logg" />
                      <label htmlFor="logg">
                        <p> Remember me</p>
                      </label>
                    </div>
                  </div>
                  <div className="forgot-password">
                    <a href="#">Forgot Password?</a>
                  </div>
                  <div className="log-on-login">
                    <button type="button" className="log-on-login-btn">
                      Login
                    </button>
                  </div>
                </form>
              </div>

              <div className="checkout-entry-question">
                <h4>ENTRY QUESTION</h4>
                <p>
                  Answer this question correctly to be entered into the live
                  draw
                </p>
                <div className="checkout-use-boat">
                  <p>Why use a bait boat?</p>

                  <div className="check-bait">
                    <form>
                      <div className="check-bait-all">
                        <div
                          className={`form-group check-bait-one ${
                            activeIndex === 1 && "active"
                          }`}
                        >
                          <input
                            type="radio"
                            name="ques"
                            checked={activeIndex === 1}
                            onChange={() => handleActiveIndex(1)}
                            id="why-one"
                          />
                          <label htmlFor="why-one">To send out bait</label>
                        </div>
                        <div
                          className={`form-group check-bait-one ${
                            activeIndex === 2 && "active"
                          }`}
                        >
                          <input
                            type="radio"
                            name="ques"
                            checked={activeIndex === 2}
                            id="why-two"
                            onChange={() => handleActiveIndex(2)}
                          />
                          <label htmlFor="why-two">To send out Poop</label>
                        </div>
                        <div
                          className={`form-group check-bait-one ${
                            activeIndex === 3 && "active"
                          }`}
                        >
                          <input
                            type="radio"
                            name="ques"
                            checked={activeIndex === 3}
                            onChange={() => handleActiveIndex(3)}
                            id="why-three"
                          />
                          <label htmlFor="why-three">To send out Beers</label>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <div className="checkout-section-login mob-log-section-hide">
                <form>
                  <div className="checkout-details-head">
                    <h4>LOGIN</h4>
                  </div>
                  <div className="checkout-details-filed">
                    <div className="name-area">
                      <div className="date-fields">
                        <input placeholder="Username / Email" />
                      </div>
                      <div className="date-fields">
                        <input type="password" placeholder="Password" />
                      </div>
                    </div>
                  </div>
                  <div className="log-on">
                    <div className="form-group">
                      <input type="checkbox" id="log" />
                      <label htmlFor="log">
                        <p> Remember me</p>
                      </label>
                    </div>
                  </div>
                  <div className="forgot-password">
                    <a href="#">Forgot Password?</a>
                  </div>
                  <div className="log-on-login">
                    <button type="button" className="log-on-login-btn">
                      Login
                    </button>
                  </div>
                </form>
              </div>
              <div className="checkout-section-details">
                <form>
                  <div className="detail-sec">
                    <div className="checkout-details-head">
                      <h4>DETAILS</h4>
                    </div>
                    <div className="checkout-details-fileds">
                      <div className="name-area">
                        <div className="name-field">
                          <input type="text" placeholder="John" />
                        </div>
                        <div className="name-field">
                          <input type="text" placeholder="Wick" />
                        </div>
                      </div>
                      <div className="name-area">
                        <div className="date-field">
                          <input placeholder="Date of birth (DD-MM-YYYY)*" />
                        </div>
                        <div className="date-field">
                          <input type="number" placeholder="Phone*" />
                        </div>
                      </div>
                      <div className="email-area">
                        <div className="mail-field">
                          <input type="email" placeholder="Email Address*" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="Account-sec anouth">
                    <div className="checkout-details-head-acc">
                      <h4>ACCOUNT</h4>
                    </div>
                    <div className="checkout-details-fileds">
                      <div className="name-area">
                        <div className="date-field">
                          <input placeholder="Email*" />
                        </div>
                        <div className="date-field">
                          <input
                            type="password"
                            placeholder="Create a password*"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="billing-sec">
                    <div className="checkout-details-head-bill">
                      <h4>BILLING ADDRESS</h4>
                    </div>
                    <div className="checkout-details-fileds">
                      <div className="email-area">
                        <div className="mail-field">
                          <input type="email" placeholder="House Name / No." />
                        </div>
                      </div>
                      <div className="email-area">
                        <div className="mail-field">
                          <input type="email" placeholder="Street Address*" />
                        </div>
                      </div>
                      <div className="name-area">
                        <div className="date-field">
                          <input type="text" placeholder="Town / City*" />
                        </div>
                        <div className="date-field">
                          <input type="text" placeholder="Postcode*" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="checkout-section-signup">
                    <div className="carp-login-check-it">
                      <div className="carp-login-check-one">
                        <div className="form-group">
                          <input type="checkbox" id="mail" />
                          <label htmlFor="mail">
                            <p> Sign me up to receive email updates and news</p>
                          </label>
                        </div>
                      </div>
                      <div className="carp-login-check-one">
                        <div className="form-group">
                          <input type="checkbox" id="sms" />
                          <label htmlFor="sms">
                            <p>Sign me up to receive SMS updates and news</p>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="checkout-section-bottom">
                    <p>
                      By checking this box and entering your phone number above,
                      you consent to receive marketing text messages from Carp
                      Gear Giveaways at the number provided. Consent is not a
                      condition of any purchase. View our Privacy Policy and
                      Terms of Service for more information.
                    </p>
                  </div>
                </form>
              </div>
            </div>
            <div className="checkout-section-right">
              <div className="checkout-section-right-top">
                <div className="checkout-section-right-head">
                  <h4>YOUR TICKETS</h4>
                </div>
                <div className="checkout-section-right-top-box">
                  <div className="checkout-section-right-top-box-left">
                    <div className="checkout-section-right-top-box-left-pic">
                      <img src="images/check-1.png" />
                    </div>
                  </div>
                  <div className="checkout-section-right-top-box-right">
                    <h4>
                      20x BAIT BOAT INSTANT WINS WITH ND BAIT BOAT 2 END DRAW #2
                    </h4>
                    <div className="checkout-section-right-ticket">
                      <div className="checkout-section-right-ticket-txt">
                        <p>
                          {" "}
                          <span className="tick-txt">5 Tickets</span>{" "}
                          <span className="slash-straight">|</span>{" "}
                          <span className="tick-price">£3.95</span>{" "}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="checkout-section-right-top-box-one">
                  <div className="checkout-section-right-top-box-left">
                    <div className="checkout-section-right-top-box-left-pic">
                      <img src="images/check-2.png" />
                    </div>
                  </div>
                  <div className="checkout-section-right-top-box-right">
                    <h4>COMPLETE NASH BUNDLE PLUS 25 JACKERY INSTANT WINS</h4>
                    <div className="checkout-section-right-ticket">
                      <div className="checkout-section-right-ticket-txt">
                        <p>
                          {" "}
                          <span className="tick-txt">9 Tickets</span>{" "}
                          <span className="slash-straight">|</span>{" "}
                          <span className="tick-price">£3.51</span>{" "}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="your-ticket-order">
                  <div className="your-ticket-order-star">
                    <svg
                      width="24"
                      height="25"
                      viewBox="0 0 24 25"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M12.0002 19.0842L5.50229 22.5001L6.74335 15.2642L1.48438 10.14L8.74968 9.08419L12.0002 2.49995L15.2497 9.08419L22.515 10.14L17.2571 15.2642L18.4992 22.5001L12.0002 19.0842Z"
                        fill="#EEC273"
                      ></path>
                    </svg>
                    <p>
                      Complete your order to earn
                      <span> 746</span> points
                    </p>
                  </div>
                </div>
                <div className="your-ticket-totals">
                  <div className="your-ticket-total">
                    <p>Total</p>
                  </div>
                  <div className="your-ticket-rate">
                    <p>£7.46</p>
                  </div>
                </div>
              </div>
              <div
                className={`checkout-section-right-points ${
                  activePoints && "active"
                }`}
              >
                <div className="pay-radio">
                  <div className="form-group">
                    <input
                      type="checkbox"
                      checked={activePoints}
                      onChange={() => setActivePoints(!activePoints)}
                      id="pay-first"
                    />
                    <label htmlFor="pay-first">
                      <div className="checkout-section-right-points-right-star">
                        <svg
                          className="click-star"
                          width="24"
                          height="24"
                          viewBox="0 0 24 24"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M12.0002 18.5843L5.50229 22.0001L6.74335 14.7643L1.48438 9.64004L8.74968 8.58425L12.0002 2L15.2497 8.58425L22.515 9.64004L17.2571 14.7643L18.4992 22.0001L12.0002 18.5843Z"
                            fill="#EEC273"
                          ></path>
                        </svg>
                        <p>1023 Points</p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
              <div className="use-point">
                <p>
                  Use your points to save <span>£0.46</span>{" "}
                </p>
              </div>
              <div
                className={`checkout-section-right-points-onee ${
                  cardIndex === 1 && "active"
                }`}
              >
                <div className="pay-radio">
                  <div className="form-group">
                    <input
                      type="radio"
                      name="card"
                      checked={cardIndex === 1}
                      onChange={() => handleCardIndex(1)}
                      id="pay-one"
                    />
                    <label htmlFor="pay-one">
                      <div className="checkout-section-right-points-right-star">
                        <svg
                          width={24}
                          height={17}
                          viewBox="0 0 24 17"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M22.5 0.000106812H1.5C1.23478 0.000106812 0.98043 0.105464 0.792893 0.293C0.605357 0.480537 0.5 0.73489 0.5 1.00011V16.0001C0.5 16.2653 0.605357 16.5197 0.792893 16.7072C0.98043 16.8948 1.23478 17.0001 1.5 17.0001H22.5C22.7652 17.0001 23.0196 16.8948 23.2071 16.7072C23.3946 16.5197 23.5 16.2653 23.5 16.0001V1.00011C23.5 0.73489 23.3946 0.480537 23.2071 0.293C23.0196 0.105464 22.7652 0.000106812 22.5 0.000106812ZM16.695 5.10461C16.6154 4.99852 16.5813 4.86517 16.6 4.7339C16.6188 4.60262 16.6889 4.48417 16.795 4.40461C16.9011 4.32504 17.0344 4.29088 17.1657 4.30963C17.297 4.32839 17.4154 4.39852 17.495 4.50461C17.8235 4.93425 18.0014 5.46004 18.0014 6.00086C18.0014 6.54168 17.8235 7.06747 17.495 7.49711C17.4154 7.60319 17.297 7.67333 17.1657 7.69208C17.0344 7.71083 16.9011 7.67667 16.795 7.59711C16.6889 7.51754 16.6188 7.39909 16.6 7.26782C16.5813 7.13654 16.6154 7.00319 16.695 6.89711C16.8927 6.64037 17 6.32542 17 6.00136C17 5.6773 16.8927 5.36235 16.695 5.10561V5.10461ZM3.5 3.50011C3.5 3.3675 3.55268 3.24032 3.64645 3.14655C3.74021 3.05279 3.86739 3.00011 4 3.00011H7.5C7.63261 3.00011 7.75979 3.05279 7.85355 3.14655C7.94732 3.24032 8 3.3675 8 3.50011V6.00011C8 6.13272 7.94732 6.25989 7.85355 6.35366C7.75979 6.44743 7.63261 6.50011 7.5 6.50011H4C3.86739 6.50011 3.74021 6.44743 3.64645 6.35366C3.55268 6.25989 3.5 6.13272 3.5 6.00011V3.50011ZM12.5 13.5001H4C3.86739 13.5001 3.74021 13.4474 3.64645 13.3537C3.55268 13.2599 3.5 13.1327 3.5 13.0001C3.5 12.8675 3.55268 12.7403 3.64645 12.6466C3.74021 12.5528 3.86739 12.5001 4 12.5001H12.5C12.6326 12.5001 12.7598 12.5528 12.8536 12.6466C12.9473 12.7403 13 12.8675 13 13.0001C13 13.1327 12.9473 13.2599 12.8536 13.3537C12.7598 13.4474 12.6326 13.5001 12.5 13.5001ZM14.5 7.00011C14.3022 7.00011 14.1089 6.94146 13.9444 6.83158C13.78 6.72169 13.6518 6.56552 13.5761 6.38279C13.5004 6.20006 13.4806 5.999 13.5192 5.80502C13.5578 5.61104 13.653 5.43285 13.7929 5.293C13.9327 5.15315 14.1109 5.05791 14.3049 5.01932C14.4989 4.98074 14.7 5.00054 14.8827 5.07623C15.0654 5.15191 15.2216 5.28009 15.3315 5.44454C15.4414 5.60899 15.5 5.80233 15.5 6.00011C15.5 6.26532 15.3946 6.51968 15.2071 6.70721C15.0196 6.89475 14.7652 7.00011 14.5 7.00011ZM20 13.5001H16.5C16.3674 13.5001 16.2402 13.4474 16.1464 13.3537C16.0527 13.2599 16 13.1327 16 13.0001C16 12.8675 16.0527 12.7403 16.1464 12.6466C16.2402 12.5528 16.3674 12.5001 16.5 12.5001H20C20.1326 12.5001 20.2598 12.5528 20.3536 12.6466C20.4473 12.7403 20.5 12.8675 20.5 13.0001C20.5 13.1327 20.4473 13.2599 20.3536 13.3537C20.2598 13.4474 20.1326 13.5001 20 13.5001ZM19.496 9.00011C19.4164 9.10619 19.298 9.17633 19.1667 9.19508C19.0354 9.21384 18.9021 9.17967 18.796 9.10011C18.6899 9.02054 18.6198 8.90209 18.601 8.77082C18.5823 8.63954 18.6164 8.50619 18.696 8.40011C19.2174 7.70985 19.4995 6.8684 19.4995 6.00336C19.4995 5.13831 19.2174 4.29686 18.696 3.60661C18.6164 3.50052 18.5823 3.36717 18.601 3.2359C18.6198 3.10462 18.6899 2.98617 18.796 2.90661C18.9021 2.82704 19.0354 2.79288 19.1667 2.81163C19.298 2.83039 19.4164 2.90052 19.496 3.00661C20.1475 3.87002 20.5 4.92221 20.5 6.00386C20.5 7.0855 20.1475 8.1377 19.496 9.00111V9.00011Z"
                            fill="white"
                          />
                        </svg>
                        <p>Pay via Card</p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
              <div
                className={`checkout-section-right-points-onee ${
                  cardIndex === 2 && "active"
                }`}
              >
                <div className="pay-radio">
                  <div className="form-group">
                    <input
                      type="radio"
                      name="card"
                      checked={cardIndex === 2}
                      onChange={() => handleCardIndex(2)}
                      id="pay-three"
                    />
                    <label htmlFor="pay-three">
                      <div className="checkout-section-right-points-right-star">
                        <svg
                          width={24}
                          height={30}
                          viewBox="0 0 24 30"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <g clipPath="url(#clip0_565_17104)">
                            <path
                              d="M9.38869 5.91031C9.18587 5.9103 8.98971 5.98267 8.83551 6.11441C8.6813 6.24616 8.57918 6.42861 8.54752 6.62894L7.14951 15.4935L5.86503 23.639L5.86426 23.6458L5.86581 23.639L7.15028 15.4935C7.21559 15.0795 7.57203 14.7749 7.99098 14.7749H12.0865C16.2083 14.7749 19.7067 11.7678 20.3456 7.69284C20.3943 7.38324 20.4203 7.07659 20.4262 6.77397V6.7735H20.4258C19.3783 6.22394 18.1483 5.91031 16.8005 5.91031H9.38869Z"
                              fill="#001C64"
                            />
                            <path
                              d="M20.4257 6.77367C20.42 7.07629 20.3936 7.38325 20.345 7.69285C19.7061 11.7678 16.2078 14.7749 12.086 14.7749H7.99046C7.57151 14.7749 7.21506 15.0795 7.14976 15.4935L5.86529 23.639L5.05918 28.7495C5.04366 28.8482 5.0497 28.9491 5.0769 29.0452C5.10409 29.1414 5.1518 29.2305 5.21673 29.3065C5.28166 29.3824 5.36228 29.4434 5.45303 29.4852C5.54378 29.5271 5.64251 29.5487 5.74244 29.5488H10.1879C10.3907 29.5488 10.5869 29.4764 10.7411 29.3446C10.8953 29.2129 10.9974 29.0305 11.0291 28.8301L12.2002 21.4033C12.2658 20.9893 12.6224 20.6842 13.0413 20.6842H15.659C19.7808 20.6842 23.2787 17.6772 23.9176 13.6026C24.3711 10.7099 22.9154 8.07846 20.4257 6.77367Z"
                              fill="#0070E0"
                            />
                            <path
                              d="M4.33794 0.00012207C4.13505 0.000128391 3.93883 0.0725717 3.78462 0.204406C3.6304 0.33624 3.52833 0.518804 3.49677 0.719219L0.00847435 22.8393C-0.00712684 22.938 -0.00113606 23.039 0.0260266 23.1352C0.0531892 23.2314 0.100881 23.3206 0.16582 23.3966C0.230758 23.4726 0.311406 23.5336 0.402197 23.5755C0.492988 23.6173 0.591769 23.639 0.691742 23.639H5.86485L7.14932 15.4935L8.54734 6.62894C8.579 6.42861 8.68112 6.24616 8.83532 6.11442C8.98952 5.98268 9.18568 5.9103 9.3885 5.91031H16.8C18.1481 5.91031 19.3781 6.22441 20.4257 6.7732C20.4971 3.06341 17.436 0.00012207 13.2267 0.00012207H4.33794Z"
                              fill="#003087"
                            />
                          </g>
                          <defs>
                            <clipPath id="clip0_565_17104">
                              <rect width={24} height="29.5487" fill="white" />
                            </clipPath>
                          </defs>
                        </svg>
                        <p>Pay via Paypal</p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
              <div
                className={`checkout-section-right-points-onee ${
                  cardIndex === 3 && "active"
                }`}
              >
                <div className="pay-radio">
                  <div className="form-group">
                    <input
                      type="radio"
                      name="card"
                      checked={cardIndex === 3}
                      onChange={() => handleCardIndex(3)}
                      id="pay-two"
                    />
                    <label htmlFor="pay-two">
                      <div className="checkout-section-right-points-right-star">
                        <svg
                          width="24"
                          height="19"
                          viewBox="0 0 24 19"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M12.5917 7.9821C12.5917 8.69877 12.1542 9.11127 11.3833 9.11127H10.3708V6.85293H11.3875C12.1542 6.85293 12.5917 7.26127 12.5917 7.9821ZM14.5708 10.5904C14.5708 10.9363 14.8708 11.1613 15.3417 11.1613C15.9417 11.1613 16.3917 10.7821 16.3917 10.2488V9.92793L15.4125 9.99043C14.8583 10.0279 14.5708 10.2321 14.5708 10.5904ZM24 2.17377V16.8404C24 17.9446 23.1042 18.8404 22 18.8404H2C0.895833 18.8404 0 17.9446 0 16.8404V2.17377C0 1.0696 0.895833 0.173767 2 0.173767H22C23.1042 0.173767 24 1.0696 24 2.17377ZM5.325 7.09877C5.675 7.12793 6.025 6.92377 6.24583 6.66543C6.4625 6.39877 6.60417 6.04043 6.56667 5.67793C6.25833 5.69043 5.875 5.8821 5.65417 6.14877C5.45417 6.37793 5.28333 6.74877 5.325 7.09877ZM7.85 10.2029C7.84167 10.1946 7.03333 9.88627 7.025 8.95293C7.01667 8.17377 7.6625 7.79877 7.69167 7.77793C7.325 7.23627 6.75833 7.17793 6.5625 7.16543C6.05417 7.13627 5.62083 7.45293 5.37917 7.45293C5.13333 7.45293 4.76667 7.17793 4.36667 7.18627C3.84583 7.1946 3.35833 7.49043 3.09583 7.96127C2.55 8.90293 2.95417 10.2946 3.48333 11.0613C3.74167 11.4404 4.05417 11.8571 4.4625 11.8404C4.85 11.8238 5.00417 11.5904 5.47083 11.5904C5.94167 11.5904 6.075 11.8404 6.48333 11.8363C6.90833 11.8279 7.17083 11.4571 7.43333 11.0779C7.72083 10.6446 7.84167 10.2279 7.85 10.2029ZM13.4917 7.97793C13.4917 6.8696 12.7208 6.11127 11.6208 6.11127H9.4875V11.7946H10.3708V9.85293H11.5917C12.7083 9.85293 13.4917 9.08627 13.4917 7.97793ZM17.2417 8.96543C17.2417 8.1446 16.5833 7.61543 15.575 7.61543C14.6375 7.61543 13.9458 8.15293 13.9208 8.88627H14.7167C14.7833 8.53627 15.1083 8.3071 15.55 8.3071C16.0917 8.3071 16.3917 8.5571 16.3917 9.02377V9.33627L15.2917 9.40293C14.2667 9.46543 13.7125 9.88627 13.7125 10.6154C13.7125 11.3529 14.2833 11.8404 15.1042 11.8404C15.6583 11.8404 16.1708 11.5613 16.4042 11.1154H16.4208V11.7988H17.2375V8.96543H17.2417ZM21.5 7.6696H20.6042L19.5667 11.0279H19.55L18.5125 7.6696H17.5833L19.0792 11.8071L19 12.0571C18.8667 12.4821 18.6458 12.6488 18.2542 12.6488C18.1833 12.6488 18.05 12.6404 17.9958 12.6363V13.3196C18.0458 13.3363 18.2667 13.3404 18.3333 13.3404C19.1958 13.3404 19.6 13.0113 19.9542 12.0154L21.5 7.6696Z"
                            fill="white"
                          />
                        </svg>

                        <p>Pay via Apple Pay</p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
              <div className="your-tickets-pay-terms">
                <div className="pay-terms">
                  <div className="pay-terms-one">
                    <div className="form-group">
                      <input type="checkbox" id="main" />
                      <label htmlFor="main">
                        <p>
                          I have read and agree to the website{" "}
                          <a href="#">Terms &amp; Conditions*</a>
                        </p>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div className="your-tickets-enter">
                <button type="button" className="your-tickets-enter-btn">
                  Enter Now
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Checkout;
