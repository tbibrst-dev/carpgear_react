import axios, { AxiosResponse } from "axios";
import { useState } from "react";
import { TOKEN } from "../../../utils";
import { User } from "../../../types";


interface UpdateUserDetailsResponse {
  success: boolean;
  error?: string;
}

const useUpdateUserAccountDetails = (token: string) => {
  const [isLoadingSave, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [isSuccess, setIsSuccess] = useState<boolean>(false);

  const updateUserDetails = async ({
    first_name,
    last_name,
    email,
    billing_phone,
    billing_address_1,
    billing_address_2,
    billing_city,
    billing_postcode,
    billing_state,
    
  }: User) => {
    setIsLoading(true);
    setIsSuccess(false);
    try {
      const res: AxiosResponse<UpdateUserDetailsResponse> = await axios.post(
        "?rest_route=/api/v1/update_user_details",
        {
          token,
          first_name: first_name,
          last_name: last_name,
          billing_first_name: first_name,
          billing_last_name:last_name ,
          billing_email: email,
          billing_phone: billing_phone,
          billing_address_1: billing_address_1,
          billing_address_2: billing_address_2,
          billing_city: billing_city,
          billing_postcode: billing_postcode,
          billing_state:billing_state,

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

export default useUpdateUserAccountDetails;
