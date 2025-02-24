import { useEffect, useState } from "react";
import { CompetitionType } from "../../../types";
import { isDrawToday, isDrawTomorrow } from "../../../utils";
import { useNavigate } from 'react-router-dom';

type PropsType = {
  tickets: CompetitionType;
};

const UpcomingTickets: React.FC<PropsType> = ({ tickets }) => {
  const [displayCount, setDisplayCount] = useState(0);
  const [isShowingMore, setIsShowingMore] = useState(false);

  const drawingToday = isDrawToday(tickets.draw_date);

  const drawingTomorrow = isDrawTomorrow(tickets.draw_date);

  function initialDisplayCount() {
    const numberOfFiveLengthTickets = tickets?.tickets?.filter(
      (item) => item?.toString().length === 5
    );
    if (numberOfFiveLengthTickets && numberOfFiveLengthTickets.length >= 6) {
      setDisplayCount(6);
    } else {
      setDisplayCount(7);
    }
  }

  useEffect(() => {
    initialDisplayCount();
  }, []);

  useEffect(() => {
    if (isShowingMore) {
      setDisplayCount(tickets?.tickets?.length!);
    } else {
      initialDisplayCount();
    }
  }, [isShowingMore]);

  const navigate = useNavigate();

  const handleClick = (prizeData: any) => {

    const cashAlt = prizeData?.cash_value > 0 ? 1 : 0;

    navigate(`/claim/prize?competition_name=${tickets?.title}&competition_type=${prizeData?.win_type}&prize_name=${prizeData?.prize}&prize_id=${prizeData?.win_id}&competition_id=${prizeData?.competition_id}&order=${tickets?.order_id}&ticket_number=${prizeData?.ticket_number}&user_id=${prizeData?.user_id}&cash_alt=${cashAlt}`);
  };


  // const handleClick = (prizeData: any) => {
  //   const payload = {
  //     competition_name: tickets?.title,
  //     competition_type: prizeData?.win_type,
  //     prize_name: prizeData?.prize,
  //     prize_id: prizeData?.win_id,
  //     competition_id: prizeData?.competition_id,
  //     order: tickets?.order_id,
  //     ticket_number: prizeData?.ticket_number,
  //     user_id: prizeData?.user_id,
  //   };

  //   const orderData = btoa(JSON.stringify(payload)); // Base64 encoding the prize data
  //   navigate(`/claim/prize?form-details=${orderData}`);
  // };



  return (
    <div className="upcoming-ticket-section-main">
      <div className="upcoming-ticket-section-one">
        <div className="upcoming-ticket-section-left">
          <h4>{tickets.title}</h4>
          <div className="single-mob-plus">
            <div
              className={`${drawingToday || drawingTomorrow
                ? "single-comp-upcoming"
                : "single-comp-upcoming-green"
                }`}
            >
              <div className="upcoming-clock">
                <svg
                  width={10}
                  height={10}
                  viewBox="0 0 10 10"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M5.5 4.5V2C5.5 1.86739 5.44732 1.74021 5.35355 1.64645C5.25979 1.55268 5.13261 1.5 5 1.5C4.86739 1.5 4.74021 1.55268 4.64645 1.64645C4.55268 1.74021 4.5 1.86739 4.5 2V5C4.5 5.13261 4.55268 5.25979 4.64645 5.35355C4.74021 5.44732 4.86739 5.5 5 5.5H7.5C7.63261 5.5 7.75979 5.44732 7.85355 5.35355C7.94732 5.25979 8 5.13261 8 5C8 4.86739 7.94732 4.74021 7.85355 4.64645C7.75979 4.55268 7.63261 4.5 7.5 4.5H5.5ZM5 10C2.2385 10 0 7.7615 0 5C0 2.2385 2.2385 0 5 0C7.7615 0 10 2.2385 10 5C10 7.7615 7.7615 10 5 10Z"
                    fill="#fff"
                  />
                </svg>
                <div
                  className={`${drawingToday || drawingTomorrow
                    ? "upcoming-txt"
                    : "upcoming-green-txt"
                    }`}
                >
                  <p>
                    {" "}
                    Draw:{" "}
                    {(() => {
                      const drawDate = new Date(tickets.draw_date);
                      const day = drawDate.getDate();
                      const suffix = (day: number) => {
                        if (day >= 11 && day <= 13) return "th";
                        switch (day % 10) {
                          case 1:
                            return "st";
                          case 2:
                            return "nd";
                          case 3:
                            return "rd";
                          default:
                            return "th";
                        }
                      };
                      const formattedDate =
                        drawDate.toLocaleDateString("en-GB", {
                          weekday: "short",
                        }) +
                        ` ${day}<sup>${suffix(day)}</sup> ` +
                        drawDate.toLocaleDateString("en-GB", {
                          month: "short",
                        });
                      return (
                        <span
                          dangerouslySetInnerHTML={{
                            __html: formattedDate,
                          }}
                        />
                      );
                    })()}
                    <span className="up-slash"> | </span>
                    {(() => {
                      const drawTime = tickets.draw_time
                        .split(":")
                        .slice(0, 2)
                        .join(":");
                      const drawHour = parseInt(drawTime.split(":")[0], 10);
                      const amOrPm = drawHour >= 12 ? "pm" : "am";
                      return drawTime + amOrPm;
                    })()}
                  </p>
                </div>
              </div>
            </div>
            <div
              className="mobil-plus-sho"
              onClick={() => setIsShowingMore(!isShowingMore)}
            >
              <button type="button" className="mobil-plus-sho">
                {!isShowingMore ? (
                  <svg
                    width={16}
                    height={16}
                    viewBox="0 0 16 16"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M8 8V1.77775M8 8V14.2222M8 8H14.2222M8 8H1.77775"
                      stroke="#FFBB41"
                      strokeWidth={2}
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    />
                  </svg>
                ) : (
                  <svg
                    width="16"
                    height="2"
                    viewBox="0 0 16 2"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M1.58301 1H14.4163"
                      stroke="#FFBB41"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    ></path>
                  </svg>
                )}
              </button>
            </div>
          </div>
          {isShowingMore && (
            <div className="ticket-for-mob">
              <div className="upcoming-ticket-section-right-ticket-number">
                {tickets.tickets
                  ?.map(Number) // Convert to numbers for accurate sorting
                  .sort((a, b) => a - b) // Sort in ascending order
                  .slice(0, displayCount) // Limit the number of displayed items
                  .map((item) => (
                    <div className="number-tickets number-tickets-new-design" key={item}>
                      <p>{item}</p>
                      <div className="number-tickets-new-design-end"></div>
                    </div>
                  ))}
              </div>
            </div>
          )}
        </div>
        <div className="upcoming-ticket-section-right">
          <div className="upcoming-ticket-section-right-head">
            {
              tickets.tickets && tickets.tickets.length > 0 ?
                <h4>Tickets</h4>
                : ""
            }
          </div>

          <div className="upcoming-ticket-section-right-ticket-number">
            {tickets.tickets
              ?.map(Number) // Convert to numbers for accurate sorting
              .sort((a, b) => a - b) // Sort in ascending order
              .slice(0, displayCount) // Limit the number of displayed items
              .map((item) => (
                <div className="number-tickets number-tickets-new-design" key={item}>
                  <p>{item}</p>
                  <div className="number-tickets-new-design-end"></div>
                </div>
              ))}
          </div>


        </div>
        <button
          className={`upcoming-tickets-toggle opacity-25 ${tickets.tickets?.length! <= displayCount && "opacity-25"
            }`}
          onClick={() => setIsShowingMore(!isShowingMore)}
          style={{
            opacity: `${tickets?.tickets?.length! < displayCount && 0.5}`,
          }}
          disabled={tickets.tickets?.length! < displayCount}
        >
          {!isShowingMore ? (
            <svg
              width={16}
              height={16}
              viewBox="0 0 16 16"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M8 8.00002V1.77777M8 8.00002V14.2223M8 8.00002H14.2222M8 8.00002H1.77775"
                stroke="#FFBB41"
                strokeWidth={2}
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </svg>
          ) : (
            <svg
              width={16}
              height={16}
              viewBox="0 0 16 16"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M1 8H15"
                stroke="#FFBB41"
                strokeWidth={2}
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </svg>
          )}
        </button>

      </div>

      <div className="main-dvi-prizes">
        {tickets.won && tickets.won.length > 0 && tickets.won?.map((item: any) => (
          <>
            <div className="content-div  content-div-desktop" key={item.ticket_id}>

              {
                item.prize_claim == 1 || item.prize_claim == 2 || item.prize_type == 'Points' || item.prize_type == 'Tickets' ?
                  <div className="image-title-content  image-title-content-black" style={{ background: "#333838", border: '1px solid #FFFFFF33' }}>

                    <div className="image-div">
                      <img src={item.prize_image} alt="" />
                    </div>
                    <div className="text-div">
                      <span>{item.edited_title ? item.edited_title : item.prize}</span>
                      {item.prize_claim == 1 || item.prize_claim == 2 || item.prize_type == 'Points' || item.prize_type == 'Tickets' ?
                        <span className="ticket_number_span" style={{ background: '#495050' }}>Ticket number: {item.ticket_number}</span>
                        :
                        <span className="ticket_number_span" style={{ background: '#eec273', color: 'black', fontWeight: '500' }}>Ticket number: {item.ticket_number}</span>}

                    </div>

                  </div>
                  :
                  <div className="image-title-content image-title-content-golden">

                    <div className="image-div">
                      <img src={item.prize_image} alt="" />
                    </div>
                    <div className="text-div">
                      <span>{item.edited_title ? item.edited_title : item.prize}</span>
                      {item.prize_claim == 1 || item.prize_claim == 2 || item.prize_type == 'Points' || item.prize_type == 'Tickets' ?
                        <span className="ticket_number_span" style={{ background: '#495050' }}>Ticket number: {item.ticket_number}</span>
                        :
                        <span className="ticket_number_span" style={{ background: '#eec273', color: 'black', fontWeight: '500' }}>Ticket number: {item.ticket_number}</span>}

                    </div>

                  </div>

              }


              <div className="button-content">
                {
                  item.prize_claim == 1 || item.prize_claim == 2 || item.prize_type == 'Points' || item.prize_type == 'Tickets' ?
                    <button className="claimed-button">CLAIMED</button>
                    :
                    <button className="claim-button" onClick={() => handleClick(item)} >CLAIM</button>
                }
              </div>
            </div>


            <div className="content-div  content-div-mobile" key={item.ticket_id}>

              {
                item.prize_claim == 1 || item.prize_claim == 2 || item.prize_type == 'Points' || item.prize_type == 'Tickets' ?
                  <div className="image-title-content image-title-content-black" style={{ background: "#333838", border: '1px solid #FFFFFF33' }}>

                    <div className="image-div-text-div">
                      <div className="image-div">
                        <img src={item.prize_image} alt="" />
                      </div>
                      <div className="text-div">
                        <span>{item.edited_title ? item.edited_title : item.prize}</span>
                        {item.prize_claim == 1 || item.prize_claim == 2 || item.prize_type == 'Points' || item.prize_type == 'Tickets' ?
                          <span className="ticket_number_span" style={{ background: '#495050' }}>Ticket number: {item.ticket_number}</span>
                          :
                          <span className="ticket_number_span" style={{ background: '#eec273', color: 'black', fontWeight: '500' }}>Ticket number: {item.ticket_number}</span>}

                      </div>
                    </div>
                    <div className="button-content">
                      {
                        item.prize_claim == 1 || item.prize_claim == 2 || item.prize_type == 'Points' || item.prize_type == 'Tickets' ?
                          <button className="claimed-button">CLAIMED</button>
                          :
                          <button className="claim-button" onClick={() => handleClick(item)} >CLAIM</button>
                      }
                    </div>

                  </div>
                  :
                  <div className="image-title-content image-title-content-golden">

                    <div className="image-div-text-div">
                      <div className="image-div">
                        <img src={item.prize_image} alt="" />
                      </div>
                      <div className="text-div">
                        <span>{item.edited_title ? item.edited_title : item.prize}</span>
                        {item.prize_claim == 1 || item.prize_claim == 2 ?
                          <span className="ticket_number_span" style={{ background: '#495050', fontWeight: '700' }}>Ticket number: {item.ticket_number}</span>
                          :
                          <span className="ticket_number_span" style={{ background: '#eec273', color: 'black', fontWeight: '700' }}>Ticket number: {item.ticket_number}</span>}

                      </div>
                    </div>

                    <div className="button-content">
                      {
                        item.prize_claim == 1 || item.prize_claim == 2 || item.prize_type == 'Points' ?
                          <button className="claimed-button">CLAIMED</button>
                          :
                          <button className="claim-button" onClick={() => handleClick(item)} >CLAIM</button>
                      }
                    </div>

                  </div>

              }



            </div>
          </>

        ))}
      </div>
    </div>
  );
};

export default UpcomingTickets;
