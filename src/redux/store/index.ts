import { configureStore } from "@reduxjs/toolkit";
import { competitionApi } from "../queries/";
import { reducer as competitionReducer } from "../slices";
import { reducer as cartReducer } from "../slices/cartSlice";
import { reducer as userReducer } from "../slices/userSlice";

const store = configureStore({
  reducer: {
    [competitionApi.reducerPath]: competitionApi.reducer,
    competition: competitionReducer,
    cart: cartReducer,
    userReducer,
    user:userReducer
  },
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware().concat(competitionApi.middleware),
});

//* Infer the `RootState` and `AppDispatch` types from the store itself
export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;

export default store;
