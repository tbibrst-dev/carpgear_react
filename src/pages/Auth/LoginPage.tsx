import { useSelector } from "react-redux";
import Login from "../../components/Auth/Login";
import { RootState } from "../../redux/store";
import { useNavigate } from "react-router";
import { useEffect } from "react";

const LoginPage = () => {
  const user = useSelector((state: RootState) => state.userReducer.user);
  const navigate = useNavigate();
  console.log(user);
  //* navigate user to home page if user exists
  useEffect(() => {
    if (user) {
      navigate("/");
    }
  }, [user]);

  return (
    <div>
      <Login />
    </div>
  );
};

export default LoginPage;
