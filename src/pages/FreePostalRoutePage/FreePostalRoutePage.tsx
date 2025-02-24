import { useEffect, useState } from "react";
// import TermsAndConditions from "../../components/TermsAndConditions/TermsAndConditions";
import axios from "axios";
import Loader from "../../common/Loader";

const FreePostalRoutePage = () => {
  const [content, setContent] = useState<string>("");
  const [title, setTitle] = useState<string>("");
  const [isLoading, setIsLoading] = useState<boolean>(true);
  useEffect(() => {
    const fetchContent = async () => {
      try {
        const res = await axios.get(
          `?rest_route=/wp/v2/pages/${import.meta.env.VITE_FREE_POSTAL_ROUTE_PAGE}`
        );

        if (res.status === 200) {
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

  if (isLoading) {
    return <Loader />;
  }

  const decodedTitle = title.replace(/&#(\d+);/g, (_, match) =>
    String.fromCharCode(match)
  );





  return (
    <div >
      <div className="comp-banner">
        <div className="comp-banner-txt">
          <span><h2 dangerouslySetInnerHTML={{ __html: decodedTitle }} /></span>
        </div>
      </div>
      <div className="privacy-content-container ">
        <div
          className="privacy-content "
          dangerouslySetInnerHTML={{
            __html: content,
          }}
        ></div>
      </div>
    </div>

  );
};

export default FreePostalRoutePage;
