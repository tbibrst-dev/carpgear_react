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

  useEffect(() => {
    const fetchMetaTags = async () => {
      try {
    
        const response = await fetch(`https://cggprelive.co.uk/wp-json/rankmath/v1/getHead?url=https://cggprelive.co.uk/results/legal-terms`);
        
        if (!response.ok) {
          throw new Error(`Error: ${response.statusText}`);
        }

        
        console.log('rankmath',response);

       
      } catch (err) {
        console.log('rankmath',err)
      } finally {
        console.log('done');
      }
    };

    fetchMetaTags();
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
