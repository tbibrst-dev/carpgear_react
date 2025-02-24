import { useState, useEffect } from "react";
import { AUTH_TOKEN_KEY, decryptToken } from "../../utils";
import useGetUserDetails from "./hooks/getUserDetails";
import useupdateUserFinancilaDetails from "./hooks/updateUserFinancilaDetails";
import toast from "react-hot-toast";

const UserFinancialDetails = () => {
  const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
  const token = decryptToken(encodedToken);
  const { data: userDetails, isLoading } = useGetUserDetails(token);
  const { isLoadingSave, error , isSuccess, updateUserFinancilaDetails } = useupdateUserFinancilaDetails(token);

  const [formData, setFormData] = useState({
    account_number: "",
    confirm_account_number: "",
    sort_code: "",
  });

  const [formErrors, setFormErrors] = useState({    
    account_number: "",
    confirm_account_number: "",
    sort_code: "",
  });

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
      setFormData({
        account_number: userDetails.account_number || "",
        confirm_account_number: userDetails.account_number || "",
        sort_code: userDetails.sort_code || "",
      });
    }
  }, [userDetails]);


  console.log('userDetails',userDetails);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    if (name === "account_number" || name === "confirm_account_number") {
      // Only allow numeric input for account number fields
      const numericValue = value.replace(/\D/g, '');
      setFormData((prevFormData) => ({
        ...prevFormData,
        [name]: numericValue,
      }));
    } else {
      setFormData((prevFormData) => ({
        ...prevFormData,
        [name]: value,
      }));
    }
  };

  const validateForm = (): boolean => {
    let isValid = true;
    const errors: any = {};

    if (!formData.account_number) {
      errors.account_number = "Account Number is required.";
      isValid = false;
    } else if (!/^\d+$/.test(formData.account_number)) {
      errors.account_number = "Account Number must contain only numbers.";
      isValid = false;
    }

    if (!formData.confirm_account_number) {
      errors.confirm_account_number = "Confirm Account Number is required.";
      isValid = false;
    } else if (formData.account_number !== formData.confirm_account_number) {
      errors.confirm_account_number = "Account Numbers do not match.";
      isValid = false;
    }

    if (!formData.sort_code) {
      errors.sort_code = "Sort Code is required.";
      isValid = false;
    } else if (!/^\d{3,6}$/.test(formData.sort_code)) {
      errors.sort_code = "Sort Code must be 6 digits.";
      isValid = false;
    }

    setFormErrors(errors);
    return isValid;
  };

  const handleSave = async () => {
    try {
      if (!validateForm()) {
        return;
      }
      // Remove confirm_account_number before sending to API
      const { confirm_account_number, ...dataToSend } = formData;
       updateUserFinancilaDetails(dataToSend);
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
      <div className="user-main-form-div user-billing-section financial-section">
        <div className="account-section-right-side user-details-form-section mb-4">
          {isLoadingSave && (
            <div className="basket-loader-container">
              <svg viewBox="25 25 50 50" className="loader-svg">
                <circle r={20} cy={50} cx={50} className="loader" />
              </svg>
            </div>
          )}
          <div className="form-row row">
            <div className="form-group col-md-12">
              <label htmlFor="account_number">ACCOUNT NUMBER</label>
              <input
                type="text"
                name="account_number"
                value={formData.account_number}
                onChange={handleChange}
                className="form-control"
                id="account_number"
                placeholder="Account Number*"
              />
              <span className="text-danger">{formErrors.account_number}</span>
            </div>
          </div>
          <div className="form-row row">
            <div className="form-group col-md-12">
              <label htmlFor="confirm_account_number">CONFIRM ACCOUNT NUMBER</label>
              <input
                type="text"
                name="confirm_account_number"
                value={formData.confirm_account_number}
                onChange={handleChange}
                className="form-control"
                id="confirm_account_number"
                placeholder="Confirm Account Number*"
              />
              <span className="text-danger">{formErrors.confirm_account_number}</span>
            </div>
          </div>
          <div className="form-row row">
            <div className="form-group col-md-12">
              <label htmlFor="sort_code">Sort Code</label>
              <input
                type="text"
                name="sort_code"
                value={formData.sort_code}
                onChange={handleChange}
                className="form-control"
                id="sort_code"
                placeholder="Sort Code*"
                maxLength={6}
              />
              <span className="text-danger">{formErrors.sort_code}</span>
            </div>
          </div>
          <div className="user-biiling-update-div">
            <button onClick={handleSave} disabled={isLoading || isLoadingSave} className="btn btn-warning submit-button submit-button-finance">
              {isLoadingSave ? 'SAVING...' : 'SAVE CHANGES'}
            </button>
          </div>
        </div>
      </div>
    )
  );
};

export default UserFinancialDetails;