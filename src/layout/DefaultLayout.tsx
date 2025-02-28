import { Outlet } from "react-router-dom";
import Navbar from "../common/Navbar";
import Header from "../components/Home/Header";
import Footer from "../common/footer";
// import ChatComponent from "../components/Chat/chat";
// import { useState } from "react";

const DefaultLayout = () => {
  // const [isChatOpen, setIsChatOpen] = useState(false);

  // const handleChatToggle = (isOpen: boolean | ((prevState: boolean) => boolean)) => {
  //   setIsChatOpen(isOpen);
  // };

  return (
    <div>
      <div className="chatWrapperChatComponent">
        {/* <ChatComponent onToggleChat={handleChatToggle} /> */}
      </div>
      {/* <div className={isChatOpen ? "blur-background" : ""}> */}
      <div>
        <Header />
        <Navbar />
        <Outlet />
        <Footer />
      </div>
    </div>
  );
};

export default DefaultLayout;
