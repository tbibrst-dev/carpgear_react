import { useNavigate } from "react-router";

type PropsType = {
  content: string;
  title: string;
};

const TermsAndConditions: React.FC<PropsType> = ({ content, title }) => {
  const decodedTitle = title.replace(/&#(\d+);/g, (_, match) =>
    String.fromCharCode(match)
  );
  const navigate = useNavigate();
  return (
    <>
      {" "}
      <div className="comp-banner">
        <div className="comp-banner-txt">
          <h2 dangerouslySetInnerHTML={{ __html: decodedTitle }} />
        </div>
      </div>
      <div className="privacy-policy-tab-container">
        <div className="privacy-policy-tabs">
          <div className="tab active">Competition Terms & Conditions</div>
          <div className="tab">Website Terms of Use</div>
          <div className="tab" onClick={() => navigate("/privacy/policy")}>
            Privacy Policy & Cookie Policy
          </div>
        </div>
      </div>
      <div className="privacy-content-container ">
        <div
          className="privacy-content "
          dangerouslySetInnerHTML={{ __html: content }}
        ></div>
      </div>
    </>
  );
};

export default TermsAndConditions;
