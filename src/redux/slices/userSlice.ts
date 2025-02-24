import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { User, PurchasedTickets, CompetitionType } from "../../types";

interface InitialStateUser {
  user: User | null;
  isAuthenticating: boolean;
  purchasedTickets: Array<PurchasedTickets>;
  upcomingTickets: Array<CompetitionType> | null;
  drawnTickets: Array<CompetitionType> | null;
  isLoading: boolean;
}

const initialState: InitialStateUser = {
  user: null,
  isAuthenticating: false,
  purchasedTickets: [],
  upcomingTickets: null,
  drawnTickets: null,
  isLoading: false,
};

export const userSlice = createSlice({
  name: "user",
  initialState,
  reducers: {
    setIsAuthenticating: (state, action: PayloadAction<boolean>) => {
      state.isAuthenticating = action.payload;
    },
    setUserState: (state, action: PayloadAction<User | null>) => {
      if (!action.payload) {
        state.user = null;
        return;
      }
      state.user = { ...action.payload };
    },
    clearUserState: (state) => {
      state.user = null;
    },
    setPurchasedTickets: (state, action: PayloadAction<PurchasedTickets[]>) => {
      state.purchasedTickets = action.payload;
    },
    setUpcomingTicketsUser: (
      state,
      action: PayloadAction<CompetitionType[]>
    ) => {
      state.upcomingTickets = action.payload;
    },
    setDrawnTickets: (state, action: PayloadAction<CompetitionType[]>) => {
      state.drawnTickets = action.payload;
    },
    setUserLoadingState: (state, action: PayloadAction<boolean>) => {
      state.isLoading = action.payload;
    },
  },
});

export const { actions, reducer } = userSlice;

export const {
  setUserState,
  clearUserState,
  setIsAuthenticating,
  setPurchasedTickets,
  setUpcomingTicketsUser,
  setDrawnTickets,
  setUserLoadingState,
} = actions;
