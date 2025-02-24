import { useSelector } from "react-redux";
import { RootState } from "../../redux/store";
import { useEffect, useState } from "react";
// import { User } from "../../types";
import { userObj } from "../../utils";
import useUpdateUser from "./hooks/updateUser";
import UserDetails from "./UserDetails";
import { User } from "../../types";
// import useChangePassword from "./hooks/updatePassword";
// import toast from "react-hot-toast";

const UserPage = () => {
  const { user, isAuthenticating } = useSelector(
    (state: RootState) => state.userReducer
  );
  const [userDetails, setUserDetails] = useState<User>(userObj);
  const { updateUser, isUpdating } = useUpdateUser();
  // const { changePassword } = useChangePassword();

  useEffect(() => {
    if (user) {
      setUserDetails(user);
    }
  }, [user]);

  const handleUpdateUser = (user: User) => {
    updateUser(user);
  };

  // const handleChangePassword = (
  //   currentPassword: string,
  //   password: string,
  //   confirmPassword: string
  // ) => {
  //   if (password !== confirmPassword) {
  //     toast.error("Password and confirm password do not match");
  //     return;
  //   }

  //   const { token, first_name, last_name, email } = userDetails;
  //   const displayName = first_name + last_name;

  //   changePassword(
  //     currentPassword,
  //     password,
  //     first_name,
  //     "brst",
  //     email,
  //     displayName,
  //     token
  //   );
  // };

  if (isAuthenticating) {
    return (
      <div className="basket-loader-container">
        <svg viewBox="25 25 50 50" className="loader-svg">
          <circle r={20} cy={50} cx={50} className="loader" />
        </svg>
      </div>
    );
  }

  return (
    <UserDetails
      isUpdating={isUpdating}
      handleUpdateUser={handleUpdateUser}
      user={userDetails}
      // isPasswordChanging={isPasswordChanging}
      // handlePasswordChange={handleChangePassword}
    />
  );
};

export default UserPage;
