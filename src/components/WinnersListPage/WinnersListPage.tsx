import { useState, useEffect, useRef } from 'react';
import axios from 'axios';


const WinnersList = () => {
    const [winners, setWinners] = useState<any[]>([]);
    const [page, setPage] = useState<number>(1);
    const [isLoading, setIsLoading] = useState<boolean>(true);

    const [hasMore, setHasMore] = useState(true);
    const observer = useRef<IntersectionObserver>();

    const token = import.meta.env.VITE_TOKEN; // Store token securely in .env file

    useEffect(() => {
        const fetchWinners = async (page: number) => {
            setIsLoading(true); // Ensure loading state is set before fetching

            try {
                const res = await axios.get(`/wp/v2/winners`, {
                    params: {
                        _embed: true,
                        type: "winners",
                        status: "publish",
                        "filter[orderby]": "date",
                        order: "desc",
                        per_page: 8,
                        page: page,
                    },
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });

                if (res.data.length === 0) {
                    setHasMore(false);
                } else {
                    setWinners((prevWinners) => [...prevWinners, ...res.data]);
                }
            } catch (error) {
                console.error("Error fetching winners:", error);
            } finally {
                setIsLoading(false);
            }
        };

        fetchWinners(page);
    }, [page]);

    useEffect(() => {
        if (observer.current) observer.current.disconnect();

        const callback = (entries: IntersectionObserverEntry[]) => {
            if (entries[0].isIntersecting && hasMore) {
                setPage(prevPage => prevPage + 1);
            }
        };

        observer.current = new IntersectionObserver(callback);
        const target = document.querySelector('#scroll-target');
        if (target) observer.current.observe(target);

        return () => {
            if (observer.current) observer.current.disconnect();
        };
    }, [hasMore]);


    const decodeHtmlEntities = (str: string) => {
        const parser = new DOMParser();
        const decoded = parser.parseFromString(str, 'text/html').documentElement.textContent;
        return decoded;
    };

    return (
        <div className='main-div-winner-list-page'>


            <div>
                <div className="comp-banner zapct-page-title">
                    <div className="comp-banner-txt">
                        <span><h2>Winners</h2></span>
                    </div>
                </div>

                {isLoading ?
                    <div className="basket-loader-container">
                        <svg viewBox="25 25 50 50" className="loader-svg">
                            <circle r={20} cy={50} cx={50} className="loader" />
                        </svg>
                    </div>

                    :
                    <div className='container main-container zapct-winners'>
                        <div className='row'>
                            {winners && winners.map((winner: any, index: number) => {
                                const embeddedMedia = winner._embedded?.['wp:featuredmedia']?.[0];
                                const sourceUrl = embeddedMedia ? embeddedMedia.source_url : 'default-image-url.jpg';
                                const customerName = winner.metadata?.customer_name || 'Unknown';
                                const customerCounty = winner.metadata?.customer_county || 'Unknown';
                                const ticketNumber = winner.metadata?.ticket_number || 'N/A';
                                const titles = winner.title?.rendered || 'Untitled';
                                const title = winner?.title?.rendered ? decodeHtmlEntities(winner.title.rendered) : 'Untitled';

                                return (
                                    <div className="col-12 col-sm-3 mb-4 " key={`winner-${index}`}>
                                        <div className='Instant-slider-content pe-none'>
                                            <div className="Instant-image">
                                                <img src={sourceUrl} alt={titles} className='' />
                                            </div>
                                            <div className="winner-image-bt-c">
                                                {/* <h3>{customerName} from {customerCounty}</h3> */}
                                                <h3 className='card-title mb-3'>{`${customerName} from ${customerCounty}`}</h3>
                                                <h2 className='card-text'> {title}</h2>
                                                <h4 >Ticket #{ticketNumber}</h4>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                        {isLoading && page > 1 && <div>Loading more...</div>}
                        <div id="scroll-target" style={{ height: '20px', margin: '10px 0' }}></div>
                    </div>}

            </div>
        </div>

    );
};

export default WinnersList;
