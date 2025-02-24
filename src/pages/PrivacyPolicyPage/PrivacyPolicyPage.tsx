import { useEffect, useState } from "react";
// import { PrivacyPolicy } from "../../components/PrivacyPolicy/PrivacyPolicy";
import axios from "axios";
import Loader from "../../common/Loader";
import { useLocation, useNavigate } from "react-router";
import { LEGAL_TERMS_ACTIVE_INDEX } from "../../utils";

const PrivacyPolicyPage = () => {
  const [content, setContent] = useState<string>("");
  const [title, setTitle] = useState<string>("");
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [activeIndex, setActiveIndex] = useState<number>(1);
  const [termsContent, setTermsContent] = useState<string>("");
  const [termsTitle, setTermsTitle] = useState<string>("");

  const [webTermsContent, setWebTermsContent] = useState<string>("");
  const [webTermsTitle, setWebTermsTitle] = useState<string>("Website Terms of Use");

  const navigate = useNavigate();
  const location = useLocation();

  const privacyPolicyUrl = `?rest_route=/wp/v2/pages/${import.meta.env.VITE_PRIVACY_POLICY_PAGE}`;
  const termsUrl = `?rest_route=/wp/v2/pages/${import.meta.env.VITE_TERMS_PAGE}`;
  const webTermsUrl = `?rest_route=/wp/v2/pages/${import.meta.env.VITE_WEBSITE_TERMS_PAGE}`;

  if (!privacyPolicyUrl || !termsUrl || !webTermsUrl) {
    navigate("not-found");
  }


  useEffect(() => {
    const params = new URLSearchParams(location.search);
    const index = params.get('tab');
    if (index) {
      setActiveIndex(parseInt(index));
    } else {
      const savedIndex = localStorage.getItem(LEGAL_TERMS_ACTIVE_INDEX);
      if (savedIndex) {
        setActiveIndex(parseInt(savedIndex));
      }
    }
  }, [location.search]);

  useEffect(() => {
    return () => {
      localStorage.setItem(LEGAL_TERMS_ACTIVE_INDEX, activeIndex.toString());
    };
  }, [activeIndex]);

  // Fetch privacy policy content
  useEffect(() => {
    const fetchContent = async () => {
      try {
        const res = await axios.get(privacyPolicyUrl);
        console.log(res);
        if (res.status == 200 || res.status == 201) {
          const htmlWithBreaks = res.data.content.rendered.replace(
            /(?<!<li>)\n/g,
            "<br>"
          );
          setContent(htmlWithBreaks);
          setTitle(res.data.title.rendered);
        }
      } catch (error) {
        console.log(error);
      } finally {
        setIsLoading(false);
      }
    };
    fetchContent();
  }, []);

  useEffect(() => {
    const fetchTermsContent = async () => {
      try {
        const res = await axios.get(termsUrl);

        if (res.status == 200) {
          const htmlWithBreaks = res.data.content.rendered.replace(
            /\n/g,
            "<br>"
          );

          setTermsContent(htmlWithBreaks);
          setTermsTitle(res.data.title.rendered);
        }
      } catch (error) {
        console.log(error);
      } finally {
        setIsLoading(false);
      }
    };
    fetchTermsContent();
  }, []);


  useEffect(() => {
    const fetchWebTermsContent = async () => {
      try {
        const res = await axios.get(webTermsUrl);

        if (res.status == 200) {
          const htmlWithBreaks = res.data.content.rendered.replace(
            /\n/g,
            "<br>"
          );

          setWebTermsContent(htmlWithBreaks);
          setWebTermsTitle(res.data.title.rendered);
        }
      } catch (error) {
        console.log(error);
      } finally {
        setIsLoading(false);
      }
    };
    fetchWebTermsContent();
  }, []);

  if (isLoading) {
    return <Loader />;
  }


  console.log('activeIndex',activeIndex)
  return (
    <div>
      <div className="comp-banner">
        <div className="comp-banner-txt">
          <h2
            dangerouslySetInnerHTML={{
              __html: activeIndex == 3 ? title : activeIndex == 1 ? termsTitle : webTermsTitle,
            }}
          />
        </div>
      </div>
      <div className="privacy-policy-tab-container">
        <div className="privacy-policy-tabs">
          <div
            className={`tab ${activeIndex == 1 && "active"}`}
            onClick={() => {
              navigate('?tab=1');
              setActiveIndex(1);
            }}
          >
            Competition Terms & Conditions
          </div>

          <div
            className={`tab ${activeIndex == 2 && "active"}`}
            onClick={() => {
              navigate('?tab=2');
              setActiveIndex(2);
            }}
          >
            Website Terms of Use
          </div>


          <div
           className={`tab ${activeIndex == 3 && "active"}`}
           onClick={() => {
             navigate('?tab=3');
             setActiveIndex(3);
           }}
          >
            Privacy Policy & Cookie Policy
          </div>
        </div>
      </div>
      <div className="privacy-content-container ">
        <div
          className="privacy-content "
          dangerouslySetInnerHTML={{
            __html: activeIndex == 3 ? content : activeIndex == 1 ? termsContent : webTermsContent,
          }}
        ></div>
      </div>
      <div id="privacy"></div>
      {/* <PrivacyPolicy content={content} title={title} /> */}
    </div>
  );
};

export default PrivacyPolicyPage;
