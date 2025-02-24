import axios from "axios";
import { useEffect, useRef, useState } from "react";
import { TOKEN } from "../../utils";
import { useDispatch, useSelector } from "react-redux";
import { RootState } from "../../redux/store";
import toast from "react-hot-toast";
import {
  setUserLoadingState,
  setUserState,
} from "../../redux/slices/userSlice";

const durationOptions = [
  "Select Limit Period",
  "Per Day",
  "Per Week",
  "Per Month",
  "Per Year",
];

const lockoutOptions = [
  "Select Limit Period",
  "1 Week",
  "2 Weeks",
  "4 Weeks",
  "12 Weeks",
  "1 Year",
  "Permanantly",
];

const ResponsibleGaming = () => {
  const [range, setRange] = useState<number>(1500);
  const sliderThumbRef = useRef<HTMLDivElement>(null);
  const sliderInputRef = useRef<HTMLInputElement>(null);
  const sliderLineRef = useRef<HTMLDivElement>(null);
  const [duration, setDuration] = useState(durationOptions[0]);
  const [lockDuration, setLockDuration] = useState(lockoutOptions[0]);
  const { user } = useSelector((state: RootState) => state.userReducer);
  const [maxRange, setMaxRange] = useState(0);
  const token = user && user.token;
  const dispatch = useDispatch();
  const defaultRange = "1500";

  const [isOpen, setIsOpen] = useState(false);
  const [isOpenSecond, setIsOpenSecond] = useState(false);


  useEffect(() => {
    if (user) {
      setRange(parseInt(user.limit_value));
      setDuration(user.limit_duration || durationOptions[0]);
      setLockDuration(user.locking_period || lockoutOptions[0]);
      setMaxRange(parseInt(user.limit_value || "1500"));
    }
  }, [user]);

  useEffect(() => {
    if (
      sliderInputRef.current &&
      sliderThumbRef.current &&
      sliderLineRef.current
    ) {
      const value = sliderInputRef.current.value;
      const max = sliderInputRef.current.max;
      const space =
        sliderInputRef.current.offsetWidth -
        sliderThumbRef.current?.offsetWidth;
      const bulletPos = Number(value) / Number(max);
      const leftPos = bulletPos * space;
      sliderThumbRef.current.style.left = leftPos + "px";
      sliderLineRef.current.style.width = `${
        (Number(value) / Number(max)) * 100
      }%`;
    }
  }, [range, maxRange]);

  const handleRangeChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setRange(Number(e.target.value));
  };

  const handleSetAccountLimit = async () => {
    try {
      dispatch(setUserLoadingState(true));
      const response = await axios.post(
        "?rest_route=/api/v1/update_user_details",
        {
          limit_value: range,
          limit_duration: duration,
          lockout_period: "",
          token,
        },
        {
          headers: {
            Authorization: TOKEN,
          },
        }
      );
      if (response.data.success) {
        toast.success("Successfully updated account limits!");
        dispatch(setUserState(response.data.data));
      }
    } catch (error) {
      console.log(error);
    } finally {
      dispatch(setUserLoadingState(false));
    }
  };
  // console.log(user);

  const handleAccountLock = async () => {
    setIsOpenSecond(false);

    try {
      if(lockDuration === "Select Limit Period"){
        toast.error("Please select lock period!");
        return;
  
      }
      dispatch(setUserLoadingState(true));
      const response = await axios.post(
        "?rest_route=/api/v1/update_user_details",
        {
          locking_period: lockDuration,
          token,
        },
        {
          headers: {
            Authorization: TOKEN,
          },
        }
      );
      if (response.data.success) {
        if (lockDuration === "Permanantly") {
          toast.success("Your account has been locked permanantly");
        } else {
          toast.success(
            `Your account has been locked for ${response.data.data.locking_period}`
          );
        }
      }

     
    } catch (error) {
      console.log(error);
    } finally {
      dispatch(setUserLoadingState(false));
    }
  };

  const handleSetDuration = (dura: string) => {
    setIsOpen(false);

    if (dura === "Select Limit Period" || user?.limit_duration === dura) return;

    if (!user?.limit_duration) {
      setDuration(dura);
      return;
    }

    const errorMessage = "You cannot increase your spending limit duration";

    if (
      user.limit_duration === "Per Day" ||
      (user.limit_duration === "Per Week" &&
        (dura === "Per Month" || dura === "Per Year")) ||
      (user.limit_duration === "Per Month" && dura === "Per Year")
    ) {
      toast.error(errorMessage);
      return;
    }

    setDuration(dura);
  };

  const handleLockDuration = (lockDuration: string) => {
    setIsOpenSecond(false);

    if(lockDuration === "Select Limit Period"){
      toast.error("Please select lock period!");
      return;

    }
    if (
      lockDuration === "Select Limit Period" ||
      user?.locking_period === lockDuration
    ) {
      setLockDuration(lockDuration);

      return;
    }

    // if (user?.locking_period === "Permanantly") {
    //   toast.error("Your account is permanantly locked");
    //   return;
    // }
    if (!user?.locking_period) {
      setLockDuration(lockDuration);
      return;
    }

    // const errorMessage = "You cannot increase your lockout period duration";

    // if (
    //   user.locking_period === "1 Week" ||
    //   (user.locking_period === "2 Weeks" && lockDuration !== "1 Week") ||
    //   (user.locking_period === "4 Weeks" &&
    //     ["12 Weeks", "1 Year", "Permanantly"].includes(lockDuration)) ||
    //   (user.locking_period === "12 Weeks" &&
    //     ["1 Year", "Permanantly"].includes(lockDuration)) ||
    //   (user.locking_period === "1 Year" && lockDuration === "Permanantly")
    // ) {
    //   toast.error(errorMessage);
    //   return;
    // }

    setLockDuration(lockDuration);
  };


  const handleToggle = () => {
    setIsOpen(!isOpen);
  };
  const handleToggleSecond = () => {
    setIsOpenSecond(!isOpenSecond);
  };

  return (
    <div>
      <div className="responsible-gaming-right-side">
        <div className="responsible-gaming-right-side-one">
          <div className="responsible-gaming-right-side-one-head">
            <div className="responsible-gaming-right-side-one-head-left">
              <h4>Current Spending Limit</h4>
            </div>
            <div className="responsible-gaming-right-side-one-head-right">
              <p>
                {" "}
                <span> £{user?.limit_value || 0}</span> {user?.limit_duration}
              </p>
            </div>
          </div>
          <div className="responsible-gaming-right-side-one-set-new">
            <h4>Set a New Limit</h4>
            <p>Set a spending limit for your account.</p>
          </div>
          <div className="respons-range">
            <div className="respons-range-left">
              <div className="range-slider">
                <div
                  id="slider_thumb"
                  className="range-slider_thumb"
                  ref={sliderThumbRef}
                />
                <div className="range-slider_line">
                  <div
                    id="slider_line"
                    className="range-slider_line-fill"
                    ref={sliderLineRef}
                  />
                </div>

                <input
                  id="slider_input"
                  className="range-slider_input"
                  type="range"
                  value={range}
                  step={1}
                  onChange={handleRangeChange}
                  min={1}
                  // max={user?.limit_value}
                  max={maxRange}
                  ref={sliderInputRef}
                />
              </div>
            </div>
            <div className="respons-range-right">
              <p>£{range ? range: defaultRange}</p>
            </div>
          </div>
          <div className="pay-per-drop">
            <div className="dropdown">
              <button
                className="btn btn-secondary dropdown-toggle"
                type="button"
                id="dropdownMenuButton1"
                data-bs-toggle="dropdown"
                aria-expanded={isOpen}
                onClick={handleToggle}
              >
                {duration}
                <span className="drp-dn">
                  <svg
                    width={15}
                    height={9}
                    viewBox="0 0 15 9"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M14 1.25L7.5 7.75L1 1.25"
                      stroke="white"
                      strokeWidth={2}
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    />
                  </svg>
                </span>
              </button>
              <ul
                className={`dropdown-menu ${isOpen ? 'show' : ''}`}
                aria-labelledby="dropdownMenuButton1"
              >
                {durationOptions.map((dura) => (
                  <li key={dura}>
                    <a
                      className="dropdown-item"
                      href="#"
                      onClick={() => handleSetDuration(dura)}
                    >
                      {dura}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          </div>
          <div className="winner-set-limit">
            {/* <button type="button" class="section-set-limit">Set Limit</button> */}
            <button
              type="button"
              className="section-set-limit"
              data-bs-toggle="modal"
              data-bs-target="#exampleModal"
            >
              Set Limit
            </button>
            <div
              className="modal fade limit_modal"
              id="exampleModal"
              tabIndex={-1}
              aria-labelledby="exampleModalLabel"
              aria-hidden="true"
            >
              <div className="modal-dialog modal-dialog-centered">
                <div className="modal-content">
                  <div className="modal-body">
                    <div className="winner-set-limit-content-all">
                      <div className="winner-set-limit-txt">
                        <h2>Set Limit?</h2>
                        <p>
                          Are you sure you would you like to set this spending
                          limit?
                        </p>
                      </div>
                      <div className="winner-limit-per-day">
                        <p>
                          {" "}
                          <span> £{range}</span> / {duration}
                        </p>
                      </div>
                    </div>
                  </div>
                  <div className="modal-footer">
                    <button
                      type="button"
                      className="limit-cancel"
                      data-bs-dismiss="modal"
                    >
                      Cancel
                    </button>
                    <button
                      type="button"
                      className="limit-set"
                      data-bs-dismiss="modal"
                      onClick={handleSetAccountLimit}
                    >
                      Set Limit
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="responsible-gaming-right-side-two">
          <div className="account-lock-main">
            <div className="account-lock-head">
              <h4>Account Lock</h4>
              <p>
                Choose the period of time in which you would like to lock your
                account.
              </p>
            </div>
            <div className="account-lock-drop">
              <div className="dropdown">
                <button
                  className="btn btn-secondary dropdown-toggle"
                  type="button"
                  id="dropdownMenuButton1"
                  data-bs-toggle="dropdown"
                  aria-expanded={isOpenSecond}
                      onClick={handleToggleSecond}
                >
                  {lockDuration}
                  <span className="drp-dn">
                    <svg
                      width={15}
                      height={9}
                      viewBox="0 0 15 9"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M14 1.25L7.5 7.75L1 1.25"
                        stroke="white"
                        strokeWidth={2}
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      />
                    </svg>
                  </span>
                </button>
                <ul
                  className={`dropdown-menu ${isOpenSecond ? 'show' : ''}`}
                  aria-labelledby="dropdownMenuButton1"
                >
                  {lockoutOptions.map((item) => (
                    <li key={item} onClick={() => handleLockDuration(item)}>
                      <a className="dropdown-item">{item}</a>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
            <div className="lock-account">
              {/* <button type="button" class="section-lock-account">Lock Account</button> */}
              <button
                type="button"
                className="section-lock-account"
                data-bs-toggle="modal"
                data-bs-target="#exampleModal-1"
              >
                Lock Account
              </button>
              <div
                className="modal fade"
                id="exampleModal-1"
                tabIndex={-1}
                aria-labelledby="exampleModalLabel-1"
                aria-hidden="true"
              >
                <div className="modal-dialog modal-dialog-centered">
                  <div className="modal-content">
                    <div className="modal-body">
                      <div className="winner-set-limit-content-all">
                        <div className="winner-set-limit-txt">
                          <h2>Lock Account?</h2>
                          <p>Your account will be locked for:</p>
                        </div>
                        <div className="winner-limit-per-day">
                          <p>{lockDuration}</p>
                        </div>
                      </div>
                    </div>
                    <div className="modal-footer">
                      <button
                        type="button"
                        className="limit-cancel"
                        data-bs-dismiss="modal"
                      >
                        Cancel
                      </button>
                      <button
                        type="button"
                        className="lock-set"
                        onClick={handleAccountLock}
                        data-bs-dismiss="modal"
                      >
                        Lock Account
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ResponsibleGaming;
