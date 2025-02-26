import axios from "axios";
import { useState } from "react";
import { toast } from "react-hot-toast";

const ForgotPassword = () => {
  const [email, setEmail] = useState<string>("");
  const [error, setError] = useState<string>("");
  const [isLoading, setIsLoading] = useState<boolean>(false);

  const handleSubmit = async () => {
    if (!email) {
      setError("Please enter your email address.");
      return;
    }
    if (!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i.test(email)) {
      setError("Invalid email address. Please check and try again.");
      return;
    }

    setIsLoading(true);
    try {
      const response = await axios.post(
        "?rest_route=/api/v1/forget_password",
        {
          email,
        },
        {
          headers: {
            Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`,
          },
        }
      );
      if (response.data.status === "error") {
        setError(response.data.error_description);
      } else {
        setError("");
        toast.success(`Password reset email sent to ${email}. `);
      }
    } catch (error) {
      console.log(error);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div>
      <div className="carp-login">
        <div className="container">
          <div className="carp-login-area">
            <div className="carp-login-head">
              <h4>Forgot Password</h4>
            </div>
            <div className="carp-login-inputs">
              <form>
                <div className="carp-login-inputs-field">
                  <input
                    type="text"
                    value={email}
                    onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                      setEmail(e.target.value)
                    }
                    placeholder="Enter your email"
                  />
                </div>
                <div className="mb-2">
                  <p className="text-danger fw-bold">{error}</p>
                </div>
              </form>
              <div className="carp-log">
                <button
                  type="button"
                  className="carp-login-btn"
                  onClick={handleSubmit}
                  disabled={isLoading}
                >
                  {isLoading ? "Sending Link..." : "Send Link"}
                </button>
              </div>
            </div>
          </div>
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
    </div>
  );
};

export default ForgotPassword;
