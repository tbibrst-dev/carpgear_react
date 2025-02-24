import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { CompetitionType } from "../../types";
import { competitionObj } from "../../utils";

interface InitialType {
  competition: CompetitionType;
  recommendComps: CompetitionType[];
  isLoading: boolean;
  detailsComp: { id: number; category: string };
}

const initialState: InitialType = {
  competition: competitionObj,
  recommendComps: [],
  isLoading: false,
  detailsComp: { id: 0, category: "" },
};

export const competitionsSlice = createSlice({
  name: "competitions",
  initialState,
  reducers: {
    setCurrentCompetition: (state, action: PayloadAction<CompetitionType>) => {
      state.competition = action.payload;
    },
    setRecommendedComps: (state, action: PayloadAction<CompetitionType[]>) => {
      state.recommendComps = [...action.payload];
    },
    setFetching: (state, action: PayloadAction<boolean>) => {
      state.isLoading = action.payload;
    },
    setDetailComp: (
      state,
      action: PayloadAction<{ id: number; category: string }>
    ) => {
      state.detailsComp = action.payload;
    },
  },
});

export const { actions, reducer } = competitionsSlice;
 
export const {
  setCurrentCompetition,
  setRecommendedComps,
  setFetching,
  setDetailComp,
} = actions;
