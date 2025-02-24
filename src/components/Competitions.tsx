const Competitions = () => {
  return (
    <>
      <div className="comp-banner">
        <div className="comp-banner-txt">
          <h2>Competitions</h2>
        </div>
      </div>
      <div className="competion-boxes">
        <div className="container">
          <div className="competion-boxes-all">
            <div className="row">
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left">
                    {/* <img src="images/competion.png" alt="" /> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>0</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>8</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>0</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>45</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-onse">
                          <div className="comp-clock">
                            <span>
                              <svg
                                width={12}
                                height={12}
                                viewBox="0 0 12 12"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M6.5 5.5V3C6.5 2.86739 6.44732 2.74021 6.35355 2.64645C6.25979 2.55268 6.13261 2.5 6 2.5C5.86739 2.5 5.74021 2.55268 5.64645 2.64645C5.55268 2.74021 5.5 2.86739 5.5 3V6C5.5 6.13261 5.55268 6.25979 5.64645 6.35355C5.74021 6.44732 5.86739 6.5 6 6.5H8.5C8.63261 6.5 8.75979 6.44732 8.85355 6.35355C8.94732 6.25979 9 6.13261 9 6C9 5.86739 8.94732 5.74021 8.85355 5.64645C8.75979 5.55268 8.63261 5.5 8.5 5.5H6.5ZM6 11C3.2385 11 1 8.7615 1 6C1 3.2385 3.2385 1 6 1C8.7615 1 11 3.2385 11 6C11 8.7615 8.7615 11 6 11Z"
                                  fill="white"
                                />
                              </svg>
                            </span>
                            <div className="comp-clock-txt">
                              <p>
                                {" "}
                                <span>Draws Today</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          THE BIG XMAS CARD COMP – 440 INSTANT WINS PLUS £500
                          END DRAW
                        </h2>
                        <h4>
                          £0.46 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>34131 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>130000</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-one">
                    {/* <img src="images/competion.png" alt=""> */}
                    <div className="comp-bottom-tag">
                      <h4>Spend £10 get £10</h4>
                    </div>
                    <div className="comp-top-tag">
                      <h4>Just Launched</h4>
                    </div>
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>9</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>8</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>4</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>50</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Fri 29th Dec</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          EVERYONES A WINNER! – SPEND £10 GET £10 BACK + MYSTERY
                          END PRIZE!
                        </h2>
                        <h4>
                          £10.00 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-71">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>357 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>500</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>71%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="mob-comp-card"></div>
              </div>
            </div>
          </div>
          <div className="competion-boxes-all">
            <div className="row">
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-two">
                    {/* <img src="images/competion.png" alt=""> */}
                    {/* <div class="comp-bottom-tag">
                          <h4>Spend £10 get £10</h4>
                        </div>

                        <div class="comp-top-tag">
                          <h4>Just Launched</h4>
                        </div> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>12</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>10</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>34</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>26</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Mon 1st Jan</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          WIN THIS APACHE DESIGN 2023 FORD TRANSIT CUSTOM PLUS
                          500 INSTANT WINS
                        </h2>
                        <h4>
                          £2.79 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>19012 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>70999</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-three">
                    {/* <img src="images/competion.png" alt=""> */}
                    {/* <div class="comp-bottom-tag">
                          <h4>Spend £10 get £10</h4>
                        </div>

                        <div class="comp-top-tag">
                          <h4>Just Launched</h4>
                        </div> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>19</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>11</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>46</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>50</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Fri 29th Dec</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          £150,000 CASH IN INSTANT WINS TOTALLING 1200 WINS –
                          PLUS £5000 END DRAW
                        </h2>
                        <h4>
                          £3.98 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>15441 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>75999</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="mob-comp-card"></div>
              </div>
            </div>
          </div>
          <div className="competion-boxes-all">
            <div className="row">
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-one">
                    {/* <img src="images/competion.png" alt=""> */}
                    <div className="comp-bottom-tag">
                      <h4>Spend £10 get £10</h4>
                    </div>
                    <div className="comp-top-tag">
                      <h4>Just Launched</h4>
                    </div>
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>9</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>8</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>4</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>50</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Fri 29th Dec</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          EVERYONES A WINNER! – SPEND £10 GET £10 BACK + MYSTERY
                          END PRIZE!
                        </h2>
                        <h4>
                          £10.00 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-71">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>357 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>500</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>71%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="mob-comp-card"></div>
              </div>
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left">
                    {/* <img src="images/competion.png" alt=""> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>0</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>8</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>0</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>45</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-onse">
                          <div className="comp-clock">
                            <span>
                              <svg
                                width={12}
                                height={12}
                                viewBox="0 0 12 12"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M6.5 5.5V3C6.5 2.86739 6.44732 2.74021 6.35355 2.64645C6.25979 2.55268 6.13261 2.5 6 2.5C5.86739 2.5 5.74021 2.55268 5.64645 2.64645C5.55268 2.74021 5.5 2.86739 5.5 3V6C5.5 6.13261 5.55268 6.25979 5.64645 6.35355C5.74021 6.44732 5.86739 6.5 6 6.5H8.5C8.63261 6.5 8.75979 6.44732 8.85355 6.35355C8.94732 6.25979 9 6.13261 9 6C9 5.86739 8.94732 5.74021 8.85355 5.64645C8.75979 5.55268 8.63261 5.5 8.5 5.5H6.5ZM6 11C3.2385 11 1 8.7615 1 6C1 3.2385 3.2385 1 6 1C8.7615 1 11 3.2385 11 6C11 8.7615 8.7615 11 6 11Z"
                                  fill="white"
                                />
                              </svg>
                            </span>
                            <div className="comp-clock-txt">
                              <p>
                                {" "}
                                <span>Draws Today</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          THE BIG XMAS CARD COMP – 440 INSTANT WINS PLUS £500
                          END DRAW
                        </h2>
                        <h4>
                          £0.46 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>34131 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>130000</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="competion-boxes-all">
            <div className="row">
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-three">
                    {/* <img src="images/competion.png" alt=""> */}
                    {/* <div class="comp-bottom-tag">
                          <h4>Spend £10 get £10</h4>
                        </div>

                        <div class="comp-top-tag">
                          <h4>Just Launched</h4>
                        </div> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>19</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>11</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>46</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>50</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Fri 29th Dec</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          £150,000 CASH IN INSTANT WINS TOTALLING 1200 WINS –
                          PLUS £5000 END DRAW
                        </h2>
                        <h4>
                          £3.98 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>15441 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>75999</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="mob-comp-card"></div>
              </div>
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-two">
                    {/* <img src="images/competion.png" alt=""> */}
                    {/* <div class="comp-bottom-tag">
                          <h4>Spend £10 get £10</h4>
                        </div>

                        <div class="comp-top-tag">
                          <h4>Just Launched</h4>
                        </div> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>12</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>10</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>34</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>26</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Mon 1st Jan</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          WIN THIS APACHE DESIGN 2023 FORD TRANSIT CUSTOM PLUS
                          500 INSTANT WINS
                        </h2>
                        <h4>
                          £2.79 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>19012 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>70999</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="competion-boxes-all">
            <div className="row">
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left">
                    {/* <img src="images/competion.png" alt=""> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>0</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>8</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>0</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>45</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-onse">
                          <div className="comp-clock">
                            <span>
                              <svg
                                width={12}
                                height={12}
                                viewBox="0 0 12 12"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M6.5 5.5V3C6.5 2.86739 6.44732 2.74021 6.35355 2.64645C6.25979 2.55268 6.13261 2.5 6 2.5C5.86739 2.5 5.74021 2.55268 5.64645 2.64645C5.55268 2.74021 5.5 2.86739 5.5 3V6C5.5 6.13261 5.55268 6.25979 5.64645 6.35355C5.74021 6.44732 5.86739 6.5 6 6.5H8.5C8.63261 6.5 8.75979 6.44732 8.85355 6.35355C8.94732 6.25979 9 6.13261 9 6C9 5.86739 8.94732 5.74021 8.85355 5.64645C8.75979 5.55268 8.63261 5.5 8.5 5.5H6.5ZM6 11C3.2385 11 1 8.7615 1 6C1 3.2385 3.2385 1 6 1C8.7615 1 11 3.2385 11 6C11 8.7615 8.7615 11 6 11Z"
                                  fill="white"
                                />
                              </svg>
                            </span>
                            <div className="comp-clock-txt">
                              <p>
                                {" "}
                                <span>Draws Today</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          THE BIG XMAS CARD COMP – 440 INSTANT WINS PLUS £500
                          END DRAW
                        </h2>
                        <h4>
                          £0.46 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>34131 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>130000</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-one">
                    {/* <img src="images/competion.png" alt=""> */}
                    <div className="comp-bottom-tag">
                      <h4>Spend £10 get £10</h4>
                    </div>
                    <div className="comp-top-tag">
                      <h4>Just Launched</h4>
                    </div>
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>9</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>8</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>4</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>50</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Fri 29th Dec</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          EVERYONES A WINNER! – SPEND £10 GET £10 BACK + MYSTERY
                          END PRIZE!
                        </h2>
                        <h4>
                          £10.00 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-71">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>357 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>500</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>71%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="mob-comp-card"></div>
              </div>
            </div>
          </div>
          <div className="competion-boxes-all">
            <div className="row">
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-two">
                    {/* <img src="images/competion.png" alt=""> */}
                    {/* <div class="comp-bottom-tag">
                          <h4>Spend £10 get £10</h4>
                        </div>

                        <div class="comp-top-tag">
                          <h4>Just Launched</h4>
                        </div> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>12</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>10</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>34</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>26</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Mon 1st Jan</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          WIN THIS APACHE DESIGN 2023 FORD TRANSIT CUSTOM PLUS
                          500 INSTANT WINS
                        </h2>
                        <h4>
                          £2.79 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>19012 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>70999</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-three">
                    {/* <img src="images/competion.png" alt=""> */}
                    {/* <div class="comp-bottom-tag">
                          <h4>Spend £10 get £10</h4>
                        </div>

                        <div class="comp-top-tag">
                          <h4>Just Launched</h4>
                        </div> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>19</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>11</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>46</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>50</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Fri 29th Dec</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          £150,000 CASH IN INSTANT WINS TOTALLING 1200 WINS –
                          PLUS £5000 END DRAW
                        </h2>
                        <h4>
                          £3.98 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>15441 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>75999</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="mob-comp-card"></div>
              </div>
            </div>
          </div>
          <div className="competion-boxes-all">
            <div className="row">
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-one">
                    {/* <img src="images/competion.png" alt=""> */}
                    <div className="comp-bottom-tag">
                      <h4>Spend £10 get £10</h4>
                    </div>
                    <div className="comp-top-tag">
                      <h4>Just Launched</h4>
                    </div>
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>9</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>8</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>4</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>50</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Fri 29th Dec</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          EVERYONES A WINNER! – SPEND £10 GET £10 BACK + MYSTERY
                          END PRIZE!
                        </h2>
                        <h4>
                          £10.00 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-71">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>357 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>500</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>71%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="mob-comp-card"></div>
              </div>
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left">
                    {/* <img src="images/competion.png" alt=""> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>0</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>8</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>0</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>45</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-onse">
                          <div className="comp-clock">
                            <span>
                              <svg
                                width={12}
                                height={12}
                                viewBox="0 0 12 12"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M6.5 5.5V3C6.5 2.86739 6.44732 2.74021 6.35355 2.64645C6.25979 2.55268 6.13261 2.5 6 2.5C5.86739 2.5 5.74021 2.55268 5.64645 2.64645C5.55268 2.74021 5.5 2.86739 5.5 3V6C5.5 6.13261 5.55268 6.25979 5.64645 6.35355C5.74021 6.44732 5.86739 6.5 6 6.5H8.5C8.63261 6.5 8.75979 6.44732 8.85355 6.35355C8.94732 6.25979 9 6.13261 9 6C9 5.86739 8.94732 5.74021 8.85355 5.64645C8.75979 5.55268 8.63261 5.5 8.5 5.5H6.5ZM6 11C3.2385 11 1 8.7615 1 6C1 3.2385 3.2385 1 6 1C8.7615 1 11 3.2385 11 6C11 8.7615 8.7615 11 6 11Z"
                                  fill="white"
                                />
                              </svg>
                            </span>
                            <div className="comp-clock-txt">
                              <p>
                                {" "}
                                <span>Draws Today</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          THE BIG XMAS CARD COMP – 440 INSTANT WINS PLUS £500
                          END DRAW
                        </h2>
                        <h4>
                          £0.46 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>34131 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>130000</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="competion-boxes-all">
            <div className="row">
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-three">
                    {/* <img src="images/competion.png" alt=""> */}
                    {/* <div class="comp-bottom-tag">
                          <h4>Spend £10 get £10</h4>
                        </div>

                        <div class="comp-top-tag">
                          <h4>Just Launched</h4>
                        </div> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>19</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>11</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>46</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>50</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Fri 29th Dec</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          £150,000 CASH IN INSTANT WINS TOTALLING 1200 WINS –
                          PLUS £5000 END DRAW
                        </h2>
                        <h4>
                          £3.98 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>15441 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>75999</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="mob-comp-card"></div>
              </div>
              <div className="col-lg-6 col-md-12 col-sm-12">
                <div className="competion-box-part">
                  <div className="competion-box-part-left-two">
                    {/* <img src="images/competion.png" alt=""> */}
                    {/* <div class="comp-bottom-tag">
                          <h4>Spend £10 get £10</h4>
                        </div>

                        <div class="comp-top-tag">
                          <h4>Just Launched</h4>
                        </div> */}
                  </div>
                  <div className="competion-box-part-right">
                    <div className="comp-text-area">
                      <div className="comp-one">
                        <div className="draw-btn">
                          <div className="draw-btn-one">
                            <h4>12</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>10</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-one">
                            <h4>34</h4>
                            <p>DAYS</p>
                          </div>
                          <div className="draw-btn-two">
                            <h4>26</h4>
                            <p>DAYS</p>
                          </div>
                        </div>
                        <div className="comp-ones">
                          <div className="comps-clock">
                            <span>
                              <svg
                                width={10}
                                height={10}
                                viewBox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                                  fill="#2CB4A5"
                                />
                              </svg>
                            </span>
                            <div className="comps-clock-txt">
                              <p>
                                {" "}
                                Draw: <span>Mon 1st Jan</span>{" "}
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="comp-main-txt">
                        <h2>
                          WIN THIS APACHE DESIGN 2023 FORD TRANSIT CUSTOM PLUS
                          500 INSTANT WINS
                        </h2>
                        <h4>
                          £2.79 <span>PER ENTRY</span>
                        </h4>
                      </div>
                      <div className="comp-progress-all">
                        <div className="progs-26">
                          <h5>
                            <div className="progs-lef">
                              <img src="images/Ticket icon 1.svg" alt="" />
                              <h4>19012 </h4>
                            </div>
                            <div className="progs-rgt">
                              <h4>70999</h4>
                            </div>
                          </h5>
                        </div>
                        <div className="progs-per">
                          <h4>26%</h4>
                        </div>
                      </div>
                      <div className="comp-btnss">
                        <div className="comp-button-all">
                          <div className="comp-btn-lefts-card">
                            <div className="increase-quantity">
                              <form>
                                <input
                                  type="number"
                                  id="number"
                                  defaultValue={0}
                                  className="num-comp"
                                />
                                <div
                                  className="value-button"
                                  id="decrease-comp"
                                >
                                  -
                                </div>
                                <div
                                  className="value-button"
                                  id="increase-comp"
                                >
                                  +
                                </div>
                              </form>
                            </div>
                          </div>
                          <div className="comp-btn-rights-card">
                            <button type="button" className="enter-btn-comp">
                              ENTER
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default Competitions;
