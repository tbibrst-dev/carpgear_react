import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import { SettingsType } from "../../types";

const baseQuery = fetchBaseQuery({
  baseUrl: `${import.meta.env.VITE_SERVER_URL}`,
});

interface CompetitionBody {
  limit: number;
  category?: string;
  order_by: string;
  order: string;
  token: string;
  page?: number;
  status?: string;
  endPoint?: string;
  id?: number;
}

const createCompetitionEndpoint = (
  url: string,
  method: string,
  getToken: (data: CompetitionBody) => string,
  page?: number
) => ({
  query: (data: CompetitionBody) => ({
    url,
    method,
    body: data,
    headers: {
      Authorization: `Bearer ${getToken(data)}`,
    },
    page,
  }),
});

const token = import.meta.env.VITE_TOKEN;

export const competitionApi = createApi({
  reducerPath: "competitionApi",
  baseQuery,
  endpoints: (builder) => ({
    getCompetition: builder.mutation<string, CompetitionBody>(
      createCompetitionEndpoint(
        "/?rest_route=/api/v1/competition",
        "post",
        (data) => data.token
      )
    ),

    getDrawNext: builder.mutation<string, CompetitionBody>(
      createCompetitionEndpoint(
        "/?rest_route=/api/v1/drawn_next_competition",
        "post",
        (data) => data.token
      )
    ),

    getFinishedSoldOut: builder.mutation<string, CompetitionBody>(
      createCompetitionEndpoint(
        "/?rest_route=/api/v1/finished_soldout_competition",
        "post",
        (data) => data.token
      )
    ),
    getFeaturedComps: builder.mutation<string, CompetitionBody>(
      createCompetitionEndpoint(
        "/?rest_route=/api/v1/featured_competition",
        "post",
        (data) => data.token
      )
    ),
    getSingleCompetition: builder.mutation<
      string,
      { id: number; token: string }
    >({
      query: (data) => ({
        url: "/?rest_route=/api/v1/getcompetition",
        method: "post",
        body: data,
        headers: {
          Authorization: `Bearer ${data.token}`,
        },
      }),
    }),
    getSettings: builder.query<{ data: SettingsType; success: boolean }, void>({
      query: () => ({
        url: "?rest_route=/api/v1/getsettings",
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }),
    }),
    getAllCompetitions: builder.mutation<string, CompetitionBody>(
      createCompetitionEndpoint(
        "/?rest_route=/api/v1/featured_competition",
        "post",
        (data) => data.token
      )
    ),
    getRecommendedComps: builder.mutation<string, CompetitionBody>(
      createCompetitionEndpoint(
        "?rest_route=/api/v1/getOtherComps",
        "post",
        (data) => data.token
      )
    ),
  }),
});

export const {
  useGetCompetitionMutation,
  useGetDrawNextMutation,
  useGetFinishedSoldOutMutation,
  useGetFeaturedCompsMutation,
  useGetSingleCompetitionMutation,
  useGetSettingsQuery,
  useGetAllCompetitionsMutation,
  useGetRecommendedCompsMutation,
} = competitionApi;
