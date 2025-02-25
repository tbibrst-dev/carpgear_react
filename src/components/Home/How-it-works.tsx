import { useEffect, useState } from "react";
import { useGetSettingsQuery } from "../../redux/queries";

const HowItWorks = () => {
  const [workSteps, setWorkSteps] = useState<string[]>([]);
  const { data } = useGetSettingsQuery();
  const S3_BASE_URL = import.meta.env.VITE_STATIC_IMAGES_URL;


  useEffect(() => {
    if (data) {
      const newSteps = [];
      newSteps.push(data.data.work_step_1);
      newSteps.push(data.data.work_step_2);
      newSteps.push(data.data.work_step_3);
      setWorkSteps(newSteps);
    }
  }, [data]);

  return (
    <div className="container">
      <div className="Instant-winss-heading">
        <div className="Instant-winss-center">
          <h2>How it works</h2>
        </div>
      </div>

      <div className="how-work-space">
        <div className="row work-spac">
          <div className="col-lg-4 col-md-4 col-sm-12">
            <div className="select-your-prize">
              <div className="parent-prize">
                <div className="logo-prize">
                  <img src={`${S3_BASE_URL}/images/present-icon.svg`} alt="present image" />
                  <h4>Step 1 </h4>
                </div>
                <div
                  className="content-prize"

                >
                  <p >
                    <div dangerouslySetInnerHTML={{ __html: workSteps[0] }}>

                    </div>

                  </p>
                  {/* <h3>Select your prize &amp; entries</h3>
                        <p>
                          Choose from our list of current competitions which
                          prize you want to enter for.
                        </p> */}
                </div>
              </div>
            </div>
          </div>
          <div className="col-lg-4 col-md-4 col-sm-12">
            <div className="select-your-prize">
              <div className="parent-prize">
                <div className="logo-prize">
                  <img src={`${S3_BASE_URL}/images/Vector (2).svg`} alt="present image" />
                  <h4>Step 2</h4>
                </div>
                <div
                  className="content-prize"
                >

                  <p >
                    <div dangerouslySetInnerHTML={{ __html: workSteps[1] }}>

                    </div>

                  </p>
                  {/* <h3>Answer the question Correctly</h3>
                        <p>
                          Use your knowledge and skill to answer the qualifying
                          question in order to be entered into the draw to win!
                        </p> */}
                </div>
              </div>
            </div>
          </div>
          <div className="col-lg-4 col-md-4 col-sm-12">
            <div className="select-your-prize">
              <div className="parent-prize">
                <div className="logo-prize">
                  <img src={`${S3_BASE_URL}/images/Mic Mult 1.svg`} alt="present image" />
                  <h4>Step 3</h4>
                </div>
                <div
                  className="content-prize"
                >

                  <p >
                    <div dangerouslySetInnerHTML={{ __html: workSteps[2] }}>

                    </div>

                  </p>

                  {/* <h3>Wait for the live draw </h3>
                        <p>
                          Assuming you entered the answer correctly you will be
                          entered into the Facebook Live draw.
                        </p> */}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default HowItWorks;
