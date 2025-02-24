
import ChatComponent from "../../components/Chat/fullpageChat";
import { useEffect } from "react";


function Cometchat() {

    useEffect(() => {
        const fetchMetaTags = async () => {
          try {
        
            const response = await fetch(`https://cggprelive.co.uk/wp-json/rankmath/v1/getHead?url=https://cggprelive.co.uk/community-chat`);
            
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
        <div>
            <ChatComponent />
        </div>
    );
};







export default Cometchat;
