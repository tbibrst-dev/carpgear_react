import ReactDOM from "react-dom/client";
import App from "./App.tsx";
import "./index.css";
import { BrowserRouter as Router } from "react-router-dom";
import { Provider } from "react-redux";
import store from "./redux/store/index.ts";
// import 'rsuite/dist/rsuite.min.css';
// import { CustomProvider } from 'rsuite';

ReactDOM.createRoot(document.getElementById("root")!).render(
  // <CustomProvider theme="dark">
    <Provider store={store}>
      <Router>
        <App />
      </Router>
    </Provider>
  // </CustomProvider>

);
