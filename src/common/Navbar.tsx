import { Link, useLocation, useNavigate } from "react-router-dom";
import BasketModal from "./BasketModal";
import CarouselModal from "./CarouselModal";

import { useSelector } from "react-redux";
import { RootState } from "../redux/store";
import { useEffect, useState } from "react";
import { CompetitionType } from "../types";

const links = [
  {
    name: "Home",
    path: "/",
  },
  // {
  //   name: "drawn next",
  //   path: "/competitions/drawn_next_competition",
  // },


  {
    name: "Comps",
    path: "/competitions/all",
  },

  {
    name: "INSTANTLY WIN",
    path: "/competitions/instant_win_comps",
  },
  // {
  //   name: "SINGULAR COMPS",
  //   path: "/competitions/singular_competition",
  // },


  {
    name: "results",
    path: "/results",
  },

  {
    name: "winners",
    path: "/winners_list",
  },
  {
    name: "contact",
    path: "/contact",
  },
];

const Navbar = () => {
  const { pathname } = useLocation();
  const navigate = useNavigate();
  const user = useSelector((state: RootState) => state.userReducer.user);
  const cartItems = useSelector((state: RootState) => state.cart.cartItems);
  const [cartQuantity, setCartQuantity] = useState<number>(0);
  const [isMenuOpen, setIsMenuOpen] = useState(false);


  const calculateCartQuantity = (items: CompetitionType[]) => {
    if (cartItems.length === 0) return 0;
    const totalQuantity = items.reduce(
      (acc, item) => parseInt(item.quantity) + acc,
      0
    );
    return totalQuantity;
  };

  useEffect(() => {
    const quantity = calculateCartQuantity(cartItems);
    setCartQuantity(quantity);
  }, [cartItems]);

  const navigateToAccount = () => {
    if (!user) navigate("/auth/login");
    else navigate("/account");
  };

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  // const closeMenu = () => {
  //   const navbarToggler = document.querySelector(
  //     "#navbarScroll"
  //   ) as HTMLDivElement;
  //   navbarToggler.classList.remove("show");
  // };

  // useEffect(() => {
  //   closeMenu();
  // }, [navigate]);

  const handleNavigation = (link: any) => {
    setIsMenuOpen(false);

    if (link == 1) {
      navigate('/competitions/all');

    } else if (link == 2) {
      navigate('/competitions/instant_win_comps');

    } else {
      navigate('/cart');

    }

  };

  return (
    <div>
      <section className="carp-header">
        <div className="container-fluid">
          <div className="car-header-all">
            <nav className="navbar navbar-expand-lg navbar-light ">
              <div className="container-fluid">
                <Link className="navbar-brand" to="/" >
                  <img src="/images/CGG-Logo-High-Res.png" alt="logo" />
                </Link>
                <div className="head-item">
                  <div className="mobile-show">
                    <button type="button" className="notification" onClick={navigateToAccount}>
                      <span className="cart-main-num">
                        <svg
                          width={24}
                          height={28}
                          viewBox="0 0 24 28"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M14.3333 16.2989H9.66665C7.19215 16.3017 4.81979 17.2859 3.07005 19.0356C1.32032 20.7854 0.336092 23.1577 0.333313 25.6322C0.333153 25.8796 0.411609 26.1206 0.557353 26.3204C0.703097 26.5203 0.908586 26.6686 1.14415 26.7441C4.68713 27.6607 8.34189 28.0719 12 27.9656C15.6581 28.0719 19.3128 27.6607 22.8558 26.7441C23.0914 26.6686 23.2969 26.5203 23.4426 26.3204C23.5884 26.1206 23.6668 25.8796 23.6666 25.6322C23.6639 23.1577 22.6796 20.7854 20.9299 19.0356C19.1802 17.2859 16.8078 16.3017 14.3333 16.2989Z"
                            fill="white"
                          />
                          <path
                            d="M12 13.9656C15.6166 13.9656 18.4166 9.91723 18.4166 6.43473C18.4166 4.73293 17.7406 3.10082 16.5372 1.89746C15.3339 0.694106 13.7018 0.0180664 12 0.0180664C10.2982 0.0180664 8.66607 0.694106 7.46271 1.89746C6.25935 3.10082 5.58331 4.73293 5.58331 6.43473C5.58331 9.91723 8.38331 13.9656 12 13.9656Z"
                            fill="white"
                          />
                        </svg>

                        {/* <div className="cart-number"><p>2</p>2</div> */}
                      </span>
                    </button>
                  </div>
                  <button
                    className="navbar-toggler"
                    type="button"
                    onClick={toggleMenu}
                  >
                    <span className="navbar-toggler-icon" />
                  </button>
                </div>
                {/* navbar slide code */}
                <div className="collapse navbar-collapse" id="navbarScroll">
                  <ul className="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll">
                    {links.map((link) => (
                      <li className={`${link.name === "Home" && "main-menu"} nav-item`} key={link.name}>
                        <Link to={link.path ? link.path : "#"} className={`nav-link ${pathname === link.path ? "active" : ""}`}>
                          {link.name}
                        </Link>
                      </li>
                    ))}
                  </ul>
                  <div className="header-shop-btns">
                    <button type="button" className="notification resp-hidden" onClick={navigateToAccount}>
                      <span className="cart-main-num">
                        <svg width={24} height={28} viewBox="0 0 24 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M14.3333 16.2989H9.66665C7.19215 16.3017 4.81979 17.2859 3.07005 19.0356C1.32032 20.7854 0.336092 23.1577 0.333313 25.6322C0.333153 25.8796 0.411609 26.1206 0.557353 26.3204C0.703097 26.5203 0.908586 26.6686 1.14415 26.7441C4.68713 27.6607 8.34189 28.0719 12 27.9656C15.6581 28.0719 19.3128 27.6607 22.8558 26.7441C23.0914 26.6686 23.2969 26.5203 23.4426 26.3204C23.5884 26.1206 23.6668 25.8796 23.6666 25.6322C23.6639 23.1577 22.6796 20.7854 20.9299 19.0356C19.1802 17.2859 16.8078 16.3017 14.3333 16.2989Z" fill="white" />
                          <path d="M12 13.9656C15.6166 13.9656 18.4166 9.91723 18.4166 6.43473C18.4166 4.73293 17.7406 3.10082 16.5372 1.89746C15.3339 0.694106 13.7018 0.0180664 12 0.0180664C10.2982 0.0180664 8.66607 0.694106 7.46271 1.89746C6.25935 3.10082 5.58331 4.73293 5.58331 6.43473C5.58331 9.91723 8.38331 13.9656 12 13.9656Z" fill="white" />
                        </svg>
                      </span>
                    </button>

                    <div className="top-basket-pop">
                      <button type="button" className="notification" onClick={() => navigate("/cart")}>
                        <span className="cart-items">
                          <svg width={28} height={28} viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M26.8336 18.6668H3.50025L5.25025 16.3335H22.1669C22.6697 16.3335 23.1142 16.0127 23.2729 15.5355L26.7729 5.0355C26.8931 4.67967 26.8324 4.28884 26.6131 3.98434C26.3937 3.67984 26.0414 3.50017 25.6669 3.50017H5.14991L1.99175 0.342003C1.53558 -0.114164 0.798246 -0.114164 0.342079 0.342003C-0.114087 0.79817 -0.114087 1.5355 0.342079 1.99167L3.50025 5.14984V14.7772L0.233579 19.1335C-0.0324205 19.487 -0.0744205 19.9595 0.122746 20.355C0.321079 20.7505 0.724746 21.0002 1.16691 21.0002H26.8336C27.4787 21.0002 28.0002 20.4775 28.0002 19.8335C28.0002 19.1895 27.4787 18.6668 26.8336 18.6668Z" fill="white" />
                            <path d="M4.66683 28.0001C5.95549 28.0001 7.00016 26.9554 7.00016 25.6668C7.00016 24.3781 5.95549 23.3334 4.66683 23.3334C3.37816 23.3334 2.3335 24.3781 2.3335 25.6668C2.3335 26.9554 3.37816 28.0001 4.66683 28.0001Z" fill="white" />
                            <path d="M23.3335 28.0001C24.6222 28.0001 25.6668 26.9554 25.6668 25.6668C25.6668 24.3781 24.6222 23.3334 23.3335 23.3334C22.0449 23.3334 21.0002 24.3781 21.0002 25.6668C21.0002 26.9554 22.0449 28.0001 23.3335 28.0001Z" fill="white" />
                          </svg>
                          <div className="cart-item-num">{cartQuantity}</div>
                        </span>
                      </button>
                    </div>

                  </div>
                </div>
                <div className="top-basket-pop">
                  <BasketModal />

                </div>
                <CarouselModal />
              </div>
            </nav>
            {isMenuOpen && (
              <div className="fullscreen-menu">
                <button className="close-btn" onClick={toggleMenu}>
                  &times;
                </button>
                <ul className="menu-list">
                  {links.map((link) => (
                    <li key={link.name}>
                      {/* <Link to={link.path ? link.path : "#"} className={`menu-link ${pathname === link.path ? "active" : ""}`} onClick={toggleMenu}> */}
                      <Link to={link.path ? link.path : "#"} className={`menu-link`} onClick={toggleMenu}>
                        {link.name}
                      </Link>
                    </li>
                  ))}
                </ul>
                <div className="copy-right">
                  <div className="container">
                    <div className="mobile-copt-txt">
                      <h2>Download OuR app</h2>
                    </div>
                    <div className="copy-right-all">
                      <div className="row copy-right-all-align">
                        <div className="col-lg-6">
                          <div className="copy-right-left">
                            <div className="copy-right-left-one">
                              <a
                                href="https://apps.apple.com/us/app/carp-gear-giveaways/id1513020494"
                                target="_blank"
                              >
                                <img src="/images/single-comp-top.svg" alt="" />{" "}
                              </a>
                              <a
                                href="https://play.google.com/store/apps/details?id=co.uk.carpgeargiveaways.app"
                                target="_blank"
                              >
                                <img src="/images/single-comp-top-1.svg" alt="" />
                              </a>
                            </div>
                          </div>
                        </div>
                        <div className="col-lg-6">
                          <div className="copy-right-right">
                            <a href="#" title="payment">
                              {" "}
                              <img
                                src="/images/Payment-IconsFooter3x.png"
                                alt="Payment-Icons"
                                width="211px"
                                height="26px"
                              />
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </section>

      <div className="bottom-nav">

        <div className="bottom-nav">
          <div className="bottom-nav-all">
            <div className="bottom-nav-responsive-three">
              <button type="button" className="bottom-nav-res" onClick={() => handleNavigation(1)} >
                <svg
                  width={33}
                  height={19}
                  viewBox="0 0 33 19"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M32.5 6.77472V3.13009C32.5 1.69059 31.2899 0.523682 29.7973 0.523682H3.20263C1.71006 0.523621 0.5 1.69053 0.5 3.13003V6.79901C1.87231 6.9891 2.92838 8.12418 2.92838 9.50016C2.92838 10.8761 1.87231 12.0113 0.5 12.2009V15.8704C0.5 17.3094 1.71006 18.4763 3.20263 18.4763H29.7973C31.2898 18.4763 32.4999 17.3094 32.4999 15.8704V12.2252C30.9983 12.1575 29.8016 10.9652 29.8016 9.50022C29.8017 8.03529 30.9984 6.84301 32.5 6.77472ZM8.09469 16.8319H7.16488V14.4934H8.09469V16.8319ZM8.09469 12.7234H7.16488V10.3849H8.09469V12.7234ZM8.09469 8.615H7.16488V6.27601H8.09469V8.615ZM8.09469 4.5066H7.16488V2.16809H8.09469V4.5066Z"
                    fill="white"
                  />
                </svg>
              </button>
            </div>
            <div className="bottom-nav-responsive-three">
              <button type="button" className="bottom-nav-res" onClick={() => handleNavigation(2)} >
                <svg
                  width={33}
                  height={33}
                  viewBox="0 0 33 33"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M5.83334 19.1667L19.1667 4.5V13.8333H27.1667L13.8333 28.5V19.1667H5.83334Z"
                    fill="white"
                    stroke="white"
                    strokeWidth={2}
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  />
                </svg>
              </button>
            </div>
            <div className="bottom-nav-responsive-three">
              <button
                type="button"
                className="bottom-nav-res"
                onClick={() => handleNavigation(3)}
              >
                <svg
                  width={29}
                  height={29}
                  viewBox="0 0 29 29"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M27.3336 19.1668H4.00025L5.75025 16.8335H22.6669C23.1697 16.8335 23.6142 16.5127 23.7729 16.0355L27.2729 5.5355C27.3931 5.17967 27.3324 4.78884 27.1131 4.48434C26.8937 4.17984 26.5414 4.00017 26.1669 4.00017H5.64991L2.49175 0.842003C2.03558 0.385836 1.29825 0.385836 0.84208 0.842003C0.385913 1.29817 0.385913 2.0355 0.84208 2.49167L4.00025 5.64984V15.2772L0.73358 19.6335C0.467579 19.987 0.425579 20.4595 0.622746 20.855C0.821079 21.2505 1.22475 21.5002 1.66691 21.5002H27.3336C27.9787 21.5002 28.5002 20.9775 28.5002 20.3335C28.5002 19.6895 27.9787 19.1668 27.3336 19.1668Z"
                    fill="white"
                  />
                  <path
                    d="M5.16683 28.5001C6.45549 28.5001 7.50016 27.4554 7.50016 26.1668C7.50016 24.8781 6.45549 23.8334 5.16683 23.8334C3.87816 23.8334 2.8335 24.8781 2.8335 26.1668C2.8335 27.4554 3.87816 28.5001 5.16683 28.5001Z"
                    fill="white"
                  />
                  <path
                    d="M23.8335 28.5001C25.1222 28.5001 26.1668 27.4554 26.1668 26.1668C26.1668 24.8781 25.1222 23.8334 23.8335 23.8334C22.5449 23.8334 21.5002 24.8781 21.5002 26.1668C21.5002 27.4554 22.5449 28.5001 23.8335 28.5001Z"
                    fill="white"
                  />
                </svg>

                {cartQuantity && cartQuantity > 0 ? <div className="cart-item-num">{cartQuantity} </div> : ""}

                {/* {pathname === "/checkout" && (
                  <span className="bottom-add-cart">14</span>
                )} */}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Navbar;

// {pathname === "/checkout" && (
//   <span className="bottom-add-cart">14</span>
// )}
