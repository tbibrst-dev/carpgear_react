import { useState, useEffect, useRef } from "react";
import { AUTH_TOKEN_KEY, decryptToken } from "../../utils";
import useGetUserDetails from "./hooks/getUserDetails";
import useUpdateUserAccountDetails from "./hooks/updateUserAccountDetails";
import toast from "react-hot-toast";
import { User } from "../../types";


// type Props = {
//   user: User;
//   isUpdating: boolean;
// };

const UserDetails = () => {
  const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
  const token = decryptToken(encodedToken);
  const { data: userDetails, isLoading } = useGetUserDetails(token);
  const { isLoadingSave, error, isSuccess, updateUserDetails } = useUpdateUserAccountDetails(token);
  console.log(error);
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

  });
  const [formErrors, setFormErrors] = useState({
    first_name: "",
    last_name: "",
    email: "",
    billing_phone: "",
    billing_address_1: "",
    billing_address_2: "",
    billing_city: "",
    billing_postcode: "",
    billing_state: "",

  });


  // Refs for input fields
  const first_nameRef = useRef<HTMLInputElement>(null);
  const last_nameRef = useRef<HTMLInputElement>(null);
  const emailRef = useRef<HTMLInputElement>(null);
  const billingPhoneRef = useRef<HTMLInputElement>(null);
  const billingAddress1Ref = useRef<HTMLInputElement>(null);
  const billingAddress2Ref = useRef<HTMLInputElement>(null);
  const billingCityRef = useRef<HTMLInputElement>(null);
  const billingPostcodeRef = useRef<HTMLInputElement>(null);
  // const billingStateRef = useRef<HTMLInputElement>(null);


  useEffect(() => {
    if (isSuccess) {
      toast.success("Details updated successfully!");
    }
  }, [isSuccess]);

  useEffect(() => {
    if (userDetails) {
      setFormData((prevFormData: any) => ({
        ...prevFormData,
        first_name: userDetails.first_name || "",
        last_name: userDetails.last_name || "",
        email: userDetails.email || "",
        billing_phone: userDetails.billing_phone || "",
        billing_address_1: userDetails.billing_address_1 || "",
        billing_address_2: userDetails.billing_address_2 || "",
        billing_city: userDetails.billing_city || "",
        billing_postcode: userDetails.billing_postcode || "",
        billing_state: userDetails.billing_state || "",
      }));
    }
  }, [userDetails]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData((prevFormData: any) => ({
      ...prevFormData,
      [name]: value,
    }));
  };

  const validateForm = (): boolean => {
    let isValid = true;
    const errors: any = {};
    let focusField: React.RefObject<HTMLInputElement> | null = null;

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

    // if (!formData.email) {
    //   errors.email = "Email address is required.";
    //   isValid = false;
    // }

    if (!formData.email) {
      errors.email = "Email address is required.";
      isValid = false;
      focusField = focusField || emailRef;
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      errors.email = "Email address is invalid.";
      isValid = false;
      focusField = focusField || emailRef;
    }

    if (!formData.billing_phone) {
      errors.billing_phone = "Phone No. is required.";
      isValid = false;
      focusField = focusField || billingPhoneRef;
    }

    if (!formData.billing_address_1) {
      errors.billing_address_1 = "House Name/No. is required.";
      isValid = false;
      focusField = focusField || billingAddress1Ref;
    }
    if (!formData.billing_address_2) {
      errors.billing_address_2 = "Street Address is required.";
      isValid = false;
      focusField = focusField || billingAddress2Ref;
    }
    if (!formData.billing_city) {
      errors.billing_city = "Town/City is required.";
      isValid = false;
      focusField = focusField || billingCityRef;
    }
    if (!formData.billing_postcode) {
      errors.billing_postcode = "Postcode is required.";
      isValid = false;
      focusField = focusField || billingPostcodeRef;
    }

    if (focusField) {
      const offset = 100; // Adjust this value to ensure the entire input field is visible
      const top = focusField.current?.getBoundingClientRect().top || 0;
      const scrolledY = window.scrollY + top - offset;
      window.scrollTo({ top: scrolledY, behavior: "smooth" });
      focusField.current?.focus();
    }

    setFormErrors(errors);
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

        <div className="user-main-form-div user-billing-section">
          <div className="account-section-right-side user-details-form-section mb-4">
            {isLoadingSave && (
              <div className="basket-loader-container">
                <svg viewBox="25 25 50 50" className="loader-svg">
                  <circle r={20} cy={50} cx={50} className="loader" />
                </svg>
              </div>
            )}
            <div className="form-row row first-two-rows">
              <div className="form-group col-md-6 top-four-coulmn lef_t">
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
              <div className="form-group col-md-6 top-four-coulmn rig_t" >
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
            <div className="form-row row first-two-rows">
              <div className="form-group col-md-6 top-four-coulmn lef_t">
                <label htmlFor="billing_phone">PHONE</label>
                <input
                  type="text"
                  name="billing_phone"
                  value={formData.billing_phone}
                  onChange={handleChange}
                  className="form-control"
                  id="billing_phone"
                  placeholder="Phone*"
                  ref={billingPhoneRef}
                />
                <span className="text-danger">{formErrors.billing_phone}</span>
              </div>
              <div className="form-group col-md-6 top-four-coulmn rig_t">
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




            <div className="form-row row">
              <div className="form-group col-md-12">
                <label htmlFor="billing_address_1">STREET ADDRESS</label>
                <input
                  type="text"
                  name="billing_address_1"
                  value={formData.billing_address_1}
                  onChange={handleChange}
                  className="form-control"
                  id="billing_address_1"
                  placeholder="House Name / No.*"
                  ref={billingAddress1Ref}

                />
                <span className="text-danger">{formErrors.billing_address_1}</span>

              </div>
            </div>

            <div className="form-row row">
              <div className="form-group col-md-12">

                <input
                  type="text"
                  name="billing_address_2"
                  value={formData.billing_address_2}
                  onChange={handleChange}
                  className="form-control"
                  id="billing_address_2"
                  placeholder="Street Address*"
                  ref={billingAddress2Ref}

                />
                <span className="text-danger">{formErrors.billing_address_2}</span>

              </div>
            </div>

            <div className="form-row row">
              <div className="form-group col-md-12">

                <input
                  type="text"
                  name="billing_city"
                  value={formData.billing_city}
                  onChange={handleChange}
                  className="form-control"
                  id="billing_city"
                  placeholder="Town / City*"
                  ref={billingCityRef}

                />
                <span className="text-danger">{formErrors.billing_city}</span>

              </div>
            </div>


            <div className="form-row row">
              <div className="form-group col-md-6 last-row-last-column lef_t">

                <input
                  type="text"
                  name="billing_postcode"
                  value={formData.billing_postcode}
                  onChange={handleChange}
                  className="form-control"
                  id="billing_postcode"
                  placeholder="Postcode*"
                  ref={billingPostcodeRef}

                />
                <span className="text-danger">{formErrors.billing_postcode}</span>

              </div>

              <div className="form-group col-md-6 leif  rig_t">
                <input
                  type="text"
                  name="billing_state"
                  value={formData.billing_state}
                  onChange={handleChange}
                  className="form-control"
                  id="billing_state"
                  placeholder="County (optional)"

                />

              </div>

            </div>

            <div className="country-regtion-div">
              <h5>
                Country/Region:
                <span>
                  United Kindgom
                </span>
              </h5>

            </div>
            <div className="user-biiling-update-div">
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
