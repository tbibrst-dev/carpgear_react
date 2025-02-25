import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import { useEffect, useState } from "react";
import axios from 'axios';
import Loader from "./Loader";
import { SLIDER_SPEED } from "../utils";
import { getMediaUrl } from "../utils/imageS3Url";





const Carousel = () => {
  const [competitions, setCompetitions] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const SLIDER_TRANSIITON_SPEED = import.meta.env.VITE_SLIDER_TRANSIITON_SPEED
  const S3_BASE_URL = import.meta.env.VITE_STATIC_IMAGES_URL;



  useEffect(() => {
    const fetchInstantWinCompetitons = async () => {
      try {

        const response = await axios.post(
          "?rest_route=/api/v1/get_slider_settings",
          {
            token: import.meta.env.VITE_TOKEN,
          },
          { headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` } }
        );
        if (response.data.success) {
          setCompetitions(response.data.data)

        }


      } catch (error) {
        console.log('error++++++++++', error);
      } finally {
        setIsLoading(false);
      }
    };
    fetchInstantWinCompetitons();
  }, []);

  const DESKTOP_HEIGHT = localStorage.getItem('DESKTOP_HEIGHT');
  const MOBILE_HEIGHT = localStorage.getItem('MOBILE_HEIGHT');
  let newHeight = 'auto';
  if (MOBILE_HEIGHT) {
    if (MOBILE_HEIGHT.endsWith('px')) {
      // If it's in pixels, convert to integer and add 30px
      newHeight = parseInt(MOBILE_HEIGHT, 10) + 40 + 'px';
    } else if (MOBILE_HEIGHT.endsWith('vh')) {
      // If it's in vh, you can add 30px as needed, but it depends on how you want to combine vh and px
      newHeight = parseInt(MOBILE_HEIGHT, 10) + 40 + 'vh';
    }
  } else {
    newHeight = 'auto'; // Default or fallback value if MOBILE_HEIGHT is not found
  }
  const savedSpeed = parseInt(localStorage.getItem(SLIDER_SPEED) as string);

  const truncateText = (text: string, maxLength: number) => {
    if (text.length > maxLength) {
      return text.substring(0, maxLength) + "...";
    }
    return text;
  };


  const onclickHandle = (link: any) => {
    window.location.href = link;
  }

  if (isLoading) {
    return <Loader />;
  }
  return (
    <>
      <div className="">
        <div className="banner-desktop">
          <div className="container-fluid ">
            <div className="swiper banner-Swiper">
              {competitions.length > 0 ? (
                <>
                  <Swiper
                    className="swiper-wrapper"
                    modules={[Pagination, Navigation, Autoplay]}
                    speed={SLIDER_TRANSIITON_SPEED}
                    autoplay={{
                      delay: savedSpeed || 3000,
                      disableOnInteraction: false,
                    }}
                    navigation={{
                      nextEl: ".swiper-button-next",
                      prevEl: ".swiper-button-prev",
                    }}
                  >

                    {
                      competitions.map((competition) => {
                        return (
                          <SwiperSlide
                            className="swiper-slide-desktop"
                            key={competition.id}
                          >
                            <div
                              className="carosel-all-desktop"
                              style={{
                                backgroundImage: `linear-gradient(180deg, rgba(15, 16, 16, 0) 28.71%, #0F1010 100%), url(${getMediaUrl(competition.desktop_image)})`,
                                height: `${DESKTOP_HEIGHT}`
                              }}
                              onClick={() => onclickHandle(competition.link)}
                            >

                              <div className="text-main-div">
                                <div className="main-heading">
                                  <span>{competition.slider_title}</span>
                                </div>
                                <div className="sub-heading">
                                  <span>{competition.sub_title}</span>
                                </div>
                                <div className="sub-button">
                                  <span onClick={() => onclickHandle(competition.link)}>{competition.btn_text}</span>
                                </div>
                              </div>
                            </div>
                          </SwiperSlide>
                        )
                      })
                    }

                  </Swiper>
                  <div className="swiper-button-next" />
                  <div className="swiper-button-prev" />
                </>
              ) : (
                <div style={{ margin: "150px 0" }}>
                  <h4
                    style={{
                      color: "white",
                      textAlign: "center",
                    }}
                  >
                    Featured competitions are not available right now!
                  </h4>
                </div>
              )}
            </div>
          </div>

        </div>

      </div>

      <div className="banner" style={{ height: `${newHeight}` }}>
        <div className="container-fluid">
          <div className="homepage-banner-mobile" style={{ height: `${newHeight}` , position:'relative' , zIndex:1}}>
            <div className="swiper banner-Swiper">
              {competitions.length > 0 ? (
                <>
                  <Swiper
                    className="swiper-wrapper"
                    modules={[Pagination, Navigation, Autoplay]}
                    speed={SLIDER_TRANSIITON_SPEED}
                    autoplay={{
                      delay: savedSpeed || 3000,
                      disableOnInteraction: false,
                    }}
                    navigation={{
                      nextEl: ".swiper-button-next",
                      prevEl: ".swiper-button-prev",
                    }}
                  >
                    {competitions.map((competition) => {
                      return (
                        <SwiperSlide
                          className="swiper-slide"
                          key={competition.id}
                        >
                          <div
                            className="carosel-all"
                            style={{
                              backgroundImage: `linear-gradient(180deg, rgba(0, 0, 0, 0) 27%, #000000 81.5%), url(${getMediaUrl(competition.mobile_image)})`,
                              height: `${MOBILE_HEIGHT}`,
                              zIndex:999

                            }}
                          >
                            <div className="banner-txt">
                              <h2 style={{ overflow: "hidden" }}>
                                {truncateText(competition.slider_title, 22)}
                              </h2>
                              <div className="sub-text">
                                <div className="text">
                                  <h5>{truncateText(competition.sub_title, 24)}</h5>
                                </div>
                              </div>
                              <div className="banner-btnss">
                                <button
                                  type="button"
                                  className="enter-btn" onClick={() => onclickHandle(competition.link)}>
                                  {competition.btn_text}
                                </button>
                              </div>
                            </div>

                            <div className="fish-bar">
                              <img src={`${S3_BASE_URL}/images/bidd.png`} alt="" />
                            </div>
                            <div className="fish-bar-two">
                              <img src={`${S3_BASE_URL}/images/mob-stick.svg`} alt="" />
                            </div>
                          </div>
                        </SwiperSlide>
                      );
                    })}
                  </Swiper>
                  <div className="swiper-button-next" />
                  <div className="swiper-button-prev" />
                </>
              ) : (
                <div style={{ margin: "150px 0" }}>
                  <h4
                    style={{
                      color: "white",
                      textAlign: "center",
                    }}
                  >
                    {/* {!fetchingComps &&
                  " Featured competitions are not available right now!"} */}
                  </h4>
                </div>
              )}
            </div>
          </div> </div> </div>
    </>
  );
};

export default Carousel;
