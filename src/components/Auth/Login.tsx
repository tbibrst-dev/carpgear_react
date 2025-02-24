import { useState } from "react";
import { AuthErrors } from "../../types";
import axios from "axios";
import { setUserState } from "../../redux/slices/userSlice";
import { useNavigate } from "react-router";
import { useDispatch } from "react-redux";
import {
  AUTH_TOKEN_KEY,
  NONCE_KEY,
  NONCE_TIMESTAMP,
  encryptToken,
} from "../../utils";
import { Link } from "react-router-dom";
import {CometChatUIKit} from "@cometchat/chat-uikit-react";


const Login = () => {
  const dispatch = useDispatch();
  const [email, setEmail] = useState<string>("");
  const [password, setPassword] = useState<string>("");
  const [errors, setErrors] = useState<AuthErrors>({
    email: "",
    password: "",
  });
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const navigate = useNavigate();

  const handleSubmit = async () => {
    // e.preventDefault();

    if (!email) {
      setErrors({ ...errors, email: "Username/Email is required" });
      return;
    }
    if (email && errors.email) {
      setErrors({ ...errors, email: "" });
    }

    if (!password) {
      setErrors({ ...errors, password: "Password is required" });
      return;
    }

    if (password && errors.password) {
      setErrors({ ...errors, password: "" });
    }

    // TODO: Implement authentication logic here
    try {
      setIsLoading(true);
      const response = await axios.post("?rest_route=/api/v1/login", {
        username: email,
        password,
      });
      if (response.data.success) {
        dispatch(setUserState(response.data.data));
        const encodedToken = encryptToken(response.data.data.token);
        localStorage.setItem(AUTH_TOKEN_KEY, encodedToken);
        localStorage.removeItem(NONCE_KEY);
        localStorage.removeItem(NONCE_TIMESTAMP);

        const cometChatid = response?.data?.data?.comchatid
        if(cometChatid){
          await CometChatUIKit.login(cometChatid);
        }

        console.log('response.data.success',response.data.data);
        navigate("/");
      } else {
        if (response.data.data.error === "incorrect_password") {
          setErrors({
            ...errors,
            password: response.data.data.error_description,
            email: "",
          });
        } else if (
          response.data.data.error === "invalid_username" ||
          response.data.data.error === "invalid_email"
        ) {
          setErrors({
            ...errors,
            email: response.data.data.error_description,
            password: "",
          });
        }
      }
    } catch (error) {
      console.log("error", error);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      <div className="carp-login">
        <div className="container">
          <div className="carp-login-area">
            <div className="carp-login-head">
              <h4>Login</h4>
            </div>
            <div className="carp-login-inputs">
              <form>
                <div className="carp-login-inputs-field">
                  <input
                    type="text"
                    placeholder="Username / Email"
                    value={email}
                    onChange={(e: React.ChangeEvent<HTMLInputElement>) => {
                      setEmail(e.target.value);
                      // setErrors({ ...errors, email: "" });
                    }}
                  />
                </div>
                {errors.email ? (
                  <div className="validated-text mb-2">
                    <p dangerouslySetInnerHTML={{ __html: errors.email }}></p>
                  </div>
                ) : null}
                <div className="carp-login-inputs-field">
                  <input
                    type="password"
                    placeholder="Password"
                    value={password}
                    onChange={(e: React.ChangeEvent<HTMLInputElement>) => {
                      setPassword(e.target.value);
                      // setErrors({ ...errors, password: "" });
                    }}
                  />
                </div>
                {errors.password ? (
                  <div className="validated-text mb-1">
                    <p>{errors.password}</p>
                  </div>
                ) : null}
                <div className="carp-login-checks">
                  <div className="carp-login-check-left">
                    <div className="form-group">
                      <input type="checkbox" id="forget" />
                      <label htmlFor="forget">Remember me</label>
                    </div>
                  </div>
                </div>
              </form>
              <div className="carp-login-forget">
                <Link to="/auth/forgot/password">Forgot Password?</Link>
              </div>
              <div className="carp-log">
                <button
                  type="button"
                  className="carp-login-btn"
                  onClick={handleSubmit}
                  disabled={isLoading}
                >
                  {isLoading ? "Please wait..." : "Login"}
                </button>
              </div>
            </div>
          </div>

          {/* <div className="login-need-accounts">
            <div className="">
              <div className="login-need-account">
                <p>Need an account?</p>
                <button type="button" className="check-login">
                  <svg
                    width={14}
                    height={15}
                    viewBox="0 0 14 15"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
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
                  Register
                </button>
              </div>
            </div>
          </div>
           */}
        </div>
      </div>

      <div className="single-comp-mob-show">
        <div className="container">
          <div className="mob-get-exclusive">
            <div className="mob-get-exclusive-all">
              <div className="mob-get-exclusive-txt get-sign">
                <h2>Get exclusive offers</h2>
                <p>
                  Get Exclusive Competitions &amp; Offers Available Only For Our
                  App Users.
                </p>
                <div className="mob-get-exc-icon">
                  <a href="#">
                    {" "}
                    <img src="/images/get-exc-2.png" alt="" />
                  </a>
                  <a href="#">
                    {" "}
                    <img src="/images/get-exc-1.png" alt="" />{" "}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default Login;
