import WinnersList from "../../components/WinnersListPage/WinnersListPage";
import { Fragment, useEffect, useState } from "react";
import { Helmet } from "react-helmet";
import { MetaData } from "../../types";
import axios from "axios";


const WinnersListPage = () => {
  const [title, setTitle] = useState<string>("");
  const [metaDesc, setMetaDesc] = useState<string>("");
  const [metaTitle, setMetaTitle] = useState<string>("");
  useEffect(() => {
    const fetchMetaData = async () => {
      try {
        const res = await axios.post(
          "?rest_route=/api/v1/getSEOSettings",
          { page: "Home" },
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
    <Fragment>
      <Helmet>
        <meta charSet="utf-8" />
        <title>{title}</title>
        <meta name="description" content={metaDesc} />
        <meta name="title" content={metaTitle} />
      </Helmet>    
      <WinnersList /> 
    </Fragment>
  );
};

export default WinnersListPage;
