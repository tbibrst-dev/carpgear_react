const ResetPassword = () => {
  return (
    <div>
      <div className="carp-login">
        <div className="container">
          <div className="carp-login-area">
            <div className="carp-login-head">
              <h4>Create new Password</h4>
            </div>
            <div className="carp-login-inputs">
              <form>
                <div className="carp-login-inputs-field">
                  <input type="text" placeholder="Enter new password" />
                </div>
                <div className="carp-login-inputs-field">
                  <input type="text" placeholder="Confirm new password" />
                </div>
              </form>
              <div className="carp-log mt-2">
                <button type="button" className="carp-login-btn">
                  Create
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="single-comp-mob-show">
        <div className="container">
          <div className="mob-get-exclusive">
            <div className="mob-get-exclusive-all">
              <div className="mob-get-exclusive-txt get-sign">
                <h2>Get exclusive offers</h2>
                <p>
                  Get Exclusive Competitions &amp; Offers Available Only For Our
                  App Users.
                </p>
                <div className="mob-get-exc-icon">
                  <a href="#">
                    {" "}
                    <img src="/images/get-exc-2.png" alt="" />
                  </a>
                  <a href="#">
                    {" "}
                    <img src="/images/get-exc-1.png" alt="" />{" "}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ResetPassword;
