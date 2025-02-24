import Winners from "../../components/Winners/Winners";
import { Fragment , useEffect } from "react";
import { Helmet } from "react-helmet";


const WinnerPage = () => {

  useEffect(() => {
    const fetchMetaTags = async () => {
      try {
    
        const response = await fetch(`https://cggprelive.co.uk/wp-json/rankmath/v1/getHead?url=https://cggprelive.co.uk/winners_list`);
        
        if (!response.ok) {
          throw new Error(`Error: ${response.statusText}`);
        }

        
        console.log('rankmath',response);

       
      } catch (err) {
        console.log('rankmath',err)
      } finally {
        console.log('done');
      }
    };

    fetchMetaTags();
  }, []);

return (
    <Fragment>
      <Helmet>
        <title>Draw Results</title>
        <meta name="description" content="Draw Results" />
        <meta name="title" content="Draw Results" />
      </Helmet>     
      <Winners /> 
    </Fragment>
  );
};

export default WinnerPage;
