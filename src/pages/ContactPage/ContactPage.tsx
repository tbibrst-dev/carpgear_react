import Contact from "../../components/Contact/Contact";
import { Helmet } from "react-helmet";
import { MetaData } from "../../types";
import axios from "axios";
import { useState,useEffect } from "react";


const ContactPage = () => {

  const [title, setTitle] = useState<string>("");
  const [metaDesc, setMetaDesc] = useState<string>("");
  const [metaTitle, setMetaTitle] = useState<string>("");





  useEffect(() => {  
    const fetchMetaData = async () => {
      try {
        const res = await axios.post(
          "?rest_route=/api/v1/getSEOSettings",
          { page: "contact_us" },
          { headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` } }
        );
        const meta: MetaData = res.data.data[0];
        setTitle(meta.page_title);
        setMetaDesc(meta.meta_description);
        setMetaTitle(meta.meta_title);
      } catch (error) {
        console.log(error);
      }
    };
    fetchMetaData();
  }, []);


  return (
    <div>
      <Helmet>
        <meta charSet="utf-8" />
        <title>{title}</title>
        <meta name="description" content={metaDesc} />
        <meta name="title" content={metaTitle} />
      </Helmet>
      <Contact  />
    </div>
  );
};

export default ContactPage;
