import axios from "axios";
import { useState } from "react";
import toast from "react-hot-toast";
import { useNavigate } from "react-router";

const ResetPassword = () => {
  const params = new URLSearchParams(location.search);
  const key = params.get("key");
  const id = params.get("id");
  const [password, setPassword] = useState<string>("");
  const [confirmPassword, setConfirmPassword] = useState<string>("");
  const [passwordError, setPasswordError] = useState<string>("");
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [confirmPasswordError, setConfirmPasswordError] = useState<string>("");
  const [passwordStrength, setPasswordStrength] = useState<string>("");
  const [errorClass, setErrorClass] = useState<string>("short");
  const [showsStrength, setShowStrength] = useState<boolean>(false);
  const [showHint, setShowHint] = useState<boolean>(true);
  const navigate = useNavigate();

  const handlePasswordChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { value } = e.target;
    setShowStrength(true);
    if (!value) {
      setErrorClass("short");
      setPasswordStrength("Please enter a password");
      setPassword(value);
      setShowHint(true);
      return;
    }

    const charactorCount = hasAlphanumeric(value);

    if (charactorCount < 2) {
      setErrorClass("short");
      setPasswordStrength("Very weak - Please enter a stronger password.");
    } else if (charactorCount < 3) {
      setErrorClass("good");
      setPasswordStrength("Medium");
    } else if (value.length < 12) {
      setPasswordStrength("Password length should of 12 characters");
      setShowHint(false);
      setErrorClass("good");
    } else {
      setPasswordStrength("Strong");
      setErrorClass("strong");
    }
    setPassword(value);
  };

  const hasAlphanumeric = (value: string): number => {
    let typesCount = 0;
    if (/[a-zA-Z]/.test(value)) typesCount++; // Contains letters
    if (/\d/.test(value)) typesCount++; // Contains numbers
    if (/[@$!%*?&]/.test(value)) typesCount++; // Contains special characters
    return typesCount;
  };

  const handleResetPassword = async () => {
    const alphanumericRegex =
    /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`])[A-Za-z\d!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]{12,}$/;
  setShowStrength(false);
    if (!password) {
      setPasswordError("Please enter a password");
      return;
    }

    if (password.length < 12) {
      setPasswordError("Password length should of 12 characters");
      return;
    }

    const regexTestResult = alphanumericRegex.test(password);
    console.log(`Regex Test Result: ${regexTestResult}`); // Debugging line
    console.log(`Regex Test Result texyt: ${password}`); // Debugging line

    if (!alphanumericRegex.test(password)) {
      setPasswordError(
        "Password must contain one letter, number and special charactor."
      );
      return;
    }

    if (!confirmPassword) {
      setPasswordError("");
      setConfirmPasswordError("Please enter confirm password");
      return;
    }
    if (password !== confirmPassword) {
      setConfirmPasswordError("Password and confirm password must be the same");
      return;
    }

    setIsLoading(true);
    setPasswordError("");
    setConfirmPasswordError("");
    try {
      const response = await axios.post(
        "?rest_route=/api/v1/reset_password",
        { key, new_password: password, login: id },
        {
          headers: {
            Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`,
          },
        }
      );

      if (response.data.status === "error") {
        setConfirmPasswordError(response.data.error_description);
      } else {
        toast.success("Password reset successfully!");
        navigate("/auth/login");
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
              <h4>Create new Password</h4>
            </div>
            <div className="carp-login-inputs">
              <form>
                <div className="carp-login-inputs-field">
                  <input
                    type="password"
                    value={password}
                    onChange={handlePasswordChange}
                    placeholder="Enter new password"
                  />
                </div>
                {passwordError && !showsStrength ? (
                  <div className="mb-2">
                    <p className="error fw-bold">{passwordError}</p>
                  </div>
                ) : null}
                {showsStrength && passwordStrength ? (
                  <>
                    <div
                      className={`mb-2 woocommerce-password-strength ${errorClass}`}
                    >
                      {passwordStrength}
                    </div>
                    {showHint && (
                      <small className="woocommerce-password-hint text-white">
                        Hint: The password should be at least twelve characters
                        long. To make it stronger, use upper and lower case
                        letters, numbers, and symbols like ! " ? $ % ^ & ).
                      </small>
                    )}
                  </>
                ) : null}
                <div className="carp-login-inputs-field">
                  <input
                    type="password"
                    value={confirmPassword}
                    onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
                      setConfirmPassword(e.target.value)
                    }
                    placeholder="Confirm new password"
                  />
                </div>
                {confirmPasswordError && (
                  <div>
                    <p className="error fw-bold">{confirmPasswordError}</p>
                  </div>
                )}
              </form>
              <div className="carp-log mt-2">
                <button
                  type="button"
                  className="carp-login-btn"
                  onClick={handleResetPassword}
                >
                  {isLoading ? "Reseting password..." : "create"}
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

export default ResetPassword;
