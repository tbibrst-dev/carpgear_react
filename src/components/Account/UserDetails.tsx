import { useState, useEffect, useRef } from "react";
import { AUTH_TOKEN_KEY, decryptToken } from "../../utils";
import useGetUserDetails from "./hooks/getUserDetails";
import useUpdateUserDetails from "./hooks/updateUserProfileDetails";
import toast from "react-hot-toast";
import { User } from "../../types";


type Props = {
  user: User;
  handleUpdateUser: (user: User) => void;
  isUpdating: boolean;
};


const UserDetails: React.FC<Props> = ({ }) => {
  const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
  const token = decryptToken(encodedToken);
  const { data: userDetails, isLoading } = useGetUserDetails(token);
  const { isLoadingSave, error, isSuccess, updateUserDetails } = useUpdateUserDetails(token);

  const [passwordStrength, setPasswordStrength] = useState<string>("");
  const [errorClass, setErrorClass] = useState<string>("short");
  const [showsStrength, setShowStrength] = useState<boolean>(false);

  const [showHint, setShowHint] = useState<boolean>(true);




  const [formData, setFormData] = useState<User>({
    billing_address_1: "",
    billing_address_2: "",
    billing_city: "",
    billing_company: "",
    billing_country: "",
    billing_email: "",
    billing_first_name: "",
    billing_last_name: "",
    billing_postcode: "",
    billing_phone: "",
    billing_state: "",
    description: "",
    email: "",
    first_name: "",
    last_name: "",
    limit_duration: "",
    limit_value: "",
    lockout_period: "",
    name: "",
    nickname: "",
    token: "",
    current_spending: "",
    lock_account: "",
    locking_period: "",
    currentPassword: "",
    newPassword: "",
    confirmPassword: "",
  });
  const [formErrors, setFormErrors] = useState({
    first_name: "",
    last_name: "",
    email: "",
    password: "",
    newpassword: ""
  });
  // Refs for input fields
  const first_nameRef = useRef<HTMLInputElement>(null);
  const last_nameRef = useRef<HTMLInputElement>(null);
  const emailRef = useRef<HTMLInputElement>(null);
  const currentPasswordRef = useRef<HTMLInputElement>(null);
  const newPasswordRef = useRef<HTMLInputElement>(null);
  const confirmPasswordRef = useRef<HTMLInputElement>(null);


  useEffect(() => {
    if (isSuccess) {
      toast.success("Details updated successfully!");
    }
  }, [isSuccess]);

  useEffect(() => {
    if (error) {
      toast.error(error);
    }
  }, [error]);

  useEffect(() => {
    if (userDetails) {
      setFormData((prevFormData: any) => ({
        ...prevFormData,
        first_name: userDetails.first_name || "",
        last_name: userDetails.last_name || "",
        email: userDetails.email || "",
      }));
    }
  }, [userDetails]);

  const hasAlphanumeric = (value: string): number => {
    let typesCount = 0;
    if (/[a-zA-Z]/.test(value)) typesCount++; // Contains letters
    if (/\d/.test(value)) typesCount++; // Contains numbers
    if (/[@$!%*?&]/.test(value)) typesCount++; // Contains special characters
    return typesCount;
  };


  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setShowStrength(true);

    setFormData((prevFormData: any) => ({
      ...prevFormData,
      [name]: value,
    }));

    if (name === 'newPassword') {

      if (!value) {
        setErrorClass("short");
        setPasswordStrength("Please enter a password");
        setShowHint(true);
      }

      const charactorCount = hasAlphanumeric(value);
      if (charactorCount < 2) {
        setErrorClass("short");
        setPasswordStrength("Very weak - Please enter a stronger password.");
      } else if (charactorCount < 3) {
        setErrorClass("good");
        setPasswordStrength("Medium");
      } else if (value.length < 12) {
        setPasswordStrength("Password length should be at least 12 characters.");
        setShowHint(false);
        setErrorClass("good");
      } else {
        setPasswordStrength("Strong");
        setErrorClass("strong");
      }



    };
  };



    const validateForm = (): boolean => {
      let isValid = true;
      const errors: any = {};
      let focusField: React.RefObject<HTMLInputElement> | null = null;
      const alphanumericRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`])[A-Za-z\d!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]{12,}$/;

      if (!formData.first_name) {
        errors.first_name = "First Name is required.";
        isValid = false;
        focusField = focusField || first_nameRef;
      }

      if (!formData.last_name) {
        errors.last_name = "Last Name is required.";
        isValid = false;
        focusField = focusField || last_nameRef;
      }

      if (!formData.email) {
        errors.email = "Email address is required.";
        isValid = false;
        focusField = focusField || emailRef;
      } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
        errors.email = "Email address is invalid.";
        isValid = false;
        focusField = focusField || emailRef;
      }

      if ((formData.currentPassword && !formData.newPassword) || (formData.newPassword && !formData.confirmPassword)) {
        errors.password = "Please Fill all password fields.";
        isValid = false;
        focusField = focusField || currentPasswordRef;
      }

      if (formData.newPassword && !alphanumericRegex.test(formData.newPassword)) {
        errors.newpassword = "Password must contain one letter, number and special charactor.";
        isValid = false;
        focusField = focusField || newPasswordRef;
      }

      if (formData.newPassword != formData.confirmPassword) {
        errors.newpassword = "New password and Confirm password do not match.";
        isValid = false;
        focusField = focusField || newPasswordRef;
      }
      setFormErrors(errors);
      if (focusField) {
        const offset = 100; // Adjust this value to ensure the entire input field is visible
        const top = focusField.current?.getBoundingClientRect().top || 0;
        const scrolledY = window.scrollY + top - offset;
        window.scrollTo({ top: scrolledY, behavior: "smooth" });
        focusField.current?.focus();
      }
      return isValid;
    };

    const handleSave = async () => {
      try {
        if (!validateForm()) {
          return;
        }


        await updateUserDetails(formData);


        // handleUpdateUser(updatedUser);
      } catch (error) {
        console.error("Error updating user details:", error);
      }
    };

    return (
      isLoading ? (
        <div className="basket-loader-container">
          <svg viewBox="25 25 50 50" className="loader-svg">
            <circle r={20} cy={50} cx={50} className="loader" />
          </svg>
        </div>
      ) : (
        <>

          <div className="user-main-form-div">
            <div className="account-section-right-side user-details-form-section mb-4">
              {isLoadingSave && (
                <div className="basket-loader-container">
                  <svg viewBox="25 25 50 50" className="loader-svg">
                    <circle r={20} cy={50} cx={50} className="loader" />
                  </svg>
                </div>
              )}
              <div className="form-row row">
                <div className="form-group col-md-6 first-name">
                  <label htmlFor="first_name">FIRST NAME</label>
                  <input
                    type="text"
                    name="first_name"
                    value={formData.first_name}
                    onChange={handleChange}
                    className="form-control"
                    id="first_name"
                    placeholder="First Name*"
                    ref={first_nameRef}

                  />
                  <span className="text-danger">{formErrors.first_name}</span>
                </div>
                <div className="form-group col-md-6">
                  <label htmlFor="last_name">LAST NAME</label>
                  <input
                    type="text"
                    name="last_name"
                    value={formData.last_name}
                    onChange={handleChange}
                    className="form-control"
                    id="last_name"
                    placeholder="Last Name*"
                    ref={last_nameRef}
                  />
                  <span className="text-danger">{formErrors.last_name}</span>
                </div>
              </div>
              <div className="form-row row email_div">
                <div className="form-group col-md-12">
                  <label htmlFor="email">EMAIL ADDRESS</label>
                  <input
                    type="email"
                    name="email"
                    value={formData.email}
                    onChange={handleChange}
                    className="form-control"
                    id="email"
                    placeholder="Email Address*"
                    ref={emailRef}
                  />
                  <span className="text-danger">{formErrors.email}</span>
                </div>
              </div>
            </div>
            <div className="account-section-right-side user-details-form-section">
              <h4 className="mb-3 headingh4">PASSWORD CHANGE</h4>
              <span className="text-danger">{formErrors.password}</span>
              <div className="form-row row">
                <div className="form-group col-md-12">
                  <label htmlFor="currentPassword" className="label-flex">CURRENT PASSWORD  <span >(leave blank for leave unchanged)</span></label>
                  <input
                    type="password"
                    name="currentPassword"
                    value={formData.currentPassword}
                    onChange={handleChange}
                    className="form-control"
                    id="currentPassword"
                    placeholder="Current Password"
                    ref={currentPasswordRef}

                  />

                </div>
              </div>
              <span className="text-danger">{formErrors.newpassword}</span>
              <div className="form-row row">
                <div className="form-group col-md-6 password-input">
                  <label htmlFor="newPassword" className="label-flex">NEW PASSWORD <span >(leave blank for leave unchanged)</span></label>

                  <input
                    type="password"
                    name="newPassword"
                    value={formData.newPassword}
                    onChange={handleChange}
                    className="form-control mb-1"
                    id="newPassword"
                    placeholder="New Password"
                    ref={newPasswordRef}

                  />
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

                </div>

                <div className="form-group col-md-6">
                  <label htmlFor="confirmPassword">CONFIRM PASSWORD</label>
                  <input
                    type="password"
                    name="confirmPassword"
                    value={formData.confirmPassword}
                    onChange={handleChange}
                    className="form-control"
                    id="confirmPassword"
                    placeholder="Confirm Password"
                    ref={confirmPasswordRef}

                  />
                </div>
              </div>
              <div className="user-profile-update-div">
                <button onClick={handleSave} disabled={isLoading || isLoadingSave} className="btn btn-warning submit-button">
                  {isLoadingSave ? 'SAVING...' : 'SAVE CHANGES'}
                </button>
              </div>
            </div>
          </div>
        </>
      )
    );
  };

  export default UserDetails;
