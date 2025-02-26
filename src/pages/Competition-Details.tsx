import { Fragment, Suspense, lazy, useEffect, useState } from "react";
import Rating from "../components/Details/Rating";
import Loader from "../common/Loader";
import InstantWinsSingle from "../components/Details/Instant-wins-single";
import { useParams } from "react-router";
import { CompetitionType, QunatityType } from "../types";
import {
  SLIDER_SPEED,
  cartError,
  competitionObj,
  navigateCompetition,
} from "../utils";
import { useGetSingleCompetitionMutation } from "../redux/queries";
import FAQ from "../components/Details/FAQ";
import OtherComps from "../components/Details/Other-competitions";
import { Helmet } from "react-helmet";
import axios from "axios";
import CarouselModal from "../common/CarouselModal";
import { useDispatch } from "react-redux";
import { setCurrentCompetition } from "../redux/slices";
import toast from "react-hot-toast";
// import { RootState } from "../redux/store";
const Details = lazy(() => import("../components/Details/Details"));

const CompetitionDetailsPage = () => {
  // const { competitionDetail } = useParams();
  const [competition, setCompetition] =
    useState<CompetitionType>(competitionObj);
  const [galleryImages, setGalleryImages] = useState<string[] | null>([]);
  const [galleryVideos, setGalleryVideos] = useState<string[] | null>([]);
  const [sliderSorting, setSliderSorting] = useState<string[] | null>([]);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [mutate] = useGetSingleCompetitionMutation();
  const [pageTitle, setPageTitle] = useState<string>("");
  const [metaDesc, setMetaDesc] = useState<string>("");
  const [metaTitle, setMetaTitle] = useState<string>("");
  const [otherComps, setOtherComps] = useState<CompetitionType[]>([]);
  const [quantities, setQuantities] = useState<QunatityType>([]);
  const [sliderSpeed, setSliderSpeed] = useState<number>(0);
  const [howItWorks, setHowItWorks] = useState<string>("");
  const [activeIndex, setActiveIndex] = useState<number>(0);
  const [postalEntry, setPostalEntry] = useState<string>("");
  const [rewardWinsInfo, setRewardWinsInfo] = useState<string>("");

  const [compFaq, setCompFaq] = useState<string>("");
  const [compRules, setCompRules] = useState<string>("");


  const dispatch = useDispatch();

  const [cachedData, setCachedData] = useState(() => {
    const cached = localStorage.getItem("cachedData");
    return cached ? JSON.parse(cached) : {};
  });

  const { competitionDetail } = useParams();
  const splitTitle = competitionDetail?.split("-");
  const id = splitTitle && splitTitle[splitTitle?.length - 1];
  // const { detailsComp } = useSelector((state: RootState) => state.competition);

  // const compPurchaase = useSelector((state: RootState) => state.user.purchasedTickets);
  const [activeTab, setActiveTab] = useState<number>(1);
  const handleActiveTab = (tabNumber: number) => {
    setActiveTab(tabNumber);
  };

  useEffect(() => {
    const fetchCompetition = async () => {
      setIsLoading(true);
      // const cached = cachedData[id as string];
      // if (cached && Date.now() - cached.timestamp < 60000) {
      //   setCompetition(cached.competition);
      //   setGalleryImages(cached.galleryImages);
      //   setGalleryVideos(cached.galleryVideos);
      //   setSliderSorting(cached.sliderSorting);
      //   setIsLoading(false);
      //   return;
      // }
      try {
        const res: any = await mutate({
          id: parseInt(id as string),
          token: import.meta.env.VITE_TOKEN,
        });

        // Function to get all values from the object
        // function getAllValues(obj: any): string[] {
        //   const values: string[] = [];
        //   for (const key in obj) {
        //     if (obj.hasOwnProperty(key)) {
        //       const value = obj[key];
        //       for (const prop in value) {
        //         if (value.hasOwnProperty(prop)) {
        //           values.push(value[prop]);
        //         }
        //       }
        //     }
        //   }
        //   return values;
        // }

        // interface VideoItem {
        //   youtube: string;
        //   thumb: string;
        // }

        // Define the structure of the transformed array
        interface GalleryVideo {
          video: string;
          thumb: string;
        }

        // Function to map over array and extract necessary values
        // function getAllValues(arr: VideoItem[]): GalleryVideo[] {
        //   return arr.map(item => ({
        //     video: item.youtube,
        //     thumb: item.thumb,
        //   }));
        // }


        if (!res.error) {
          const imagesString = res.data.data.gallery_images;
          const imagesArr: string[] = imagesString ? imagesString.split(",") : null;
          // imagesArr && imagesArr.length && imagesArr.unshift(res.data.data.image);


          const videosString = res.data.data.gallery_videos;
          // let videosArr: { video: string, thumb: string }[] = videosString ? JSON.parse(videosString) : [];
          // videosArr = getAllValues(videosArr);
          let videosArr: GalleryVideo[] = [];
          const parsedVideos = JSON.parse(videosString);
          if (parsedVideos && typeof parsedVideos == 'object') {
            const videoArray = Object.values(parsedVideos) as { vimeo?: string; youtube?: string; thumb: string }[];

            videosArr = videoArray.map(item => ({
              video: item.vimeo || item.youtube || "",  // Use Vimeo if available, otherwise YouTube
              thumb: item.thumb
            })).filter(item => item.video); // Remove empty entries
          }

          const slideSortingString = res.data.data.slider_sorting;
          let sortingArr: string[] = slideSortingString ? JSON.parse(slideSortingString) : null;


          const newCachedData = {
            ...cachedData,
            [id as string]: {
              competition: res.data.data,
              galleryImages: imagesArr,
              galleryVideos: videosArr,
              sliderSorting: sortingArr,
              timestamp: Date.now(),
            },
          };
          setCachedData(newCachedData);
          localStorage.setItem("cachedData", JSON.stringify(newCachedData));
          setGalleryImages(imagesArr);
          // setGalleryVideos(videosArr);
          setGalleryVideos(videosArr as unknown as string[]);
          setCompetition(res.data.data);
          setSliderSorting(sortingArr);
        }
      } catch (error) {
        console.log("error", error);
      } finally {
        setIsLoading(false);
      }
    };

    const sliderSpeed = parseInt(localStorage.getItem(SLIDER_SPEED) as string);
    if (sliderSpeed) {
      setSliderSpeed(sliderSpeed);
    }
    fetchCompetition();
  }, [id]);

  useEffect(() => {
    const fetchMetaData = async () => {
      try {
        const res = await axios.post(
          "?rest_route=/api/v1/getSEOSettings",
          { page: "competition" },
          { headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` } }
        );
        const meta = res.data.data[0];
        setPageTitle(meta.page_title);
        setMetaDesc(meta.meta_description);
        setMetaTitle(meta.meta_title);
      } catch (error) {
        console.log(error);
      }
    };
    fetchMetaData();
  }, [id]);

  useEffect(() => {
    const fetchGlobalSetting = async () => {
      try {
        const response = await axios.get("?rest_route=/api/v1/getsettings", {
          headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` },
        });

        if (response.data.success) {
          const sliderSpeed = localStorage.getItem(SLIDER_SPEED);

          if (
            !sliderSpeed ||
            parseInt(sliderSpeed) !== parseInt(response.data.data.slider_speed)
          ) {
            setSliderSpeed(Number(response.data.data.slider_speed));
            localStorage.setItem(SLIDER_SPEED, response.data.data.slider_speed);
          }
          setHowItWorks(`${response.data.data.instant_wins_info}`);
          setRewardWinsInfo(response.data.data.reward_prize_info);
          setPostalEntry(response.data.data.postal_entry_info);
          setCompRules(response.data.data.competition_rules);
          setCompFaq(response.data.data.competition_faq);
        }
      } catch (error) {
        console.log(error);
      }
    };
    fetchGlobalSetting();
  }, [id]);

  useEffect(() => {
    const fecthOtherComps = async () => {
      try {
        const response = await axios.post(
          "?rest_route=/api/v1/getOtherComps",
          {
            category: competition.category,
            limit: 5,
            order: "desc",
            order_by: "draw_date",
            id,
          },
          { headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` } }
        );
        if (response.data.success === "true") {
          setOtherComps(response.data.data);
          const initialQuantities: QunatityType = {};
          response.data.data.forEach((competition: CompetitionType) => {
            initialQuantities[competition.id] = parseInt(competition.quantity)
              ? parseInt(competition.quantity)
              : 1;
            setQuantities(initialQuantities);
          });
        }
      } catch (error) {
        console.log(error);
      }
    };
    fecthOtherComps();
  }, []);

  const handleQuantityChange = (
    id: number,
    newQuantity: number,
    action: "increment" | "decrement"
  ) => {
    const competition = otherComps.find(
      (comp) => comp.id === id
    ) as CompetitionType;
    const currentQty = quantities[id];
    const ticketsAvailable =
      parseInt(competition.total_sell_tickets) -
      parseInt(competition.total_ticket_sold);
    if (
      Number(competition?.max_ticket_per_user) === currentQty &&
      action === "increment"
    ) {
      toast.error(cartError());
      return;
    }
    if (currentQty === ticketsAvailable) return;
    setQuantities((prevQuantities) => ({
      ...prevQuantities,
      [id]: Math.max(
        0,
        action === "increment"
          ? prevQuantities[id] + newQuantity
          : prevQuantities[id] - newQuantity
      ),
    }));
  };

  const showHowItWorks = (clicked?: boolean) => {
    if (clicked) {
      setActiveIndex(1);
      return;
    }
    activeIndex ? setActiveIndex(0) : setActiveIndex(1);
  };

  const handleSetCompetition = (qty: number) => {
    const updatedQty = qty;
    const updatedCompetition = { ...competition };
    updatedCompetition.quantity = updatedQty.toString();
    setCompetition(() => updatedCompetition);
    dispatch(setCurrentCompetition(updatedCompetition));
  };

  const handleQuantityChangeInput = (id: number, value: number) => {
    let parsedValue: number;

    if (isNaN(value)) return;
    //* check if user input is not more than max ticket per user
    const competition = otherComps.find(
      (item) => item.id === id
    ) as CompetitionType;
    if (value > parseInt(competition.max_ticket_per_user)) {
      setQuantities((prevQuantities) => ({
        ...prevQuantities,
        [id]: parseInt(competition.max_ticket_per_user),
      }));
      return;
    }

    if (!value) {
      parsedValue = 0;
    } else {
      parsedValue = value;
    }
    setQuantities((prevQuantities) => ({
      ...prevQuantities,
      [id]: parsedValue,
    }));
  };

  if (isLoading) {
    return <Loader />;
  }


  const S3_BASE_URL = import.meta.env.VITE_STATIC_IMAGES_URL;

  return (
    <Fragment>
      <Helmet>
        <title>
          {pageTitle ? pageTitle : "Carp Gear Giveaway"} {pageTitle && "-"}{" "}
          {competition.title}
        </title>
        <meta name="description" content={metaDesc} />
        <meta name="keywords" content={metaTitle} />
      </Helmet>
      <div className="single-comp-mob-show">
        <div className="container">
          <div className="mob-get-exclusive">
            <div className="mob-get-exclusive-all">
              <div className="mob-get-exclusive-txt">
                <h2>Get exclusive offers</h2>
                <p>
                  Get Exclusive Competitions &amp; Offers Available Only For Our
                  App Users.
                </p>
                <div className="mob-get-exc-icon">
                  <a
                    href="https://apps.apple.com/us/app/carp-gear-giveaways/id1513020494"
                    target="_blank"
                  >
                    {" "}
                    <img src={`${S3_BASE_URL}/images/get-exc-2.png`} />
                  </a>
                  <a
                    href="https://play.google.com/store/apps/details?id=co.uk.carpgeargiveaways.app"
                    target="_blank
                  "
                  >
                    {" "}
                    <img src={`${S3_BASE_URL}/images/get-exc-1.png`} />{" "}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <Rating />
      {
        competition.status == 'Closed' || competition.category == 'finished_and_sold_out' ?
          <section className="win-twenty-bait-main">
            <div className="container">
              <div className="win-twenty-bait-main-all">
                <div className="row win-twenty-bait-align">
                  <div className="col-lg-12 col-md-12 col-12 text-center text-light">
                    <h2>
                      {competition.status === 'Closed'
                        ? 'This competition has closed, and tickets are no longer available for purchase.'
                        : 'This competition has finished or sold out, and tickets are no longer available for purchase.'}
                    </h2>
                  </div>
                </div>
              </div>
            </div>
          </section>



          :

          <>
            <Suspense fallback={<Loader />}>


              <Details
                competition={competition}
                galleryImages={galleryImages}
                sliderSorting={sliderSorting}
                galleryVideos={galleryVideos}
                sliderSpeed={sliderSpeed}
                postalEntryInfo={postalEntry}
                isEnableRewardWin={
                  Number(competition.enable_reward_wins) ? true : false
                }
                handleSetCompetition={handleSetCompetition}
                activeTab={activeTab}
                handleActiveTab={handleActiveTab}
              />
            </Suspense>{" "}
            <InstantWinsSingle
              instantWins={competition.instant_wins}
              rewardWins={competition.reward_wins}
              instantWinTicket={competition.instant_wins_tickets!}
              isEnableInsatntWins={
                Number(competition.enable_instant_wins) ? true : false
              }
              isEnableRewardWins={
                Number(competition.enable_reward_wins) ? true : false
              }
              showHowItWorks={showHowItWorks}
            />
            <FAQ
              faq={compFaq}
              rules={compRules}
              howItWorks={howItWorks}
              // showHowItWorks={showHowItWorks}
              reward_prize_info={rewardWinsInfo}
              // activeIndex={activeIndex}
              isEnableRewardWins={
                Number(competition.enable_reward_wins) ? true : false
              }
              draw_date={competition.draw_date}
              total_sell_tickets={competition.total_sell_tickets}
              handleActiveTab={handleActiveTab}
            />
            <OtherComps
              competitions={otherComps}
              quantities={quantities}
              handleQuantityChange={handleQuantityChange}
              navigateCompetition={navigateCompetition}
              category={competition.category!}
              handleQuantityChangeInput={handleQuantityChangeInput}
            />

            <CarouselModal />
          </>
      }
    </Fragment>
  );
};

export default CompetitionDetailsPage;
