import React, { useState } from 'react';

interface FAQItem {
  id: string;
  question: string;
  answer: React.ReactNode;
}

const faqs: FAQItem[] = [
  {
    id: "winners",
    question: "CAN I SEE ALL THE INSTANT WINNERS BEFORE/AFTER A DRAW?",
    answer: (
      <div>
        Yes you can, the history of all winners who win instant wins tally up by [DAY] and can be found here:{' '}
        <span className="highlight">INSTANT WINNERS</span>
        <p className="update">
          UPDATE: 13/05/2023: For normal draws (Monday and Thursday) results can be found here:{' '}
          <span className="highlight">DRAW RESULTS</span>
        </p>
      </div>
    )
  },
  { id: "email", question: "I HAVE EMAILED/MESSAGED, WHEN WILL IT BE ANSWERED?", answer: "We aim to respond to all queries within 24 hours." },
  { id: "wait", question: "HOW LONG DO I HAVE TO WAIT FOR MY PRIZE?", answer: "Prize delivery typically takes 5-7 business days after winner confirmation." },
  { id: "sellout", question: "WHAT HAPPENS IF A COMPETITION SELLS OUT BEFORE THE DRAW DATE?", answer: "The draw will be conducted earlier than scheduled if a competition sells out." },
  { id: "instant", question: "WHAT HAPPENS IF I GET AN INSTANT WIN NUMBER?", answer: "If you receive an instant win number, you'll be notified immediately and can claim your prize." },
  { id: "unsold", question: "WHAT HAPPENS IF NOT ALL TICKETS SELL?", answer: "The draw will still take place with the sold tickets only." },
  { id: "winners-notification", question: "HOW WILL I KNOW IF I HAVE WON?", answer: "Winners will be notified via email and announced on our social media channels." },
  { id: "duration", question: "HOW LONG IS THE COMPETITION OPEN FOR?", answer: "Competition duration varies, but typically runs for 2-4 weeks unless sold out earlier." },
  { id: "prizes", question: "WHAT ARE THE PRIZES?", answer: "Prizes vary for each competition and are listed in the competition details." },
  { id: "data", question: "HOW DO YOU USE MY PERSONAL DATA?", answer: "Your personal data is processed in accordance with our privacy policy and data protection regulations." },
  { id: "eligibility", question: "CAN ANYONE ENTER THE COMPETITION?", answer: "Entry requirements and eligibility criteria are specified in each competition's terms and conditions." }
];

const FAQAccordion = () => {
  const [showAll, setShowAll] = useState(false);
  const [openItems, setOpenItems] = useState<{ [key: string]: boolean }>({});

  const toggleItem = (id: string) => {
    setOpenItems((prev) => ({
      ...prev,
      [id]: !prev[id],
    }));
  };

  const displayedFaqs = showAll ? faqs : faqs.slice(0, 8);

  return (


    <div>
      {/* Header Banner */}
      <div className="comp-banner">
        <div className="comp-banner-txt">
          <h2>FAQS</h2>
        </div>
      </div>

      {/* FAQ Accordion */}
      <div className="faq-container">
        <div className="accordion">
          {displayedFaqs.map((faq) => (
            <div key={faq.id} className="accordion-item">
              <h2 className="accordion-header">
                <button
                  className={`accordion-button ${openItems[faq.id] ? '' : 'collapsed'}`}
                  type="button"
                  onClick={() => toggleItem(faq.id)}
                  aria-expanded={openItems[faq.id]}
                >
                  {faq.question}
                </button>
              </h2>
              {openItems[faq.id] && (
                <div className="accordion-collapse show">
                  <div className="accordion-body">{faq.answer}</div>
                </div>
              )}
            </div>
          ))}
        </div>

        {/* Load More Button */}
        {faqs.length > 8 && !showAll && (
          <div className="load-more">
            <span
              onClick={() => setShowAll(true)}
            >
              LOAD MORE
            </span>
          </div>
        )}
      </div>
    </div>


  );
};

export default FAQAccordion;
