type PropsType = {
    ticket: any;
};
const WinnerList: React.FC<PropsType> = ({ ticket }) => {
    return (
        <>
            <div className="winner-list-header-div">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-calendar-fill"
                        viewBox="0 0 16 16">
                        <path
                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V5h16V4H0V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5" />
                    </svg>
                    {ticket.winning_date}
                </h3>

            </div>
            <ul className="winner-list-main-ul">
                {ticket.list.map((data: { competition_title: any; display_name: any; ticket_number: any; edit_title: any; prize_title: any; }) => (
                    <li className="winner-list-component-main-li" key={data.ticket_number}>
                        <div className="trophy_div">
                            <svg width="28" height="29" viewBox="0 0 28 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M27.9709 5.58276L27.9028 5.0092H24.4971C24.6674 3.86206 24.7695 2.68119 24.8376 1.46658C24.8717 0.825533 24.3608 0.285706 23.7819 0.285706H4.26689C3.65385 0.285706 3.17705 0.825533 3.21111 1.46658C3.24516 2.68119 3.38139 3.86206 3.55168 5.0092H0.145928L0.0437559 5.58276C0.00969838 5.71772 -0.364935 9.15912 1.88286 12.2631C3.34734 14.2875 5.62919 15.6708 8.5922 16.3793C9.54581 17.324 10.6016 18.0325 11.7255 18.4711C11.5893 19.8545 11.419 21.204 11.2487 22.4524H16.8001C16.5957 21.1703 16.4254 19.8545 16.2892 18.4711C17.4131 18.0663 18.4689 17.3577 19.4225 16.3793C22.3855 15.6708 24.6674 14.2875 26.1318 12.2631C28.3796 9.15912 27.9709 5.71772 27.9709 5.58276ZM2.87053 11.4196C1.44011 9.46277 1.26983 7.3372 1.26983 6.32503H3.75603C4.40312 9.53025 5.56108 12.3643 7.02555 14.4899C5.25456 13.8151 3.8582 12.7692 2.87053 11.4196ZM25.1442 11.4196C24.1565 12.7692 22.7601 13.8151 20.9891 14.4899C22.4877 12.3306 23.6116 9.53025 24.2587 6.32503H26.7449C26.7449 7.3372 26.5405 9.46277 25.1442 11.4196Z" fill="url(#paint0_radial_9115_1957)" />
                                <path d="M19.8023 28.2857H8.19769C7.85638 28.2857 7.58333 28.0012 7.58333 27.6455V23.0926C7.58333 22.7369 7.85638 22.4524 8.19769 22.4524H19.8023C20.1436 22.4524 20.4167 22.7369 20.4167 23.0926V27.6455C20.4167 28.0012 20.1436 28.2857 19.8023 28.2857Z" fill="#C19D72" />
                                <path d="M3.40568 28.2857H24.9817C25.4698 28.2857 25.8604 28.0965 25.8604 27.86V26.9614C25.8604 26.7249 25.4698 26.5357 24.9817 26.5357H3.40568C2.91754 26.5357 2.52702 26.7249 2.52702 26.9614V27.86C2.52702 28.0729 2.91754 28.2857 3.40568 28.2857Z" fill="#A88763" />
                                <path d="M18.2517 24.2024H10.1357C9.78064 24.2024 9.52702 24.0833 9.52702 23.9167V23.3214C9.52702 23.1548 9.78064 23.0357 10.1357 23.0357H18.2517C18.6067 23.0357 18.8604 23.1548 18.8604 23.3214V23.9167C18.8604 24.0833 18.556 24.2024 18.2517 24.2024Z" fill="#FAAE26" />
                                <defs>
                                    <radialGradient id="paint0_radial_9115_1957" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(8.5 14.6039) rotate(68.1002) scale(14.7459 16.0175)">
                                        <stop stop-color="#EEC273" />
                                        <stop offset="1" stop-color="#FFA400" />
                                    </radialGradient>
                                </defs>
                            </svg>
                        </div>
                        <div >
                            <p className="title"> {data.competition_title}</p>
                            {/* <p><span> <span className="display-name">{data.display_name}</span> Ticket  #{data.ticket_number}</span></p> */}
                            <p><span> <span className="display-name ">{data.edit_title && data.edit_title != null ? data.edit_title : data.prize_title}</span>-<span className="display-name user-name">{data.display_name}</span>  Ticket  #{data.ticket_number}</span></p>

                        </div>
                    </li>
                ))}</ul>
        </>
    )
}

export default WinnerList;
