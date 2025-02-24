import axios from "axios";
import { useState } from "react";
import toast from "react-hot-toast";
import { User } from "../../../types";
import { TOKEN } from "../../../utils";
import { setUserState } from "../../../redux/slices/userSlice";
import { useDispatch } from "react-redux";

const useUpdateUser = () => {
  //   MUTATION UPDATE USER
  const [isUpdating, setIsUpdating] = useState<boolean>(false);
  const dispatch = useDispatch();

  const updateUser = async (user: User) => {
    const { first_name, last_name, token, email } = user;
    if (!first_name || !last_name) {
      toast.error("All feilds are required");
      return;
    }
    setIsUpdating(true);
    try {
      const response = await axios.post(
        "?rest_route=/api/v1/updateProfile",
        {
          account_first_name: first_name,
          account_last_name: last_name,
          account_email: email,
          account_display_name: `${first_name} ${last_name}`,
          token,
        },
        {
          headers: {
            Authorization: TOKEN,
          },
        }
      );
      console.log(response);
      if (response.data.success) {
        dispatch(setUserState(response.data.data));
        toast.success(" Profile updated successfully!");
      }
    } catch (error) {
      console.log(error);
    } finally {
      setIsUpdating(false);
    }
  };

  return { updateUser, isUpdating };
};

export default useUpdateUser;
