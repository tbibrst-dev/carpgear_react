import { useEffect } from 'react';

interface InjectFrontendScriptsProps {
  scriptContent: string;
}

const InjectFrontendScripts: React.FC<InjectFrontendScriptsProps> = ({ scriptContent }) => {
  useEffect(() => {
    // Create a temporary div element to parse the script content
    const div = document.createElement('div');
    div.innerHTML = scriptContent;

    // Extract all script tags from the content
    const scripts = div.getElementsByTagName('script');

    const scriptElements: HTMLScriptElement[] = [];

    // Loop over each script element to inject into the DOM
    for (let i = 0; i < scripts.length; i++) {
      const script = scripts[i];
      const newScript = document.createElement('script');
      
      // Set async attribute if present
      if (script.async) {
        newScript.async = true;
      }

      // Check if the script has a src attribute (external script)
      if (script.src) {
        newScript.src = script.src;
      } else {
        // Inline script
        newScript.innerHTML = script.innerHTML;
      }

      // Append the script to the document head
      document.head.appendChild(newScript);
      scriptElements.push(newScript);
    }

    // Cleanup function to remove injected scripts when component unmounts
    return () => {
      scriptElements.forEach(script => {
        document.head.removeChild(script);
      });
    };
  }, [scriptContent]);

  return <div dangerouslySetInnerHTML={{ __html: scriptContent }} />; // Renders raw HTML
};

export default InjectFrontendScripts;
