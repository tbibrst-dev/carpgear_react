import { Fragment, useEffect, useState } from "react";
import Competitions from "../../components/Category/Competitions";
// import Loader from "../../common/Loader";
import axios from "axios";
import { MetaData } from "../../types";
import { Helmet } from "react-helmet";
import { useParams } from "react-router";
import CarouselModal from "../../common/CarouselModal";

const CompetitionsPage = () => {
  const [pageTitle, setPageTitle] = useState<string>("");
  const [metaDesc, setMetaDesc] = useState<string>("");
  const [metaTitle, setMetaTitle] = useState<string>("");
  const { category } = useParams();

  useEffect(() => {
    const fetchMetaData = async () => {
      try {
        const res = await axios.post(
          "?rest_route=/api/v1/getSEOSettings",
          {
            page:
              category === "instant_win_comps"
                ? "instant_win"
                : category === "drawn_next_competition"
                  ? "drawn_next"
                  : category === "the_big_gear"
                    ? "the_big_gear"
                    : category === "the_accessories_and_bait"
                      ? "the_accessories_and_bait"
                      : category === "finished_and_sold_out"
                        ? "finished_and_sold_out"
                        : "competitions",
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
  }, [category]);

  useEffect(() => {
    const fetchMetaTags = async () => {
      try {
        const page = category === "instant_win_comps"
          ? "instant_win"
          : category === "drawn_next_competition"
            ? "drawn_next"
            : category === "singular_competition"
              ? "singular_competition"
              : category === "the_accessories_and_bait"
                ? "the_accessories_and_bait"
                : category === "finished_and_sold_out"
                  ? "finished_and_sold_out"
                  : "all";
        const response = await fetch(`https://cggprelive.co.uk/wp-json/rankmath/v1/getHead?url=https://cggprelive.co.uk/competitions/${page}`, {
          headers: {
            'Content-Type': 'application/json',
            // 'Authorization': 'Bearer YOUR_API_TOKEN', // Uncomment if API needs auth token
          },
        });

        if (!response.ok) {
          throw new Error(`Error: ${response.statusText}`);
        }


        console.log('rankmath', response);


      } catch (err) {
        console.log('rankmath', err)
      } finally {
        console.log('done');
      }
    };

    fetchMetaTags();
  }, [category]);

  return (
    <Fragment>
      <Helmet>
        <title>{pageTitle}</title>
        <meta name="description" content={metaDesc} />
        <meta name="title" content={metaTitle} />
      </Helmet>
      {/* <Suspense fallback={<Loader />}> */}
      <Competitions />
      {/* </Suspense> */}
      <CarouselModal />
    </Fragment>
  );
};

export default CompetitionsPage;
