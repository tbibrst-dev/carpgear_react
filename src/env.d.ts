interface ImportMetaEnv {
  readonly VITE_SERVER_URL: string;
  readonly VITE_TOKEN: string;
  readonly VITE_REDIRECT_URL: string;
  readonly VITE_PRIVACY_POLICY_PAGE: number;
  readonly VITE_TERMS_PAGE: number;
  readonly VITE_ADD_TO_CART_API: string;
  readonly VITE_UPDATE_CART_API: string;
  readonly VITE_REMOVE_ITEM_API: string;
  readonly VITE_GET_CART: string;
  // more env variables...
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
