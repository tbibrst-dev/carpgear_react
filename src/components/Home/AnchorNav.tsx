import { isMobile } from 'react-device-detect';
import { SetStateAction, useState, useEffect } from 'react';
import { Link } from 'react-scroll';

const AnchorNav = () => {

  const [activeTab, setActiveTab] = useState('draw-next');
  const [screenWidth, setScreenWidth] = useState(window.innerWidth);

  useEffect(() => {
    const handleResize = () => {
      setScreenWidth(window.innerWidth);
    };

    window.addEventListener('resize', handleResize);

    // Clean up the event listener on component unmount
    return () => {
      window.removeEventListener('resize', handleResize);
    };
  }, []);




  const handleTabClick = (tabName: SetStateAction<string>) => {
    setActiveTab(tabName);

  };



  // Check if the device is mobile
  if (screenWidth >= 576 || !isMobile) {
    return (

      <div className="anchor-nav anchor-tab-main-div">
        <div>
          <div className="tab-nav homepage-tab-links">
            <Link
              to="draw-next-section-scroll"
              className={`tab-link ${activeTab === 'draw-next' ? 'active' : ''}`}
              onClick={() => handleTabClick('draw-next')}
              style={{ borderRight: '1px solid #FFFFFF1A' }}
            // smooth={true} 
            // duration={500} 
            >
              Drawn Next
            </Link>
            <Link
              to="instant-win-section-scroll"
              className={`tab-link ${activeTab === 'instant-wins' ? 'active' : ''}`}
              onClick={() => handleTabClick('instant-wins')}
              style={{ borderRight: '1px solid #FFFFFF1A' }}
            // smooth={true}
            // duration={500} 

            >
              Instantly Win
            </Link>

            <Link
              to="comps-for-all-section-scroll"
              className={`tab-link ${activeTab === 'comps-for-everyone' ? 'active' : ''}`}
              onClick={() => handleTabClick('comps-for-everyone')}
              style={{ borderRight: '1px solid #FFFFFF1A' }}
            // smooth={true}
            // duration={500} 
            >
              Comps for All
            </Link>

            <Link
              to="finished-section-scroll"
              className={`tab-link ${activeTab === 'finished' ? 'active' : ''}`}
              onClick={() => handleTabClick('finished')}
            // smooth={true}
            // duration={500} 

            >
              Finished
            </Link>

          </div>
        </div>

      </div>

    );
  } else {
    return (
      <div className="anchor-nav anchor-tab-main-div">
        <div className='mobile-tab-anchor'>
          <div className="tab-nav homepage-tab-links">
            <Link
              to="draw-next-section-scroll"
              className={`tab-link ${activeTab === 'draw-next' ? 'active' : ''}`}
              onClick={() => handleTabClick('draw-next')}
              style={{ borderRight: '1px solid #FFFFFF1A' }}
            // smooth={true} 
            // duration={500} 
            >
              Drawn Next
            </Link>
            <Link
              to="instant-win-section-scroll"
              className={`tab-link ${activeTab === 'instant-wins' ? 'active' : ''}`}
              onClick={() => handleTabClick('instant-wins')}
              style={{ borderRight: '1px solid #FFFFFF1A' }}
            // smooth={true}
            // duration={500} 

            >
              Instantly Win
            </Link>
          </div>

          <div className="tab-nav homepage-tab-links">

            <Link
              to="comps-for-all-section-scroll"
              className={`tab-link ${activeTab === 'comps-for-everyone' ? 'active' : ''}`}
              onClick={() => handleTabClick('comps-for-everyone')}
              style={{ borderRight: '1px solid #FFFFFF1A' }}
            // smooth={true}
            // duration={500} 
            >
              Comps for All
            </Link>

            <Link
              to="finished-section-scroll"
              className={`tab-link ${activeTab === 'finished' ? 'active' : ''}`}
              onClick={() => handleTabClick('finished')}
            // smooth={true}
            // duration={500} 

            >
              Finished
            </Link>
          </div>
        </div>

      </div>
    )
  }
};

export default AnchorNav;
