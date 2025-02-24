import { PointLogs } from "../../types";

type Props = {
  points: number | string;
  pointsLogs: PointLogs[];
};

const PointsUI: React.FC<Props> = ({ pointsLogs, points }) => {
  return (

    pointsLogs && pointsLogs.length > 0 ?
      <div className="user-points-section ">
        <div className="user-points-section-header">
          <h1>you have</h1>
          <div>
            <h1>{points}</h1>
          </div>
          <h1>points</h1>
        </div>

        <div className="user-points-section-details order-section-right-side">
          <div className="user-points-section-details-table-head">
            <div className="points-table-heading">Event</div>
            <div className="points-table-heading">date</div>
            <div className="points-table-heading-2">points</div>
          </div>
          {pointsLogs.map((log) => (
            <div className="user-points-section-details-table-row">
              <div className="points-table-row">{log.description}</div>
              <div className="points-table-row">{log.date_display_human}</div>
              <div className="points-table-row-2 heading-details-points">
                {log.type === "order-redeem" ? log.points : `+${log.points}`}
              </div>
            </div>
          ))}
        </div>

        {/* mobile view */}
        {pointsLogs.map((log) => (
          <div className="order-section-mobile-view mb-3">
            <div className="single-order-section-mobile-container">
              <div className="point-section-child-div">
                <div >
                  <span className="heading">EVENT</span>
                </div>
                <div className="heading-details-div">
                  <span className="heading-details">{log.description}</span>
                </div>


              </div>
              <div className="point-section-child-div">
                <span className="heading">DATE</span>
                <span className="heading-details">{log.date_display_human}</span>
              </div>
              <div className="point-section-child-div">
                <span className="heading">POINTS</span>
                <span className="heading-details heading-details-points">{log.type === "order-redeem" ? log.points : `+${log.points}`}</span>
              </div>
            </div>
          </div>
        ))}
      </div>
      :
      <div className="user-points-section-empty-section row">
        <div className="user-points-section-details-empty-section">
          <span className="empty-page-message">
          YOUR POINTS BALANCE IS 0 UNTIL YOU PLACE AN ORDER
          </span>
         
        </div>
      </div>
  );
};

export default PointsUI;
