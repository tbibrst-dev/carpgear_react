import axios from "axios";
import React, { useState } from "react";
import { useLocation } from "react-router";
import { Link } from "react-router-dom";
import { LEGAL_TERMS_ACTIVE_INDEX } from "../utils";

interface Errors {
  name?: string;
  email?: string;
}

const Footer = () => {
  const { pathname } = useLocation();
  const [name, setName] = useState<string>("");
  const [email, setEmail] = useState<string>("");
  const [error, setError] = useState<Errors>({ name: "", email: "" });
  const [isLoading, setIsLoading] = useState(false);
  const [apiSuccess, setApiSuccess] = useState<string>("");
  const [apiError, setApiError] = useState<string>("");

  const handleSubscribe = async () => {
    let errors: Errors = {};

    if (!name) {
      errors.name = "Please enter your name";
    }

    if (!email) {
      errors.email = "Please enter your email";
    } else if (!/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[A-Za-z]+$/.test(email)) {
      errors.email = "Please enter a valid email";
    }

    if (Object.keys(errors).length > 0) {
      setError(errors);
      return;
    }

    //TODO: Call the api and Add the user to the mailing list here.
    setIsLoading(true);
    try {
      const response = await axios.post(
        "?rest_route=/api/v1/subscribe_mailing",
        { name, email },
        { headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` } }
      );

      if (response.data.success === "false") {
        setApiSuccess("");
        setApiError(response.data.message);
      } else {
        setApiError("");
        setApiSuccess(response.data.message);
      }
    } catch (error) {
      console.log(error);
    } finally {
      setIsLoading(false);
      setName("");
      setEmail("");
      setTimeout(() => setApiSuccess(""), 10000);
      setTimeout(() => setApiError(""), 10000);
    }
  };

  return (
    <>
      <div className="footer-section">
        <div className="container">
          <div
            className={`${
              pathname === "/"
                ? "image-contact-field"
                : "image-contact-field-comp"
            }`}
          >
            <div className="form-footer">
              <h2>Sign up to our mailing list</h2>
              <p>
                Sign up to our mailing list to get the latest news and offers.
              </p>
              <div className="row">
                <div className="col-lg-6 col-md-6 col-sm-12 form-input-contact">
                  <div className="mb-3">
                    <input
                      type="text"
                      className="form-control"
                      id="name"
                      placeholder="Name"
                      value={name}
                      onChange={(e: React.ChangeEvent<HTMLInputElement>) => {
                        setName(e.target.value);
                        setError({ ...error, name: "" });
                      }}
                    />
                    {error?.name && (
                      <span
                        className="footer-error"
                        style={{ color: "red", fontWeight: 700 }}
                      >
                        {error.name}
                      </span>
                    )}
                    {apiSuccess && (
                      <span
                        className="footer-success"
                        style={{ color: "#0fcf0fed", fontWeight: 600 }}
                      >
                        {apiSuccess}
                      </span>
                    )}
                    {apiError && (
                      <span
                        className="footer-error"
                        style={{ color: "red", fontWeight: 700 }}
                      >
                        {apiError}
                      </span>
                    )}
                  </div>
                </div>
                <div className="col-lg-6 col-md-6 col-sm-12 form-input-contact">
                  <div className="mb-3">
                    <input
                      type="email"
                      className="form-control"
                      id="email"
                      placeholder="Email"
                      value={email}
                      onChange={(e: React.ChangeEvent<HTMLInputElement>) => {
                        setEmail(e.target.value);
                        setError({ ...error, email: "" });
                      }}
                    />
                    {error?.email && (
                      <span style={{ color: "red", fontWeight: 700 }}>
                        {error.email}
                      </span>
                    )}
                  </div>
                </div>
                <div className="col-sm-12 sub">
                  <div className="contact-subscribe">
                    <button
                      type="button"
                      className="btn"
                      onClick={handleSubscribe}
                      disabled={isLoading}
                    >
                      {isLoading ? "Subscribing..." : "Subscribe"}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      {/* copy right section start here */}
      <div className="copy-right">
        <div className="container">
          <div className="mobile-copt-txt">
            <h2>Carp Gear Giveaway is better with our IOS / Android app</h2>
          </div>
          <div className="copy-right-all">
            <div className="row copy-right-all-align">
              <div className="col-lg-6">
                <div className="copy-right-left">
                  <div className="copy-right-left-one">
                    <a
                      href="https://apps.apple.com/us/app/carp-gear-giveaways/id1513020494"
                      target="_blank"
                    >
                      <img src="/images/single-comp-top.svg" alt="" />{" "}
                    </a>
                    <a
                      href="https://play.google.com/store/apps/details?id=co.uk.carpgeargiveaways.app"
                      target="_blank"
                    >
                      <img src="/images/single-comp-top-1.svg" alt="" />
                    </a>
                  </div>
                  <div className="copy-right-left-two">
                    {/* <div className="copy-right-left-social">
                      <a
                        href="https://www.twitter.com/carpgearcgg"
                        target="_blank"
                      >
                        <svg
                          width={24}
                          height={24}
                          viewBox="0 0 24 24"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M18.2437 2.25H21.5531L14.325 10.5094L22.8281 21.75H16.1719L10.9547 14.9344L4.99216 21.75H1.6781L9.40779 12.9141L1.25623 2.25H8.08123L12.7922 8.47969L18.2437 2.25ZM17.0812 19.7719H18.914L7.08279 4.125H5.11404L17.0812 19.7719Z"
                            fill="white"
                          />
                        </svg>
                      </a>
                    </div> */}
                    <div className="copy-right-left-social">
                      <a
                        href="https://www.facebook.com/carpgeargiveaways/"
                        target="_blank"
                      >
                        <svg
                          width={24}
                          height={24}
                          viewBox="0 0 24 24"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M23.2916 12C23.2916 5.57812 18.0885 0.375 11.6666 0.375C5.24475 0.375 0.041626 5.57812 0.041626 12C0.041626 17.8022 4.29272 22.6116 9.85022 23.4844V15.3605H6.89709V12H9.85022V9.43875C9.85022 6.52547 11.5846 4.91625 14.241 4.91625C15.5132 4.91625 16.8435 5.14313 16.8435 5.14313V8.0025H15.3773C13.9335 8.0025 13.483 8.89875 13.483 9.81797V12H16.7071L16.1915 15.3605H13.483V23.4844C19.0405 22.6116 23.2916 17.8022 23.2916 12Z"
                            fill="white"
                          />
                        </svg>
                      </a>
                    </div>
                    <div className="copy-right-left-social">
                      <a
                        href="https://www.instagram.com/carpgeargiveaways/"
                        target="_blank"
                      >
                        <svg
                          width={25}
                          height={26}
                          viewBox="0 0 25 26"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M12.3386 6.8108C8.93146 6.8108 6.18324 9.57173 6.18324 12.9947C6.18324 16.4176 8.93146 19.1785 12.3386 19.1785C15.7457 19.1785 18.494 16.4176 18.494 12.9947C18.494 9.57173 15.7457 6.8108 12.3386 6.8108ZM12.3386 17.015C10.1368 17.015 8.33681 15.212 8.33681 12.9947C8.33681 10.7773 10.1315 8.97434 12.3386 8.97434C14.5457 8.97434 16.3404 10.7773 16.3404 12.9947C16.3404 15.212 14.5404 17.015 12.3386 17.015ZM20.1815 6.55785C20.1815 7.35976 19.5386 8.00021 18.7457 8.00021C17.9475 8.00021 17.31 7.35437 17.31 6.55785C17.31 5.76132 17.9529 5.11548 18.7457 5.11548C19.5386 5.11548 20.1815 5.76132 20.1815 6.55785ZM24.2582 8.02173C24.1672 6.08962 23.7279 4.37816 22.319 2.96809C20.9154 1.55802 19.2118 1.1167 17.2886 1.01982C15.3065 0.906803 9.36538 0.906803 7.38324 1.01982C5.46538 1.11132 3.76181 1.55264 2.35288 2.96271C0.943956 4.37278 0.510027 6.08423 0.413599 8.01635C0.301099 10.0077 0.301099 15.9762 0.413599 17.9676C0.50467 19.8997 0.943956 21.6111 2.35288 23.0212C3.76181 24.4313 5.46003 24.8726 7.38324 24.9695C9.36538 25.0825 15.3065 25.0825 17.2886 24.9695C19.2118 24.878 20.9154 24.4367 22.319 23.0212C23.7225 21.6111 24.1618 19.8997 24.2582 17.9676C24.3707 15.9762 24.3707 10.0131 24.2582 8.02173ZM21.6975 20.1042C21.2797 21.1591 20.4707 21.9717 19.4154 22.3969C17.835 23.0266 14.085 22.8813 12.3386 22.8813C10.5922 22.8813 6.83681 23.0212 5.26181 22.3969C4.21181 21.9771 3.40288 21.1644 2.97967 20.1042C2.35288 18.5165 2.49753 14.7492 2.49753 12.9947C2.49753 11.2401 2.35824 7.46739 2.97967 5.8851C3.39753 4.83024 4.20646 4.01757 5.26181 3.59239C6.84217 2.96271 10.5922 3.10802 12.3386 3.10802C14.085 3.10802 17.8404 2.96809 19.4154 3.59239C20.4654 4.01219 21.2743 4.82486 21.6975 5.8851C22.3243 7.47278 22.1797 11.2401 22.1797 12.9947C22.1797 14.7492 22.3243 18.5219 21.6975 20.1042Z"
                            fill="white"
                          />
                        </svg>
                      </a>
                    </div>
                    <div className="copy-right-left-social">
                      <a
                        href="https://www.tiktok.com/@carpgeargiveawaysltd"
                        target="_blank"
                      >
                        <svg
                          width={24}
                          height={26}
                          viewBox="0 0 24 26"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M24 10.6622C21.6408 10.6675 19.3395 9.97015 17.4211 8.66852V17.7464C17.4205 19.4278 16.8783 21.0688 15.8671 22.4502C14.856 23.8316 13.424 24.8874 11.7627 25.4766C10.1013 26.0657 8.28986 26.16 6.57045 25.7469C4.85104 25.3338 3.30566 24.433 2.14095 23.1649C0.976234 21.8969 0.247702 20.3219 0.0527683 18.6508C-0.142166 16.9796 0.20579 15.2919 1.05011 13.8133C1.89443 12.3346 3.19486 11.1356 4.77753 10.3764C6.36019 9.61725 8.14965 9.33417 9.90663 9.56504V14.1309C9.10263 13.8912 8.23929 13.8984 7.43988 14.1516C6.64047 14.4047 5.94589 14.8908 5.45531 15.5404C4.96474 16.19 4.70326 16.97 4.70823 17.7689C4.71319 18.5677 4.98434 19.3447 5.48295 19.9888C5.98157 20.6329 6.68215 21.1112 7.48464 21.3554C8.28714 21.5996 9.15052 21.5972 9.95148 21.3485C10.7524 21.0998 11.45 20.6176 11.9446 19.9707C12.4392 19.3238 12.7055 18.5454 12.7055 17.7464V0H17.4211C17.4179 0.377471 17.4512 0.754428 17.5208 1.12611C17.6847 1.95582 18.0254 2.74512 18.5221 3.44574C19.0188 4.14635 19.6611 4.74355 20.4097 5.20081C21.4747 5.86829 22.7233 6.22406 24 6.2238V10.6622Z"
                            fill="white"
                          />
                        </svg>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div className="col-lg-6">
                <div className="copy-right-right">
                  <a href="#" title="payment">
                    {" "}
                    <img
                      src="/images/Payment-IconsFooter.png"
                      srcSet="/images/Payment-IconsFooter.png 1x, /images/Payment-IconsFooter2x.png 2x,"  
                      alt="Payment-Icons"
                    />
                    
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* copy right section end here */}
      <div className="privacy-section-footer">
        <div className="container">
          <ul>
            <li>
              <Link
                to="/legal-terms?tab=1"
                onClick={() =>
                  localStorage.setItem(LEGAL_TERMS_ACTIVE_INDEX, "1")
                }
                title="Competition Terms & Conditions"
              >
                Competition Terms &amp; Conditions
              </Link>
            </li>
            <li>
            <Link
                to="/legal-terms?tab=2"
                onClick={() =>
                  localStorage.setItem(LEGAL_TERMS_ACTIVE_INDEX, "2")
                }
                title="Privacy Policy & Cookie Policy"
              >
                Website Terms of Use
                </Link>
            </li>
            <li>
              <Link
                to="/legal-terms?tab=3"
                onClick={() =>
                  localStorage.setItem(LEGAL_TERMS_ACTIVE_INDEX, "3")
                }
                title="Privacy Policy & Cookie Policy"
              >
                Privacy Policy &amp; Cookie Policy
              </Link>
            </li>
            <li>
              <a href="/free-postal-route" title="Free Postal Route">
                Free Postal Route
              </a>
            </li>
            <li>
              <a href="/faq" title="FAQ">
                FAQ
              </a>
            </li>
            <li>
              <Link to="/contact" title="Contact">
                Contact
              </Link>
            </li>
          </ul>
          <h6>
            Copyright ©Carp Gear Giveaways- Registered Company Number 12385280
            <br />
            Trademarked - UK00003485092
          </h6>
          <h6 className="images-found">
            Images Found On This Website are Copyright Protected! Do NOT
            Download or Use For Commercial Use!
          </h6>
          
        </div>
      </div>
    </>
  );
};

export default Footer;
