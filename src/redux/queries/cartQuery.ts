import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import { NonceApiResponse } from "../../types";

const baseQuery = fetchBaseQuery({ baseUrl: import.meta.env.VITE_SERVER_URL });

export const cartApi = createApi({
  reducerPath: "cartApi",
  baseQuery,
  endpoints: (builder) => ({
    getNonceValue: builder.query<string, NonceApiResponse>({
      query: () => ({
        url: "?rest_route=/api/v1/get-nonce",
        headers: {
          Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`,
        },
      }),
    }),
  }),
});

export const { useGetNonceValueQuery } = cartApi;
