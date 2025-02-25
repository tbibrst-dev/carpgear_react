import { useEffect, useState } from "react";
import TermsAndConditions from "../../components/TermsAndConditions/TermsAndConditions";
import axios from "axios";
import Loader from "../../common/Loader";

const TermsAndConditionsPage = () => {
  const [content, setContent] = useState<string>("");
  const [title, setTitle] = useState<string>("");
  const [isLoading, setIsLoading] = useState<boolean>(true);
  useEffect(() => {
    const fetchContent = async () => {
      try {
        const res = await axios.get(
          `?rest_route=/wp/v2/pages/${import.meta.env.VITE_TERMS_PAGE}`
        );

        if (res.status === 200) {
          setContent(res.data.content.rendered);
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



  

  if (isLoading) {
    return <Loader />;
  }

  return (
    <div>
      <TermsAndConditions content={content} title={title} />
    </div>
  );
};

export default TermsAndConditionsPage;
