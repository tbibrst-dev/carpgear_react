import { useEffect, useState } from "react";
import axios from "axios";

// Define the interface for the review data
interface Review {
  id: string;
  comments: string;
  author: {
    name: string;
  };
  time_ago: string;
}

interface ReviewsResponse {
  reviews: Review[];
}

const Reviews = () => {
  const [reviews, setReviews] = useState<ReviewsResponse | null>(null);

  useEffect(() => {
    const fetchAllReviews = async () => {
      try {
        const response = await axios.get("?rest_route=/api/v1/get_review_all", {
          headers: { Authorization: `Bearer ${import.meta.env.VITE_TOKEN}` },
        });

        const data = response.data.body;
        // Check if the data is a string and parse it
        const parsedReviews = typeof data === "string" ? JSON.parse(data) : data;
        console.log("Parsed Reviews:", parsedReviews);
        setReviews(parsedReviews);
      } catch (error) {
        console.log("Error fetching reviews:", error);
      }
    };

    fetchAllReviews();
  }, []);

  return (
    <div>
      <div className="review">
        <div className="container">
          <div className="Instant-winss-heading">
            <div className="Instant-winss-center">
              <h2>Reviews</h2>
            </div>
          </div>

          <div className="review-ratings">
            <div className="price-rating-lefts">
              <div className="price-ratings-lefts-one">
                <img src="images/stars.svg" alt="star image" />
              </div>
              <div className="price-ratings-lefts-two">
                <p>
                  <span>4.9</span> Rating
                </p>
              </div>
              <div className="price-ratings-lefts-two">
                <img src="images/reviewsio-logo.svg" alt="Reviews.io logo" />
              </div>
            </div>
          </div>
          <div className="instant-view-all_vie">
            <a href="https://www.reviews.io/company-reviews/store/www.carpgeargiveaways.co.uk" target="_blank">
              View All
            </a>
          </div>
          <div className="review-parts-all">
            <div className="row">
              {reviews && reviews.reviews.length > 0 ? (
                reviews.reviews.map((review) => (
                  <div key={review.id} className="col-lg-4 col-md-4 col-sm-12 rev-spacing">
                    <div className="review-child">
                      <svg
                        width="109"
                        height="18"
                        viewBox="0 0 109 18"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                      >
                        <path d="M10.1361 14.9258L4.38778 18L5.48566 11.4878L0.833374 6.87599L7.26054 5.92578L10.1361 0L13.0107 5.92578L19.4379 6.87599L14.7865 11.4878L15.8853 18L10.1361 14.9258Z" fill="#EEC273" />
                        <path d="M32.485 14.9258L26.7366 18L27.8345 11.4878L23.1822 6.87599L29.6094 5.92578L32.485 0L35.3596 5.92578L41.7868 6.87599L37.1354 11.4878L38.2342 18L32.485 14.9258Z" fill="#EEC273" />
                        <path d="M54.8338 14.9258L49.0855 18L50.1834 11.4878L45.5311 6.87599L51.9583 5.92578L54.8338 0L57.7085 5.92578L64.1356 6.87599L59.4843 11.4878L60.5831 18L54.8338 14.9258Z" fill="#EEC273" />
                        <path d="M77.1827 14.9258L71.4344 18L72.5323 11.4878L67.88 6.87599L74.3071 5.92578L77.1827 0L80.0573 5.92578L86.4845 6.87599L81.8331 11.4878L82.932 18L77.1827 14.9258Z" fill="#EEC273" />
                        <path d="M99.5316 14.9258L93.7833 18L94.8812 11.4878L90.2289 6.87601L96.656 5.9258L99.5316 0L102.406 5.9258L108.833 6.87601L104.182 11.4878L105.281 18L99.5316 14.9258Z" fill="#EEC273" />
                      </svg>

                      <h4 className="review-comment">{review.comments || "No title provided"}</h4>
                      <div className="luckbeany">
                        <span>{review.author.name}</span> <span>{review.time_ago}</span>
                      </div>
                    </div>
                  </div>
                ))
              ) : (
                <p>No reviews available.</p>
              )}
            </div>
          </div>
          <div className="instant-view-all-mob">
            <a href="https://www.reviews.io/company-reviews/store/www.carpgeargiveaways.co.uk" target="_blank">
              View All
            </a>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Reviews;
