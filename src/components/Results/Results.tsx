import { useEffect } from "react";

const Results = () => {

  useEffect(() => {
    const fetchMetaTags = async () => {
      try {
    
        const response = await fetch(`https://cggprelive.co.uk/wp-json/rankmath/v1/getHead?url=https://cggprelive.co.uk/results`);
        
        if (!response.ok) {
          throw new Error(`Error: ${response.statusText}`);
        }

        
        console.log('rankmath',response);

       
      } catch (err) {
        console.log('rankmath',err)
      } finally {
        console.log('done');
      }
    };

    fetchMetaTags();
  }, []);
  return (
    <div>
      <div className="comp-banner">
        <div className="comp-banner-txt">
          <h2>Draw Results</h2>
        </div>
      </div>

      <div className="responsible-gaming">
        <div className="container ">
          <div className="ticket-right-side draw">
            <div className="ticket-right-side-tabs">
              <ul
                className="nav nav-tabs nav-justified"
                id="ex1"
                role="tablist"
              >
                <li className="nav-item" role="presentation">
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
                <li className="nav-item" role="presentation">
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
                  <div className="draw-results-all-content">
                    <div className="draw-results-all-main">
                      <div className="draw-results-heading">
                        <svg
                          width={16}
                          height={19}
                          viewBox="0 0 16 19"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M3.42857 1.14286V2.28571H1.71429C0.767857 2.28571 0 3.05357 0 4V5.71429H16V4C16 3.05357 15.2321 2.28571 14.2857 2.28571H12.5714V1.14286C12.5714 0.510714 12.0607 0 11.4286 0C10.7964 0 10.2857 0.510714 10.2857 1.14286V2.28571H5.71429V1.14286C5.71429 0.510714 5.20357 0 4.57143 0C3.93929 0 3.42857 0.510714 3.42857 1.14286ZM16 6.85714H0V16.5714C0 17.5179 0.767857 18.2857 1.71429 18.2857H14.2857C15.2321 18.2857 16 17.5179 16 16.5714V6.85714Z"
                            fill="white"
                          />
                        </svg>
                        <h4>Wed, 10 Jan 2024</h4>
                      </div>
                      <div className="draw-results-sub-txt">
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4> OMC PEEKA BOO SUNGLASSES</h4>
                              <p>Ryan Baker - Ticket #14</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4> AVID BAIT STATION!</h4>
                              <p>Christopher Withers - Ticket #42</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>RIDGEMONKEY HUNTER 750 BAIT BOAT!</h4>
                              <p>James Crinson - Ticket #32</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>TRAKKER LEVELITE OVAL MF-HDR SLEEP SYSTEM</h4>
                              <p>Bruce Laughton - Ticket #47</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>NASH POWERBANX HUB 30K #2</h4>
                              <p>Brady Chabot - Ticket #182</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>TRAKKER ARMOLIFE CG-3 STOVE</h4>
                              <p>Richard Impey - Ticket #16 </p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                G SHOCK URBAN UTILITY GBA-900 SERIES WATCH #5
                              </h4>
                              <p>Haydn Farmer - Ticket #12</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>SOLAR SW MOON CHAIR</h4>
                              <p>Tom Christie - Ticket #194</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                JACKERY PORTABLE POWER STATION + 5 JACKERY
                                INSTANT WINS
                              </h4>
                              <p>Jamie Bainbridge - Ticket #2233</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                THROUGH THE NIGHT SPONTI – END DRAW ND BAIT BOAT
                                2 WITH SITE CREDIT INSTANT WINS
                              </h4>
                              <p>Mark Ancell - Ticket #5710</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>3x SHIMANO MGS 14000 XTD’S</h4>
                              <p>Mark Marsland - Ticket #8045</p>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="draw-results-all-content-two">
                    <div className="draw-results-all-main">
                      <div className="draw-results-heading">
                        <svg
                          width={16}
                          height={19}
                          viewBox="0 0 16 19"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M3.42857 1.14286V2.28571H1.71429C0.767857 2.28571 0 3.05357 0 4V5.71429H16V4C16 3.05357 15.2321 2.28571 14.2857 2.28571H12.5714V1.14286C12.5714 0.510714 12.0607 0 11.4286 0C10.7964 0 10.2857 0.510714 10.2857 1.14286V2.28571H5.71429V1.14286C5.71429 0.510714 5.20357 0 4.57143 0C3.93929 0 3.42857 0.510714 3.42857 1.14286ZM16 6.85714H0V16.5714C0 17.5179 0.767857 18.2857 1.71429 18.2857H14.2857C15.2321 18.2857 16 17.5179 16 16.5714V6.85714Z"
                            fill="white"
                          />
                        </svg>
                        <h4>TUE, 9 Jan 2024</h4>
                      </div>
                      <div className="draw-results-sub-txt">
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4> 1 HOUR SPONTI – ND BAIT BOAT 2 FOR 12P</h4>
                              <p>Darron Alderson - Ticket #713</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                SPONTI – 3x KORDA KAIZEN CARP RODS 3.5LB 12FT
                              </h4>
                              <p>James Cole - Ticket #32191</p>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="draw-results-all-content-two">
                    <div className="draw-results-all-main">
                      <div className="draw-results-heading">
                        <svg
                          width={16}
                          height={19}
                          viewBox="0 0 16 19"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M3.42857 1.14286V2.28571H1.71429C0.767857 2.28571 0 3.05357 0 4V5.71429H16V4C16 3.05357 15.2321 2.28571 14.2857 2.28571H12.5714V1.14286C12.5714 0.510714 12.0607 0 11.4286 0C10.7964 0 10.2857 0.510714 10.2857 1.14286V2.28571H5.71429V1.14286C5.71429 0.510714 5.20357 0 4.57143 0C3.93929 0 3.42857 0.510714 3.42857 1.14286ZM16 6.85714H0V16.5714C0 17.5179 0.767857 18.2857 1.71429 18.2857H14.2857C15.2321 18.2857 16 17.5179 16 16.5714V6.85714Z"
                            fill="white"
                          />
                        </svg>
                        <h4>SUN, 7 Jan 2024</h4>
                      </div>
                      <div className="draw-results-sub-txt">
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                {" "}
                                SUNDAY FUNDAY SPONTI – KORDA SPRING BOW LANDING
                                NET
                              </h4>
                              <p>Tom Booth - Ticket #1764</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                {" "}
                                SUNDAY FUNDAY SPONTI – JACKERY POWER PACK
                              </h4>
                              <p>William Ramshaw - Ticket #2072</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                {" "}
                                SUNDAY FUNDAY SPONTI – 3 X SHIMANO TX2 RODS
                              </h4>
                              <p>Taylor Squelch - Ticket #332</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                SUNDAY FUNDAY SPONTI – SOLAR SP UNI SPIDER
                                BIVVY!
                              </h4>
                              <p>Fraser Williams - Ticket #91</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                SUNDAY FUNDAY SPONTI – CARPSIGHT OCELLUS RANGE
                                FINDER
                              </h4>
                              <p>Chris King - Ticket #2736</p>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  className="tab-pane fade"
                  id="ex3-tabs-2"
                  role="tabpanel"
                  aria-labelledby="ex3-tab-2"
                >
                  <div className="draw-results-all-content">
                    <div className="draw-results-all-main">
                      <div className="draw-results-heading">
                        <svg
                          width={16}
                          height={19}
                          viewBox="0 0 16 19"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M3.42857 1.14286V2.28571H1.71429C0.767857 2.28571 0 3.05357 0 4V5.71429H16V4C16 3.05357 15.2321 2.28571 14.2857 2.28571H12.5714V1.14286C12.5714 0.510714 12.0607 0 11.4286 0C10.7964 0 10.2857 0.510714 10.2857 1.14286V2.28571H5.71429V1.14286C5.71429 0.510714 5.20357 0 4.57143 0C3.93929 0 3.42857 0.510714 3.42857 1.14286ZM16 6.85714H0V16.5714C0 17.5179 0.767857 18.2857 1.71429 18.2857H14.2857C15.2321 18.2857 16 17.5179 16 16.5714V6.85714Z"
                            fill="white"
                          />
                        </svg>
                        <h4>Wed, 10 Jan 2024</h4>
                      </div>
                      <div className="draw-results-sub-txt">
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4> OMC PEEKA BOO SUNGLASSES</h4>
                              <p>Ryan Baker - Ticket #14</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4> AVID BAIT STATION!</h4>
                              <p>Christopher Withers - Ticket #42</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>RIDGEMONKEY HUNTER 750 BAIT BOAT!</h4>
                              <p>James Crinson - Ticket #32</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>TRAKKER LEVELITE OVAL MF-HDR SLEEP SYSTEM</h4>
                              <p>Bruce Laughton - Ticket #47</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>NASH POWERBANX HUB 30K #2</h4>
                              <p>Brady Chabot - Ticket #182</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>TRAKKER ARMOLIFE CG-3 STOVE</h4>
                              <p>Richard Impey - Ticket #16 </p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                G SHOCK URBAN UTILITY GBA-900 SERIES WATCH #5
                              </h4>
                              <p>Haydn Farmer - Ticket #12</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>SOLAR SW MOON CHAIR</h4>
                              <p>Tom Christie - Ticket #194</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                JACKERY PORTABLE POWER STATION + 5 JACKERY
                                INSTANT WINS
                              </h4>
                              <p>Jamie Bainbridge - Ticket #2233</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                THROUGH THE NIGHT SPONTI – END DRAW ND BAIT BOAT
                                2 WITH SITE CREDIT INSTANT WINS
                              </h4>
                              <p>Mark Ancell - Ticket #5710</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list-two">
                          <ul>
                            <li>
                              <h4>3x SHIMANO MGS 14000 XTD’S</h4>
                              <p>Mark Marsland - Ticket #8045</p>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="draw-results-all-content-two">
                    <div className="draw-results-all-main">
                      <div className="draw-results-heading">
                        <svg
                          width={16}
                          height={19}
                          viewBox="0 0 16 19"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M3.42857 1.14286V2.28571H1.71429C0.767857 2.28571 0 3.05357 0 4V5.71429H16V4C16 3.05357 15.2321 2.28571 14.2857 2.28571H12.5714V1.14286C12.5714 0.510714 12.0607 0 11.4286 0C10.7964 0 10.2857 0.510714 10.2857 1.14286V2.28571H5.71429V1.14286C5.71429 0.510714 5.20357 0 4.57143 0C3.93929 0 3.42857 0.510714 3.42857 1.14286ZM16 6.85714H0V16.5714C0 17.5179 0.767857 18.2857 1.71429 18.2857H14.2857C15.2321 18.2857 16 17.5179 16 16.5714V6.85714Z"
                            fill="white"
                          />
                        </svg>
                        <h4>TUE, 9 Jan 2024</h4>
                      </div>
                      <div className="draw-results-sub-txt">
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4> 1 HOUR SPONTI – ND BAIT BOAT 2 FOR 12P</h4>
                              <p>Darron Alderson - Ticket #713</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list-two">
                          <ul>
                            <li>
                              <h4>
                                SPONTI – 3x KORDA KAIZEN CARP RODS 3.5LB 12FT
                              </h4>
                              <p>James Cole - Ticket #32191</p>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="draw-results-all-content-two">
                    <div className="draw-results-all-main">
                      <div className="draw-results-heading">
                        <svg
                          width={16}
                          height={19}
                          viewBox="0 0 16 19"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M3.42857 1.14286V2.28571H1.71429C0.767857 2.28571 0 3.05357 0 4V5.71429H16V4C16 3.05357 15.2321 2.28571 14.2857 2.28571H12.5714V1.14286C12.5714 0.510714 12.0607 0 11.4286 0C10.7964 0 10.2857 0.510714 10.2857 1.14286V2.28571H5.71429V1.14286C5.71429 0.510714 5.20357 0 4.57143 0C3.93929 0 3.42857 0.510714 3.42857 1.14286ZM16 6.85714H0V16.5714C0 17.5179 0.767857 18.2857 1.71429 18.2857H14.2857C15.2321 18.2857 16 17.5179 16 16.5714V6.85714Z"
                            fill="white"
                          />
                        </svg>
                        <h4>SUN, 7 Jan 2024</h4>
                      </div>
                      <div className="draw-results-sub-txt">
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                {" "}
                                SUNDAY FUNDAY SPONTI – KORDA SPRING BOW LANDING
                                NET
                              </h4>
                              <p>Tom Booth - Ticket #1764</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                {" "}
                                SUNDAY FUNDAY SPONTI – JACKERY POWER PACK
                              </h4>
                              <p>William Ramshaw - Ticket #2072</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                {" "}
                                SUNDAY FUNDAY SPONTI – 3 X SHIMANO TX2 RODS
                              </h4>
                              <p>Taylor Squelch - Ticket #332</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list">
                          <ul>
                            <li>
                              <h4>
                                SUNDAY FUNDAY SPONTI – SOLAR SP UNI SPIDER
                                BIVVY!
                              </h4>
                              <p>Fraser Williams - Ticket #91</p>
                            </li>
                          </ul>
                        </div>
                        <div className="draw-results-sub-list-two">
                          <ul>
                            <li>
                              <h4>
                                SUNDAY FUNDAY SPONTI – CARPSIGHT OCELLUS RANGE
                                FINDER
                              </h4>
                              <p>Chris King - Ticket #2736</p>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div className="draw-load-more">
                <button type="button" className="load-more-draw">
                  Load More
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Results;
