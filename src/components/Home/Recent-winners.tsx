import { useState, useEffect } from 'react';
import axios from 'axios';
import { Scrollbar } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import 'swiper/css';
import 'swiper/css/scrollbar';

const RecentWinners = () => {
  const [winners, setWinners] = useState<any>([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const fetchWinners = async () => {
      try {
        // const res = await axios.get('/wp-json/wp/v2/winners?_embed&type=winners&status=publish&filter[orderby]=date&order=desc&per_page=5');
        const res = await axios.get('/wp/v2/winners?_embed&type=winners&status=publish&filter[orderby]=date&order=desc&per_page=5');  //only for cgg live
        setWinners(res.data);
      } catch (error) {
        console.error('Error fetching winners:', error);
      } finally {
        setIsLoading(false);
      }
    };

    fetchWinners();
  }, []);

  if (isLoading) {
    return <div>Loading...</div>;
  }

  const decodeHtmlEntities = (str: string) => {
    const parser = new DOMParser();
    const decoded = parser.parseFromString(str, 'text/html').documentElement.textContent;
    return decoded;
  };

  return (
    <div>
      <div className="recent-winner">
        <div className="container">
          <div className="Instant-winss-heading">
            <div className="Instant-winss-center">
              <h2>Recent Winners</h2>
            </div>
          </div>

          <div className="instant-winner-space">
            <div className="instant-slider">
              <div className="swiper inst-slider">
                <Swiper
                  className="swiper-wrapper"
                  slidesPerView={3.3}
                  spaceBetween={20}
                  modules={[Scrollbar]}
                  scrollbar={{ hide: true, el: "#recent-scrollbar" }}
                  breakpoints={{
                    0: {
                      slidesPerView: 1,
                      spaceBetween: 10,
                    },
                    375: {
                      slidesPerView: 1.3,
                      spaceBetween: 10,
                    },
                    420: {
                      slidesPerView: 1.3,
                      spaceBetween: 10,
                    },
                    600: {
                      slidesPerView: 2,
                      spaceBetween: 30,
                    },
                    800: {
                      slidesPerView: 2,
                      spaceBetween: 0,
                    },
                    1200: {
                      slidesPerView: 3.3,
                      spaceBetween: 10,
                    },
                    1400: {
                      slidesPerView: 3.3,
                      spaceBetween: 10,
                    },
                  }}
                >
                  {winners && winners.map((winner: any) => {
                    const embeddedMedia = winner._embedded?.['wp:featuredmedia']?.[0];
                    const sourceUrl = embeddedMedia ? embeddedMedia.source_url : 'default-image-url.jpg';
                    const customerName = winner.metadata?.customer_name || 'Unknown';
                    const customerCounty = winner.metadata?.customer_county || 'Unknown';
                    const ticketNumber = winner.metadata?.ticket_number || 'N/A';
                    const title = winner?.title?.rendered ? decodeHtmlEntities(winner.title.rendered) : 'Untitled';

                    return (
                      <SwiperSlide key={winner.id} className="swiper-slide">
                        <div className="Instant-slider-content pe-none">
                          <div className="Instant-image">
                            <img src={sourceUrl} alt="image" />
                          </div>
                          <div className="winner-image-bt-c">
                            {/* <h3>{customerName} from {customerCounty}</h3> */}
                            <h3>{`${customerName} from ${customerCounty}`}</h3>
                            <h2>{title}</h2>
                            <h4>Ticket #{ticketNumber}</h4>
                          </div>
                        </div>
                      </SwiperSlide>
                    );
                  })}
                </Swiper>
                <div className="swiper-scrollbar" id="recent-scrollbar" />
              </div>
              <div className="instant-view-all-mob">
                <a href="/winners_list">View All</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default RecentWinners;
