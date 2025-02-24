import { Fragment, useEffect, useState } from "react";
import InstantWinsComps from "../components/Instant_win/Instant-win";
import axios from "axios";
import { MetaData } from "../types";
import { Helmet } from "react-helmet";
import CarouselModal from "../common/CarouselModal";

const InstantWinPage = () => {
  const [pageTitle, setPageTitle] = useState<string>("");
  const [metaDesc, setMetaDesc] = useState<string>("");
  const [metaTitle, setMetaTitle] = useState<string>("");
  useEffect(() => {
    const fetchMetaData = async () => {
      try {
        const res = await axios.post(
          "?rest_route=/api/v1/getSEOSettings",
          {
            page: "instant_win",
          },
          { headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` } }
        );

        const meta: MetaData = res.data.data[0];
        setPageTitle(meta.page_title);
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
        <title>{pageTitle}</title>
        <meta name="description" content={metaDesc} />
        <meta name="title" content={metaTitle} />
      </Helmet>
      <InstantWinsComps />
      <CarouselModal />
    </Fragment>
  );
};
export default InstantWinPage;
