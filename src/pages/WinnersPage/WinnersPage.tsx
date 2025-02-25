import Winners from "../../components/Winners/Winners";
import { Fragment } from "react";
import { Helmet } from "react-helmet";


const WinnerPage = () => {

  

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
