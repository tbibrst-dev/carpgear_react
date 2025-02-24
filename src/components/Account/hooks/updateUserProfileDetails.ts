import axios, { AxiosResponse } from "axios";
import { useState } from "react";
import { TOKEN } from "../../../utils";
import { User } from "../../../types";


interface UpdateUserDetailsResponse {
  success: boolean;
  error?: string;
}

const useUpdateUserDetails = (token: string) => {
  const [isLoadingSave, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [isSuccess, setIsSuccess] = useState<boolean>(false);

  const updateUserDetails = async ({
    first_name,
    last_name,
    email,
    currentPassword,
    newPassword,
    confirmPassword,
  }: User) => {
    setIsLoading(true);
    setIsSuccess(false);
    setError(null)
    try {
      const res: AxiosResponse<UpdateUserDetailsResponse> = await axios.post(
        "?rest_route=/api/v1/updateProfile",
        {
          token,
          account_first_name: first_name,
          account_last_name: last_name,
          account_display_name: `${first_name} ${last_name}`,
          account_email: email,
          password_current: currentPassword,
          password_1: newPassword,
          password_2: confirmPassword,
        },
        {
          headers: { Authorization: TOKEN },
        }
      );
      if (!res.data.success) {
      
        throw new Error(res.data.error || "Failed to update user details.");
      }else{
        setIsSuccess(true); // Set isSuccess to true on success
      }
    } catch (error:any) {      
      setError(error.response.data.error|| "An error occurred.");
    } finally {
      setIsLoading(false);
    }
  };

  return { isLoadingSave, error,isSuccess, updateUserDetails };
};

export default useUpdateUserDetails;
