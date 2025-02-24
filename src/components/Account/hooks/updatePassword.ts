import axios from "axios";
import { useState } from "react";
import toast from "react-hot-toast";
import { TOKEN } from "../../../utils";

const useChangePassword = () => {
  const [isPasswordChanging, setIsPasswordChanging] = useState(false);

  const changePassword = async (
    currentPassword: string,
    newPassword: string,
    account_first_name: string,
    account_last_name: string,
    account_email: string,
    account_display_name: string,
    token: string
  ) => {
    setIsPasswordChanging(true);
    try {
      const response = await axios.post(
        "?rest_route=/api/v1/updateProfile",
        {
          password_1: newPassword,
          password_current: currentPassword,
          account_display_name,
          account_email,
          account_first_name,
          account_last_name,
          password_2: newPassword,
          token,
        },
        {
          headers: {
            Authorization: TOKEN,
          },
        }
      );
      if (response.data.success) {
        toast.success("Password changed successfully!");
      }
    } catch (error: any) {
      console.log(error);
      if (error && error.response) {
        toast.error(error.response.data.error);
      }
    } finally {
      setIsPasswordChanging(false);
    }
  };

  return { changePassword, isPasswordChanging };
};

export default useChangePassword;
