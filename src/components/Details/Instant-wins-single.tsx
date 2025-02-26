import { useEffect, useState } from "react";
import { InstantWinTickets, RewardsType } from "../../types";
import DOMPurify from "dompurify";
// import { truncateText } from "../../utils";
import { getMediaUrl } from "../../utils/imageS3Url";

interface PropsType {
  instantWins: RewardsType[];
  rewardWins: RewardsType[];
  instantWinTicket: InstantWinTickets[];
  isEnableInsatntWins: boolean;
  showHowItWorks: (clicked: boolean) => void;
  isEnableRewardWins: boolean;
}

const InstantWinsSingle: React.FC<PropsType> = ({
  instantWins,
  instantWinTicket,
  showHowItWorks,
  isEnableInsatntWins,
  rewardWins,
  isEnableRewardWins,
}) => {
  const [innerWidth, setInnerWidth] = useState<number>(window.innerWidth);

  useEffect(() => {
    const handleResize = () => setInnerWidth(window.innerWidth);
    window.addEventListener("resize", handleResize);
    return () => window.removeEventListener("resize", handleResize);
  }, []);

  if (!isEnableInsatntWins && !isEnableRewardWins) {
    return null;
  }

  const rewardLength = rewardWins.length;

  function chunkArray(array: RewardsType[], chunkSize: number) {
    const chunks = [];
    for (let i = 0; i < array.length; i += chunkSize) {
      chunks.push(array.slice(i, i + chunkSize));
    }
    return chunks;
  }

  const ChunkedRewardWins = chunkArray(rewardWins, 5);

  const S3_BASE_URL = import.meta.env.VITE_STATIC_IMAGES_URL;


  return (
    <section className="bait-instant-win" id="instant-win">
      <div className="container">
        {isEnableInsatntWins && (
          <>
            {" "}
            <div className="bait-reward-heading">
              <div className="bait-reward-center">
                <h2>
                  <div className="bait-instant-win-head-title">
                    <img src={`${S3_BASE_URL}/images/bait-instant.png`} />
                    <svg
                      className="bait-instant-icon"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M4 14L14 3V10H20L10 21V14H4Z"
                        fill="white"
                        stroke="white"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      ></path>
                    </svg>
                    <h2>Instant Wins</h2>
                  </div>
                </h2>
              </div>
            </div>
            <div className="bait-how it works">
              <a href="#how-it-work" onClick={() => showHowItWorks(true)}>
                How it works
              </a>
            </div>
            <div className="bait-instant-win-box">
              <div className="bait-instant-win-box-all">
                <div className="row inst-price-parts">
                  {instantWins.map((item:any) => {
                    //  const totalinstantPrize = instantWinTicket.filter( 
                    //   (ticket:any) => ticket.instant_id === item.id
                    // );
                    const filterPrize = instantWinTicket.filter( 
                      (ticket:any) => ticket.instant_id === item.id &&  ticket.ticket_number != null && ticket.ticket_number != ""
                    );
                    const pendingTickets = filterPrize.filter(
                      (prize:any) => prize.user_id === null
                    );

                    // const pendingTickets = [...filterPrize];

                    const soldTickets = filterPrize.filter(
                      (prize:any) => prize.user_id
                    );

                    return (
                      <ChildComponent
                        key={item.id}
                        item={item}
                        filterPrize={filterPrize}
                        pendingTickets={pendingTickets}
                        soldTickets={soldTickets}
                      />
                    );
                  })}
                </div>
              </div>
            </div>
            {instantWins && instantWins.length > 0 ? (
              <div className="bottom-nav">
                <a href="#instant-win" className="Instant-bottom-btn">
                  <div className="Instant-wins-bottom">
                    <div className="instant-bottom-icon">
                      <svg
                        width="13"
                        height="15"
                        viewBox="0 0 13 15"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path
                          d="M1.5 8.94444L7.75 1V6.05556H11.5L5.25 14V8.94444H1.5Z"
                          fill="#0F1010"
                          stroke="#0F1010"
                          strokeWidth="2"
                          strokeLinecap="round"
                          strokeLinejoin="round"
                        />
                      </svg>
                    </div>
                    <p> InstanT Wins</p>
                  </div>
                </a>
              </div>
            ) : (
              ""
            )}
          </>
        )}

        {/* reward wins */}
        {isEnableRewardWins && (
          <>
            <div
              className={`bait-reward-win ${
                isEnableInsatntWins && "reward-margin"
              }`}
              id="reward-scroll"
            >
              <div className="bait-reward-heading">
                <div className="bait-reward-center">
                  <h2>
                    <div className="bait-instant-win-head-title">
                      <svg
                        className="bait-instant-icon"
                        width="22"
                        height="22"
                        viewBox="0 0 22 22"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <rect
                          x="11"
                          width="14.9289"
                          height="14.9289"
                          rx="2"
                          transform="rotate(45 11 0)"
                          fill="white"
                        ></rect>
                      </svg>
                      <img src={`${S3_BASE_URL}/images/bait-reward.png`} />
                      <h2>Reward Wins</h2>
                    </div>
                  </h2>
                </div>
              </div>

              <div className="bait-how it works">
                <a href="#how-it-work" onClick={() => showHowItWorks(true)}>
                  How it works
                </a>
              </div>
            </div>
            <div className="bait-reward-win-screen">
              <div className="bait-reward-win-screen-all">
                <div className="bait-reward-win-screen-all-one"></div>
              </div>
            </div>
            <div className="bait-reward-win-all">
              <div className="bait-reward-win-all-parts">
                {innerWidth < 577 &&
                  rewardWins.map((reward, index) => (
                    <div
                      className={`bait-reward-win-all-parts-one image-outer ${
                        reward.reward_open && "active"
                      }`}
                      key={reward.id}
                    >
                      <div
                        className={`bait-reward-win-all-parts-one-pic-act ${
                          index > 4 && "rewards-top-space"
                        } ${
                          innerWidth <= 576 &&
                          index + 1 !== rewardLength &&
                          "line-select"
                        } ${
                          (index + 1) % 5 !== 0 &&
                          index !== rewardLength - 1 &&
                          "line-select"
                        } ${
                          (index % 5 === 0 ||
                            (index + 1) % 5 === 0 ||
                            innerWidth <= 576) &&
                          "line-select-a"
                        } ${
                          ((index !== 0 && index % 5 !== 0) ||
                            (index > 0 && innerWidth <= 576)) &&
                          "line-select-b"
                        }`}
                      >
                        <img src={getMediaUrl(reward.image)} alt="" />
                        <div className="bait-reward-links-act">
                          <h4>{index + 1}</h4>
                        </div>
                      </div>
                      <div className="bait-reward-win-all-parts-one-txt">
                        <div className="bait-reward-win-all-parts-one-txt-head">
                          <h2>{reward.title}</h2>
                        </div>
                        {reward.full_name && (
                          <div className="bait-reward-win-all-parts-one-txt-para">
                            <p>
                              {reward.full_name}{" "}
                              <span>{reward.ticket_number}</span>
                            </p>
                          </div>
                        )}
                      </div>
                    </div>
                  ))}
              </div>
              {innerWidth > 576 &&
                ChunkedRewardWins.map((rewardWinsArr, chunkIndex) => {
                  const reversedRow = (chunkIndex + 1) % 2 === 0;
                  const lengthOfArray = rewardWinsArr.length;
                  return (
                    <div
                      className={`bait-reward-win-all-parts ${
                        reversedRow && "bait-reward-reversed"
                      }`}
                      key={chunkIndex}
                    >
                      {rewardWinsArr.map((reward, index) => {
                        const trueIndex = index + 1 + 5 * chunkIndex;

                        const showLineSelect = reversedRow
                          ? index !== 0 && "line-select"
                          : (index + 1) % 5 !== 0 && "line-select";

                        const showLineSelectBClass = reversedRow
                          ? index === 0 || (index + 1) % 5 !== 0
                          : index !== 0 && index % 5 !== 0;

                        const isLastElement =
                          !reversedRow && lengthOfArray < 5
                            ? index === lengthOfArray - 1
                            : false;

                        const isLastElementInReverseRow =
                          reversedRow && lengthOfArray < 5
                            ? index === lengthOfArray - 1
                            : false;

                        return (
                          <div
                            className={`bait-reward-win-all-parts-one image-outer ${
                              reward.reward_open && "active"
                            }`}
                            key={reward.id}
                          >
                            <div
                              className={`bait-reward-win-all-parts-one-pic-act ${
                                index > 4 && "rewards-top-space"
                              } ${
                                innerWidth <= 576 &&
                                index + 1 !== lengthOfArray &&
                                "line-select"
                              } ${
                                showLineSelect &&
                                !isLastElement &&
                                "line-select"
                              } ${
                                (index % 5 === 0 ||
                                  (index + 1) % 5 === 0 ||
                                  innerWidth <= 576) &&
                                "line-select-a"
                              } ${
                                ((showLineSelectBClass &&
                                  !isLastElementInReverseRow) ||
                                  (index > 0 && innerWidth <= 576)) &&
                                "line-select-b"
                              }`}
                            >
                              <img src={getMediaUrl(reward.image)} alt="" />
                              <div className="bait-reward-links-act">
                                <h4>{trueIndex}</h4>
                              </div>
                            </div>
                            <div className="bait-reward-win-all-parts-one-txt">
                              <div className="bait-reward-win-all-parts-one-txt-head">
                                <h2>{reward.title}</h2>
                              </div>
                              {reward.full_name && (
                                <div className="bait-reward-win-all-parts-one-txt-para">
                                  <p>
                                    {reward.full_name}{" "}
                                    <span>{reward.ticket_number}</span>
                                  </p>
                                </div>
                              )}
                            </div>
                          </div>
                        );
                      })}
                    </div>
                  );
                })}
            </div>
          </>
        )}
        {/* <div className="bait-reward-win-alls">
            <div className="bait-reward-win-all-parts next-direction">
              <div
                className="bait-reward-win-all-parts-one image-outer "
                id="mobile-content"
              >
                <div className="bait-reward-win-all-parts-one-pic-act line-select ">
                  <img src="/images/bait-reward-win-6.png" alt="" />
                  <div className="bait-reward-links-act">
                    <h4>10</h4>
                  </div>
                </div>
                <div className="bait-reward-win-all-parts-one-txt">
                  <div className="bait-reward-win-all-parts-one-txt-head ">
                    <h2>NASH TITAN T2 PRO BIVVY</h2>
                  </div>
                </div>
              </div>
              <div className="bait-reward-win-all-parts-one image-outer">
                <div className="bait-reward-win-all-parts-one-pic-act line-select line-select-b">
                  <img src="/images/bait-reward-win-7.png" alt="" />
                  <div className="bait-reward-links-act">
                    <h4>9</h4>
                  </div>
                </div>
                <div className="bait-reward-win-all-parts-one-txt">
                  <div className="bait-reward-win-all-parts-one-txt-head">
                    <h2>3X Nash Scope Cork Rods</h2>
                  </div>
                </div>
              </div>
              <div className="bait-reward-win-all-parts-one image-outer">
                <div className="bait-reward-win-all-parts-one-pic-act line-select line-select-b">
                  <img src="/images/bait-reward-win-4.png" alt="" />
                  <div className="bait-reward-links-act">
                    <h4>8</h4>
                  </div>
                </div>
                <div className="bait-reward-win-all-parts-one-txt">
                  <div className="bait-reward-win-all-parts-one-txt-head">
                    <h2>£50.00</h2>
                  </div>
                </div>
              </div>
              <div className="bait-reward-win-all-parts-one image-outer">
                <div className="bait-reward-win-all-parts-one-pic-act line-select line-select-b">
                  <img src="/images/bait-reward-win-8.png" alt="" />
                  <div className="bait-reward-links-act">
                    <h4>7</h4>
                  </div>
                </div>
                <div className="bait-reward-win-all-parts-one-txt">
                  <div className="bait-reward-win-all-parts-one-txt-head">
                    <h2>FOX FRONTIER X BIVVY</h2>
                  </div>
                </div>
              </div>
              <div
                className="bait-reward-win-all-parts-one image-outer"
                id="mobile-content-bottom"
              >
                <div className="bait-reward-win-all-parts-one-pic-act line-select-a line-select-b mobile-after">
                  <img src="/images/bait-reward-win-9.png" alt="" />
                  <div className="bait-reward-links-act">
                    <h4>6</h4>
                  </div>
                </div>
                <div className="bait-reward-win-all-parts-one-txt">
                  <div className="bait-reward-win-all-parts-one-txt-head">
                    <h2>Carp Royal Duke Bait Boat</h2>
                  </div>
                </div>
              </div>
            </div>
          </div> */}
      </div>
    </section>
  );
};

export default InstantWinsSingle;

interface ChildProps {
  item: RewardsType;
  filterPrize: InstantWinTickets[];
  pendingTickets: InstantWinTickets[];
  soldTickets: InstantWinTickets[];
}

const ChildComponent: React.FC<ChildProps> = ({
  item,
  filterPrize,
  pendingTickets,
  soldTickets,
}) => {
  const [innerWidth, setInnerWidth] = useState(window.innerHeight);
  console.log("innerWidth", innerWidth);
  // const [ticketsLength] = useState(() => {
  //   const tickets = pendingTickets.slice(0, 6);
  //   const numberOfFourLengthTickets = tickets.filter(
  //     (ticket) => ticket.ticket_number.length === 4
  //   ).length;

  //   if (innerWidth < 450) {
  //     if (numberOfFourLengthTickets === 2) {
  //       return 5;
  //     } else if (numberOfFourLengthTickets === 1) {
  //       return 4;
  //     } else {
  //       return 5;
  //     }
  //   }

  //   if (numberOfFourLengthTickets === 2) {
  //     return 6;
  //   } else if (numberOfFourLengthTickets === 1) {
  //     return 5;
  //   } else {
  //     return 6;
  //   }
  // });
  // const [displayCount, setDisplayCount] = useState(ticketsLength);
  const [isShowingMore, setIsShowingMore] = useState<boolean>(false);
  const [tab, setTab] = useState<string>("ticket");
  const [visibleTickets, setVisibleTickets] = useState(24);

  useEffect(() => {
    const handleResize = () => setInnerWidth(window.innerWidth);
    window.addEventListener("resize", handleResize);
    return () => window.removeEventListener("resize", handleResize);
  }, []);

  const handleShowMore = () => {
    // if (isShowingMore) {
    //   setDisplayCount(() => ticketsLength);
    // } else {
    //   setDisplayCount(() => pendingTickets.length);
    // }
    setIsShowingMore((prevState) => !prevState);
  };

  const handleTabs = (tab: string) => {
    setTab(tab);
  };

  const handleLoadMore = () => {
    setVisibleTickets(pendingTickets.length + soldTickets.length);
  };

  const handleShowLess = () => {
    setVisibleTickets(24);
  };

  console.log("pendingTickets", pendingTickets);
  console.log("soldTickets", soldTickets);
  console.log("isShowingMore", isShowingMore);
  console.log(filterPrize, "filterPrize");

  const formatName = (fullName: string | null) => {
    if (!fullName) return "";

    const nameParts = fullName.split(" ");
    const surname = nameParts[nameParts.length - 1]; // Get the last part (surname)
    const firstNameInitial = nameParts[0][0]; // Get the first letter of the first name

    // Combine surname and first initial, and truncate if too long
    const formattedName = `${surname} ${firstNameInitial}`;
    const maxLength = 7; // Set a max length for the name to display

    return formattedName.length > maxLength
      ? `${formattedName.slice(0, maxLength)}...`
      : formattedName;
  };
  // Function to clean up unwanted tags and inline styles
  const cleanUnwantedTags = (htmlString: string) => {
    return (
      htmlString
        // Remove <colgroup>, <col> tags, and <style> blocks
        .replace(/<colgroup[^>]*>.*?<\/colgroup>/g, "")
        .replace(/<col[^>]*>/g, "")
        .replace(/<style[^>]*>.*?<\/style>/g, "")
        // Optionally clean empty <p> tags
        .replace(/<p>\s*<\/p>/g, "")
    );
  };

  // Optional function to remove inline styles from images, keeping the `src` intact
  // const cleanImageStyles = (htmlString:string) => {
  //   return htmlString.replace(/<img[^>]*style="[^"]*"([^>]*)>/g, '<img$1>');
  // };

  // Full cleaning process
  const processHTML = (htmlString: string) => {
    let cleanedHTML = cleanUnwantedTags(htmlString);
    return DOMPurify.sanitize(cleanedHTML);
  };

  return (
    <div className="col-lg-6 col-md-12 col-sm-12 nin-spa box-win-sitewide">
      <div
        className={`${
          pendingTickets.length > 0
            ? isShowingMore
              ? tab === "ticket"
                ? "bait-instant-win-boxx-ticket-available-full"
                : "bait-instant-win-boxx-desc-available-full"
              : "bait-instant-win-boxx-available"
            : isShowingMore
            ? tab === "ticket"
              ? "bait-instant-win-boxx-ticket-won-full"
              : "bait-instant-win-boxx-desc-won-full"
            : "bait-instant-win-boxx-won"
        }`}
      >
        {/* {pendingTickets.length > 0 ? (
          <div className="bait-instant-win-boxx-circle">
            <svg width="58" height="58" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="60" cy="30" r="28" fill="#202323" stroke="#FFFFFF33" strokeWidth="0.5" />
            </svg>
          </div>
        ) : (
          <div className="bait-instant-win-boxx-circle">
            <svg width="58" height="58" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="60" cy="30" r="28" fill="#202323" stroke="#EEC273" strokeWidth="0.5" />
            </svg>
          </div>
        )} */}

        {pendingTickets.length > 0 && (
          <div className="bait-instant-win-boxx-flag"></div>
        )}

        <div className="bait-instant-win-boxx-head">
          <div className="bait-instant-win-boxx-container">
            <div className="bait-instant-win-boxx-head-left">
              <img src={item.image} alt="" />
            </div>
            {pendingTickets.length > 0 ? (
              <div className="bait-instant-win-boxx-head-right-available">
                <h4>{item.title}</h4>
                <p>
                  <span className="ins-slash">
                    {filterPrize.length - soldTickets.length} LEFT TO BE WON
                  </span>
                </p>
              </div>
            ) : (
              <div className="bait-instant-win-boxx-head-right-won">
                <h4>{item.title}</h4>
                <p>
                  <span className="ins-slash">ALL PRIZES WON</span>
                </p>
              </div>
            )}
          </div>
          {pendingTickets.length === 0 ? (
            <div
              className="bait-instant-win-boxx-head-down-arrow"
              role="button"
              id={"arrow-1"}
              onClick={handleShowMore}
            >
              {!isShowingMore ? (
                <svg
                  width="28"
                  height="16"
                  viewBox="0 0 28 16"
                  fill="#EEC273"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M14.1423 10.6278L24.0423 0.727764C24.2268 0.536743 24.4475 0.384379 24.6915 0.279561C24.9355 0.174743 25.1979 0.119571 25.4635 0.117263C25.729 0.114955 25.9924 0.165559 26.2382 0.266121C26.484 0.366682 26.7073 0.515189 26.8951 0.702974C27.0829 0.890759 27.2314 1.11406 27.3319 1.35986C27.4325 1.60565 27.4831 1.86901 27.4808 2.13457C27.4785 2.40013 27.4233 2.66256 27.3185 2.90657C27.2137 3.15058 27.0613 3.37127 26.8703 3.55576L15.5563 14.8698C15.1812 15.2447 14.6726 15.4553 14.1423 15.4553C13.612 15.4553 13.1033 15.2447 12.7283 14.8698L1.41429 3.55576C1.22327 3.37127 1.0709 3.15058 0.966085 2.90657C0.861267 2.66256 0.806094 2.40013 0.803786 2.13457C0.801479 1.86901 0.852082 1.60565 0.952644 1.35986C1.05321 1.11406 1.20171 0.890759 1.3895 0.702974C1.57728 0.515189 1.80059 0.366682 2.04638 0.266121C2.29217 0.165559 2.55553 0.114955 2.82109 0.117263C3.08665 0.119571 3.34909 0.174743 3.5931 0.279561C3.8371 0.384379 4.05779 0.536743 4.24229 0.727764L14.1423 10.6278Z"
                    fill="#EEC273"
                  />
                </svg>
              ) : (
                <svg
                  width="28"
                  height="16"
                  viewBox="0 0 28 16"
                  fill="#EEC273"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M13.8577 5.37224L3.95771 15.2722C3.77322 15.4633 3.55253 15.6156 3.30852 15.7204C3.06451 15.8253 2.80207 15.8804 2.53651 15.8827C2.27096 15.885 2.0076 15.8344 1.7618 15.7339C1.51601 15.6333 1.29271 15.4848 1.10492 15.297C0.917135 15.1092 0.768629 14.8859 0.668068 14.6401C0.567507 14.3943 0.516903 14.131 0.519211 13.8654C0.521519 13.5999 0.576691 13.3374 0.681509 13.0934C0.786328 12.8494 0.93869 12.6287 1.12971 12.4442L12.4437 1.13024C12.8188 0.755294 13.3274 0.544662 13.8577 0.544662C14.388 0.544662 14.8967 0.755294 15.2717 1.13024L26.5857 12.4442C26.7767 12.6287 26.9291 12.8494 27.0339 13.0934C27.1387 13.3374 27.1939 13.5999 27.1962 13.8654C27.1985 14.131 27.1479 14.3944 27.0474 14.6401C26.9468 14.8859 26.7983 15.1092 26.6105 15.297C26.4227 15.4848 26.1994 15.6333 25.9536 15.7339C25.7078 15.8344 25.4445 15.885 25.1789 15.8827C24.9134 15.8804 24.6509 15.8253 24.4069 15.7204C24.1629 15.6156 23.9422 15.4633 23.7577 15.2722L13.8577 5.37224Z"
                    fill="#EEC273"
                  />
                </svg>
              )}
            </div>
          ) : (
            <div
              className="bait-instant-win-boxx-head-down-arrow"
              role="button"
              id={"arrow-2"}
              onClick={handleShowMore}
            >
              {!isShowingMore ? (
                <svg
                  width="28"
                  height="16"
                  viewBox="0 0 28 16"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M14.1423 10.6278L24.0423 0.727764C24.2268 0.536743 24.4475 0.384379 24.6915 0.279561C24.9355 0.174743 25.1979 0.119571 25.4635 0.117263C25.729 0.114955 25.9924 0.165559 26.2382 0.266121C26.484 0.366682 26.7073 0.515189 26.8951 0.702974C27.0829 0.890759 27.2314 1.11406 27.3319 1.35986C27.4325 1.60565 27.4831 1.86901 27.4808 2.13457C27.4785 2.40012 27.4233 2.66256 27.3185 2.90657C27.2137 3.15058 27.0613 3.37127 26.8703 3.55576L15.5563 14.8698C15.1812 15.2447 14.6726 15.4553 14.1423 15.4553C13.612 15.4553 13.1033 15.2447 12.7283 14.8698L1.41429 3.55576C1.22327 3.37127 1.0709 3.15058 0.966085 2.90657C0.861267 2.66256 0.806094 2.40012 0.803786 2.13457C0.801479 1.86901 0.852082 1.60565 0.952644 1.35986C1.05321 1.11406 1.20171 0.890759 1.3895 0.702974C1.57728 0.515189 1.80059 0.366682 2.04638 0.266121C2.29217 0.165559 2.55553 0.114955 2.82109 0.117263C3.08665 0.119571 3.34909 0.174743 3.5931 0.279561C3.8371 0.384379 4.05779 0.536743 4.24229 0.727764L14.1423 10.6278Z"
                    fill="#2CB4A5"
                  />
                </svg>
              ) : (
                <svg
                  width="28"
                  height="16"
                  viewBox="0 0 28 16"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M13.8577 5.37224L3.95771 15.2722C3.77322 15.4633 3.55253 15.6156 3.30852 15.7204C3.06451 15.8253 2.80207 15.8804 2.53651 15.8827C2.27096 15.885 2.0076 15.8344 1.7618 15.7339C1.51601 15.6333 1.29271 15.4848 1.10492 15.297C0.917135 15.1092 0.768629 14.8859 0.668068 14.6401C0.567507 14.3943 0.516903 14.131 0.519211 13.8654C0.521519 13.5999 0.576691 13.3374 0.681509 13.0934C0.786328 12.8494 0.93869 12.6287 1.12971 12.4442L12.4437 1.13024C12.8188 0.755294 13.3274 0.544662 13.8577 0.544662C14.388 0.544662 14.8967 0.755294 15.2717 1.13024L26.5857 12.4442C26.7767 12.6287 26.9291 12.8494 27.0339 13.0934C27.1387 13.3374 27.1939 13.5999 27.1962 13.8654C27.1985 14.131 27.1479 14.3944 27.0474 14.6401C26.9468 14.8859 26.7983 15.1092 26.6105 15.297C26.4227 15.4848 26.1994 15.6333 25.9536 15.7339C25.7078 15.8344 25.4445 15.885 25.1789 15.8827C24.9134 15.8804 24.6509 15.8253 24.4069 15.7204C24.1629 15.6156 23.9422 15.4633 23.7577 15.2722L13.8577 5.37224Z"
                    fill="#2CB4A5"
                  />
                </svg>
              )}
            </div>
          )}
        </div>
        {isShowingMore &&
          (pendingTickets.length > 0 ? (
            <div className="bait-instant-win-boxx-available-navbar">
              {item.show_description > 0 && item.prize_description ? (
                <>
                  <div
                    className={`bait-instant-win-boxx-navbar-available-tab ${
                      tab === "ticket" ? "ticket-active" : "ticket"
                    }`}
                    role="button"
                    onClick={() => handleTabs("ticket")}
                  >
                    <div
                      className={`bait-instant-win-boxx-navbar-available-text ${
                        tab === "ticket" ? "active" : ""
                      }`}
                    >
                      TICKETS
                    </div>
                  </div>

                  <div
                    className={`bait-instant-win-boxx-navbar-available-tab ${
                      tab === "desc" ? "desc-active" : "desc"
                    }`}
                    role="button"
                    onClick={() => handleTabs("desc")}
                  >
                    <div
                      className={`bait-instant-win-boxx-navbar-available-text ${
                        tab === "desc" ? "active" : ""
                      }`}
                    >
                      DESCRIPTION
                    </div>
                  </div>
                </>
              ) : (
                ""
              )}
            </div>
          ) : (
            <div className="bait-instant-win-boxx-won-navbar">
              {item.show_description > 0 && item.prize_description ? (
                <>
                  <div
                    className={`bait-instant-win-boxx-navbar-won-tab ${
                      tab === "ticket" ? "ticket-active" : "ticket"
                    }`}
                    role="button"
                    onClick={() => handleTabs("ticket")}
                  >
                    <div
                      className={`bait-instant-win-boxx-navbar-won-text ${
                        tab === "ticket" ? "active" : ""
                      }`}
                    >
                      TICKETS
                    </div>
                  </div>
                  <div
                    className={`bait-instant-win-boxx-navbar-won-tab ${
                      tab === "desc" ? "desc-active" : "desc"
                    }`}
                    role="button"
                    onClick={() => handleTabs("desc")}
                  >
                    <div
                      className={`bait-instant-win-boxx-navbar-won-text ${
                        tab === "desc" ? "active" : ""
                      }`}
                    >
                      DESCRIPTION
                    </div>
                  </div>
                </>
              ) : (
                ""
              )}
            </div>
          ))}

        {isShowingMore &&
          (tab === "ticket" ? (
            <div className="bait-instant-win-boxx-bottom">
              {isShowingMore &&
                filterPrize.length > 0 &&
                filterPrize.slice(0, visibleTickets).map((ticket) => {
                  const user = ticket.user_id;
                  return (
                    <div
                      className={`bait-instant-win-boxx-bottom-${
                        !user ? "available" : "won"
                      }-ticket`}
                      key={ticket.ticket_number}
                    >
                      <div
                        className={`bait-instant-win-boxx-bottom-${
                          !user ? "available" : "won"
                        }-ticket-left`}
                      >
                        <div
                          className={`bait-instant-win-boxx-bottom-${
                            !user ? "available" : "won"
                          }-ticket-left-text`}
                        >
                          {ticket.ticket_number}
                        </div>
                        {user && (
                          <div className="bait-instant-win-boxx-bottom-won-ticket-bottom-text">
                            {" "}
                            {ticket && formatName(ticket.full_name)}
                          </div>
                        )}
                      </div>
                      <div
                        className={`bait-instant-win-boxx-bottom-${
                          !user ? "available" : "won"
                        }-ticket-right`}
                      >
                        <p
                          className={`bait-instant-win-boxx-bottom-${
                            !user ? "available" : "won"
                          }-ticket-right-text`}
                        >
                          {!user ? "AVAILABLE" : "WON"}
                        </p>
                      </div>
                    </div>
                  );
                })}
            </div>
          ) : (
            <div className="bait-instant-win-boxx-bottom-description">
              {pendingTickets.length > 0 ? (
                <div className="bait-instant-win-boxx-bottom-description-container">
                  {item.show_description > 0 && item.prize_description ? (
                    <div
                      dangerouslySetInnerHTML={{
                        __html: processHTML(item.prize_description),
                      }}
                    />
                  ) : (
                    "No Description"
                  )}
                </div>
              ) : (
                <div className="bait-instant-win-boxx-bottom-description-container">
                  {item.show_description > 0 && item.prize_description ? (
                    <div
                      dangerouslySetInnerHTML={{
                        __html: processHTML(item.prize_description),
                      }}
                    />
                  ) : (
                    "No Description"
                  )}
                </div>
              )}
            </div>
          ))}

        {isShowingMore &&
          tab === "ticket" &&
          visibleTickets < filterPrize.length && (
            <div className="bait-instant-win-boxx-bottom-load-more-container">
              <div
                className="bait-instant-win-boxx-bottom-load-more"
                role="button"
                onClick={handleLoadMore}
              >
                <div className="bait-instant-win-boxx-bottom-load-more-text">
                  LOAD MORE
                </div>
              </div>
            </div>
          )}
        {isShowingMore &&
          tab === "ticket" &&
          visibleTickets === filterPrize.length && (
            <div className="bait-instant-win-boxx-bottom-load-more-container">
              <div
                className="bait-instant-win-boxx-bottom-load-more"
                role="button"
                onClick={handleShowLess}
              >
                <div className="bait-instant-win-boxx-bottom-load-more-text">
                  SHOW LESS
                </div>
              </div>
            </div>
          )}
      </div>
    </div>
  );
};
