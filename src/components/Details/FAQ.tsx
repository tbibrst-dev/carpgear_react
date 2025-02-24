import { useState } from "react";
import { MAIN_COMPETITION_INFO } from "../../utils";

interface PropsType {
  rules: string;
  faq: string;
  howItWorks: string;
  // showHowItWorks: () => void;
  reward_prize_info: string;
  isEnableRewardWins: boolean;
  draw_date: string;
  total_sell_tickets: string;
  // activeIndex?: number;
  handleActiveTab: (tabNumber: number) => void;

}

const FAQ: React.FC<PropsType> = ({
  faq,
  rules,
  howItWorks,
  // showHowItWorks,
  reward_prize_info,
  isEnableRewardWins,
  // activeIndex,
  draw_date,
  total_sell_tickets,
  handleActiveTab

}) => {
  const [openIndices, setOpenIndices] = useState<number[]>([]);

  const toggleAccordion = (index: number) => {
    if (openIndices.includes(index)) {
      setOpenIndices(openIndices.filter(i => i !== index));
    } else {
      setOpenIndices([...openIndices, index]);
    }
  };

  const mainCompInfo = localStorage.getItem(MAIN_COMPETITION_INFO) ?? '';
  const formattedDrawDate = draw_date ? new Date(draw_date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }) : '';

  return (
    <div>
      <section className="bait-accordion">
        <div className="container">
          <div className="bait-accordion-head">
            <h2>Information</h2>
          </div>
          <div className="bait-accordion-all">
            <button
              className={`accordion ${openIndices.includes(1) ? "active" : ""}`}
              onClick={() => toggleAccordion(1)}
              id="how-it-work"
            >
              How it works
            </button>
            {openIndices.includes(1) && (
              <div className="panel" style={{ display: "block" }}>
                <p dangerouslySetInnerHTML={{ __html: (mainCompInfo) }}></p>

                <h3
                  style={{
                    color: "white",
                    fontWeight: "bold",
                    marginTop: "20px",
                  }}
                >
                  Instant Wins
                </h3>
                <p dangerouslySetInnerHTML={{ __html: (howItWorks) }}></p>
                {isEnableRewardWins && (
                  <>
                    <h3
                      style={{
                        color: "white",
                        fontWeight: "bold",
                        marginTop: "20px",
                      }}
                    >
                      Reward Wins
                    </h3>
                    <p
                      dangerouslySetInnerHTML={{ __html: (reward_prize_info) }}
                    ></p>
                  </>
                )}
              </div>
            )}
            <button
              className={`accordion ${openIndices.includes(2) ? "active" : ""}`}
              onClick={() => toggleAccordion(2)}
            >
              Rules
            </button>
            {openIndices.includes(2) && (
              <div className="panel" style={{ display: "block" }}>
                <p dangerouslySetInnerHTML={{ __html: (rules.replace(/\[draw_date\]/g, formattedDrawDate).replace(/\[total_sell_tickets\]/g, total_sell_tickets)) }}></p>
              </div>
            )}

            <button
              className={`accordion ${openIndices.includes(3) ? "active" : ""}`}
              onClick={() => toggleAccordion(3)}
            >
              FAQ’s
            </button>
            {openIndices.includes(3) && (
              <div className="panel" style={{ display: "block" }}>
                <p dangerouslySetInnerHTML={{ __html: (faq.replace(/\[draw_date\]/g, formattedDrawDate)) }}></p>
              </div>
            )}
          </div>
          <div className="mob-all-order">
            <div className="all-order-are">
              <p>
                All orders are subject to our <a href="/legal-terms?tab=2">Terms</a> &amp;{" "}
                <a href="/legal-terms?tab=3">Privacy.</a> For free postal entry route{" "}
                <a
                  href="#"
                  onClick={(e) => {
                    e.preventDefault();          // Prevents page reload
                    handleActiveTab(2);         // Switches to the second tab

                    // Smooth scroll to the target section
                    const targetElement = document.getElementById('clickTabOpenDiv');
                    if (targetElement) {
                      targetElement.scrollIntoView({ behavior: 'smooth' });
                    }
                  }}
                >
                  See here.
                </a>
              </p>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default FAQ;
