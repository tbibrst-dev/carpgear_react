const Rating = () => {
  const S3_BASE_URL = import.meta.env.VITE_STATIC_IMAGES_URL;

  return (
    <div>
      <div className="single-compi-top-rating">
        <div className="container">
          <div className="single-compi-top-rating-all">
            <div className="single-compi-top-rating-left">
              <div className="single-compi-top-rating-left-one">
                <img src={`${S3_BASE_URL}/images/stars.svg`} alt="" />
              </div>
              <div className="single-compi-top-rating-left-two">
                <p>
                  <span> 4.9</span> Rating
                </p>
              </div>
              <div className="single-compi-top-rating-left-three">
                <a href="#">
                  {" "}
                  <img src={`${S3_BASE_URL}/images/reviewsio-logo.svg`} alt="logo" />{" "}
                </a>
              </div>
            </div>
            <div className="single-compi-top-rating-right">
              <div className="single-compi-top-rating-right-one">
                <a
                  href="https://apps.apple.com/us/app/carp-gear-giveaways/id1513020494"
                  target="_blank"
                >
                  {" "}
                  <img src={`${S3_BASE_URL}/images/single-comp-top.svg`} alt="" />
                </a>
              </div>
              <div className="single-compi-top-rating-right-one">
                <a
                  href="https://play.google.com/store/apps/details?id=co.uk.carpgeargiveaways.app"
                  target="_blank"
                >
                  {" "}
                  <img src={`${S3_BASE_URL}/images/single-comp-top-1.svg`} alt="" />{" "}
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Rating;
