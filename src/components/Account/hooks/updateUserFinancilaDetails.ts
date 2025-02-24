import axios, { AxiosResponse } from "axios";
import { useState } from "react";
import { TOKEN } from "../../../utils";
// import { User } from "../../../types";


interface UpdateUserDetailsResponse {
  success: boolean;
  error?: string;
}

interface User {
  account_number: string;
  sort_code: string;

  // Other properties...
}


const useupdateUserFinancilaDetails = (token: string) => {
  const [isLoadingSave, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [isSuccess, setIsSuccess] = useState<boolean>(false);

  const updateUserFinancilaDetails = async ({
    account_number,
    sort_code
    
  }: User) => {
    setIsLoading(true);
    setIsSuccess(false);
    try {
      const res: AxiosResponse<UpdateUserDetailsResponse> = await axios.get(
        "?rest_route=/api/v1/get_bank_details",
        {
          params: { // Send parameters as query parameters
            token,
            account_number,
            sort_code
          },
          headers: { // Set headers here
            Authorization: TOKEN,
          },
        }
      );
      if (!res.data.success) {

        setError("Failed to update user details.");
        setIsSuccess(false)
      

      }else{
        setIsSuccess(true); // Set isSuccess to true on success
      }
    } catch (error:any) {      
      setError(error.response.data.error|| "An error occurred.");
    } finally {
      setIsLoading(false);
    }
  };

  return { isLoadingSave, error, isSuccess, updateUserFinancilaDetails };
};

export default useupdateUserFinancilaDetails;
