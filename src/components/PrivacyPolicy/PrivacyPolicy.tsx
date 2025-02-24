type PropsType = {
  content: string;
  title: string;
};

export const PrivacyPolicy: React.FC<PropsType> = ({ content }) => {
  return (
    <>
      {" "}
      {/* <div className="comp-banner">
        <div className="comp-banner-txt">
          <h2 dangerouslySetInnerHTML={{ __html: title }} />
        </div>
      </div> */}
      {/* <div className="privacy-policy-tab-container">
        <div className="privacy-policy-tabs">
          <div
            className="tab"
            onClick={() => navigate("/terms/and/conditions")}
          >
            Competition Terms & Conditions
          </div>
          <div className="tab">Website Terms of Use</div>
          <div className="tab active">Privacy Policy & Cookie Policy</div>
        </div>
      </div> */}
      <div className="privacy-content-container ">
        <div
          className="privacy-content "
          dangerouslySetInnerHTML={{ __html: content }}
        ></div>
      </div>
    </>
  );
};
