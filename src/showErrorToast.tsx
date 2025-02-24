// toastManager.js
import { ReactElement, JSXElementConstructor, ReactNode } from "react";
import { toast } from "react-hot-toast";

let errorToastId: string | null = null; // To track the currently active toast

export const showErrorToast = (message: string | number | boolean | ReactElement<any, string | JSXElementConstructor<any>> | Iterable<ReactNode> | null | undefined) => {
  if (!errorToastId) {
    errorToastId = toast((t) => (
      <span>
        {message}
        <button onClick={() => toast.dismiss(t.id)} style={{ marginLeft: '10px', backgroundColor: '#fe5042', color: '#fe5042', border: 'none', cursor: 'pointer' }}>
          Dismiss
        </button>
      </span>
    ), {
      duration: 4000,  // Customize the duration
      style: { backgroundColor: '#fe5042', color: '#fff' }
    });

    // Reset errorToastId after the duration
    setTimeout(() => {
      errorToastId = null;
    }, 4000); // Match the toast duration
  }
};
