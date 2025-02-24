import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { CompetitionType } from "../../types";

interface CartState {
  cartItems: CompetitionType[];
  isAdding: boolean;
  isUpdating: boolean;
  isDeleting: boolean;
}

const initialState: CartState = {
  cartItems: [],
  isAdding: false,
  isUpdating: false,
  isDeleting: false,
};

export const cartSlice = createSlice({
  name: "cart",
  initialState,
  reducers: {
    isAddingToCart: (state, action: PayloadAction<boolean>) => {
      state.isAdding = action.payload;
    },
    addToCart: (state, action: PayloadAction<CompetitionType[]>) => {
      // state.cartItems.push(action.payload);
      state.cartItems = action.payload;
    },
    setIsUpdating: (state, action: PayloadAction<boolean>) => {
      state.isUpdating = action.payload;
    },
    updateCartQty: (
      state,
      action: PayloadAction<{ id: number; qty: number; totals: any }>
    ) => {
      const { id, qty, totals } = action.payload;

      
      const { cartItems } = state;

      const itemIndex = cartItems.findIndex((item) => item.id === id);

      if (itemIndex !== -1) {
        const updatedItem = {
          ...cartItems[itemIndex],
          quantity: qty.toString(),
          totals: {
            ...cartItems[itemIndex].totals,
            ...totals,
          }
        };
        const updatedCartItems = [...cartItems];
        updatedCartItems.splice(itemIndex, 1, updatedItem);
        state.cartItems = updatedCartItems;
      }
    },
    setIsDeleting: (state, action: PayloadAction<boolean>) => {
      state.isDeleting = action.payload;
    },
    removeItemFromCart: (state, action: PayloadAction<number>) => {
      const { cartItems } = state;
      const index = cartItems.findIndex((i) => i.id === action.payload);
      cartItems.splice(index, 1);
      state.cartItems = [...cartItems];
    },
    emptyCart: (state) => {

      state.cartItems = [];
      
    },
  },
});

export const { actions, reducer } = cartSlice;

export const {
  addToCart,
  updateCartQty,
  isAddingToCart,
  setIsUpdating,
  removeItemFromCart,
  setIsDeleting,
  emptyCart,
} = actions;
