import {  useEffect, useState } from "react";
import axios from "axios";
import { AUTH_TOKEN_KEY, decryptToken, TOKEN } from "../../utils";
import WinnerList from "./WinnerComponent/WinnerList";
import InstantWinnerList from "./WinnerComponent/InstantWinnerList";

interface WinnerData {
  id: number;
  name: string;
  // Add other fields as necessary
}

const Winners = () => {
  const [loading, setLoading] = useState<boolean>(true);
  const [data, setData] = useState<WinnerData[]>([]);

  const [loadingInstant, setLoadingInstant] = useState<boolean>(true);
  const [dataInstant, setDataInstant] = useState<WinnerData[]>([]);



  const [pagination, setPagination] = useState<number>(1);
  const [hasMore, setHasMore] = useState<boolean>(true);



  const [paginationInstant, setPaginationInstant] = useState<number>(1);
  const [hasMoreInstant, setHasMoreInstant] = useState<boolean>(true);


  const [loadingButton, setLoadingButton] = useState<boolean>(true);
  const [loadingButtonInstant, setLoadingButtonInstant] = useState<boolean>(true);




  useEffect(() => {
    setLoading(true);
    fetchCompetitionsWinner(pagination);
    fetchCompetitionsInstantWinner(paginationInstant);
  }, []);



  const fetchCompetitionsWinner = async (nextPage: number) => {
    try {
      const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
      const token = decryptToken(encodedToken);
      const res = await axios.post(
        "?rest_route=/api/v1/get_competition_winner",
        { token, page: nextPage },
        { headers: { Authorization: TOKEN } }
      );
      if (res.data.success) {
        setData(prevData => [...prevData, ...res.data.data]);
        setHasMore(res.data.data.length > 0);
        setPagination(res.data.page);
      } else {
        setHasMore(false);
      }
    } catch (error) {
      console.log(error);
      setLoading(false);
      setLoadingButton(true)

    } finally {
      setLoading(false);
      setLoadingButton(true)

    }
  };


  const fetchCompetitionsInstantWinner = async (nextPageInstant: any) => {
    try {
      const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
      const token = decryptToken(encodedToken);
      const res = await axios.post(
        "?rest_route=/api/v1/get_instant_winners",
        { token, page: nextPageInstant },
        { headers: { Authorization: TOKEN } }
      );
      if (res.data.success) {
        setDataInstant(prevData => [...prevData, ...res.data.data]);
        setHasMoreInstant(res.data.data.length > 0);
        setPaginationInstant(res.data.page);

      } else {
        setHasMoreInstant(false);
      }
    } catch (error) {
      console.log(error);
      setLoadingInstant(false);
      setLoadingButtonInstant(true);


    } finally {
      setLoadingInstant(false);
      setLoadingButtonInstant(true);

    }
  };




  const handleLoadMore = () => {
    setLoadingButton(false);
    const nextPage = pagination + 1;
    setPagination(nextPage);
    fetchCompetitionsWinner(nextPage);
  };

  const handleLoadMoreInstant = () => {
    setLoadingButtonInstant(false);
    const nextPageInstant = paginationInstant + 1;
    setPaginationInstant(nextPageInstant);
    fetchCompetitionsInstantWinner(nextPageInstant);
  };

  return (
    <>
      <div className="comp-banner">
        <div className="comp-banner-txt">
          <h2>Draw Results</h2>
        </div>
      </div>

      <div className="winner-page-main-div">
        <div className="winner-page-main-div-tabs">
          <ul className="nav nav-tabs nav-justified mb-3" id="ex1">
            <li className="nav-item" >
              <a
                className="nav-link active"
                id="ex3-tab-1"
                data-bs-toggle="tab"
                href="#ex3-tabs-1"
                role="tab"
                aria-controls="ex3-tabs-1"
                aria-selected="true"
              >
                Winners
              </a>
            </li>
            <li className="nav-item">
              <a
                className="nav-link"
                id="ex3-tab-2"
                data-bs-toggle="tab"
                href="#ex3-tabs-2"
                role="tab"
                aria-controls="ex3-tabs-2"
                aria-selected="false"
              >
                Instant Winners
              </a>
            </li>
          </ul>

          <div className="tab-content" id="ex2-content">
            <div
              className="tab-pane fade show active"
              id="ex3-tabs-1"
              role="tabpanel"
              aria-labelledby="ex3-tab-1"
            >
              {loading ? (
                <div className="basket-loader-container">
                  <svg viewBox="25 25 50 50" className="loader-svg">
                    <circle r={20} cy={50} cx={50} className="loader" />
                  </svg>
                </div>
              ) : (
                <>
                  {data.length > 0 ? (
                    <>
                      <div>
                        {data.map((item, idx) => (
                          <div key={idx} className="winner-tab-list">
                            <WinnerList ticket={item} />
                          </div>
                        ))}
                      </div>
                      <div className="winner-page-button-div">
                        {hasMore && (
                          <button onClick={handleLoadMore} disabled={!loadingButton} className="load-more-btn winner-button">
                            {!loadingButton ? "Loading..." : "Load More"}
                          </button>
                        )}
                      </div>
                    </>
                  ) : (
                    <div style={{ marginBottom: "100px" }}>
                      <h4
                        className="text-white text-center text-uppercase"
                        style={{ fontWeight: 800 }}
                      >
                        No Winner as of now
                      </h4>
                    </div>
                  )}
                </>
              )}
            </div>

            <div
              className="tab-pane fade"
              id="ex3-tabs-2"
              role="tabpanel"
              aria-labelledby="ex3-tab-2"
            >
              {loadingInstant ? (
                <div className="basket-loader-container">
                  <svg viewBox="25 25 50 50" className="loader-svg">
                    <circle r={20} cy={50} cx={50} className="loader" />
                  </svg>
                </div>
              ) : (
                <>
                  {dataInstant.length > 0 ? (
                    <>
                      <div>
                        {dataInstant.map((item, idx) => (
                          <div key={idx} className="winner-tab-list">
                            <InstantWinnerList ticket={item} />
                          </div>
                        ))}
                      </div>
                      <div className="winner-page-button-div">
                        {hasMoreInstant && (
                          <button onClick={handleLoadMoreInstant} disabled={!loadingButtonInstant} className="load-more-btn winner-button">
                            {!loadingButtonInstant ? "Loading..." : "Load More"}
                          </button>
                        )}
                      </div>
                    </>
                  ) : (
                    <div style={{ marginBottom: "100px" }}>
                      <h4
                        className="text-white text-center text-uppercase"
                        style={{ fontWeight: 800 }}
                      >
                        No Winner as of now
                      </h4>
                    </div>
                  )}
                </>
              )}
            </div>

          </div>
        </div>
      </div>
    </>
  );
};

export default Winners;
