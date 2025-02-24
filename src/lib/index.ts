// const handleAddToCart = async (competition: CompetitionType) => {
//   // console.log("call");
//   const isItem = cartItems.find(
//     (item) =>
//       item.competition_product_id === competition.competition_product_id
//   );
//   if (isItem) {
//     return;
//   }
//   dispatch(isAddingToCart(true));
//   const quantity = quantities[competition.id];
//   let nonceVal = localStorage.getItem(NONCE_KEY);
//   if (!nonceVal) {
//     const res: any = await fetchNonceValue();
//     nonceVal = res.nonce;
//     localStorage.setItem(NONCE_KEY, res.nonce);
//   }

//   const cart_header = localStorage.getItem(CART_HEADER) || undefined;

//   try {
//     const response = await axios.post(
//       "?rest_route=/api/v1/addItem",
//       {
//         id: competition.competition_product_id,
//         quantity,
//         nonce: nonceVal,
//         cart_header,
//       },
//       {
//         headers: {
//           Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`,
//         },
//       }
//     );
//     // console.log(response);
//     if (response.status === 200) {
//       const cart_header = response.data.cart_header;
//       const items = response.data.items.map((item: any) => {
//         const competition = item.competition;
//         competition.quantity = item.quantity.toString();
//         const competitionWithKey = { ...competition, key: item.key };
//         return competitionWithKey;
//       });

//       items.forEach((item: any) => {
//         const newKeys = {
//           ...cartKeys,
//           [item.id as number]: { key: item.key },
//         };
//         localStorage.setItem(UPDATE_CART_KEY, JSON.stringify(newKeys));
//       });
//       if (cart_header) {
//         localStorage.setItem(CART_HEADER, cart_header);
//       }
//       // const newKeys = {
//       //   ...cartKeys,
//       //   [item.competition.id as number]: { key: item.key },
//       // };
//       // localStorage.setItem(UPDATE_CART_KEY, JSON.stringify(newKeys));

//       // const competition: CompetitionType = item.competition;
//       // competition.quantity = item.quantity.toString();
//       dispatch(addToCart(items));
//     }
//   } catch (error) {
//     console.log(error);
//   } finally {
//     dispatch(isAddingToCart(false));
//   }
// };

// const fetchUpsellsCompetition = async () => {
//   try {
//     const res: any = await mutate({
//       limit: 5,
//       order_by: "id",
//       order: "desc",
//       token: import.meta.env.VITE_TOKEN,
//       endPoint: "drawn_next_competition",
//     });

//     if (!res.error) {
//       setCompetitions(res.data.data);
//       handleQuantitySetter(res.data.data);
//     }
//   } catch (error) {
//     console.log(error);
//   }
// };

// //todo handle quantity change of a competition
// const handleQuantityChange = (
//   id: number,
//   newQuantity: number,
//   action: "increment" | "decrement"
// ) => {
//   const competition = competitions.find((comp) => comp.id === id);
//   if (
//     Number(competition?.max_ticket_per_user) === quantities[id] &&
//     action === "increment"
//   ) {
//     toast.error(cartError());
//     return;
//   }
//   setQuantities((prevQuantities) => ({
//     ...prevQuantities,
//     [id]: Math.max(
//       0,
//       action === "increment"
//         ? prevQuantities[id] + newQuantity
//         : prevQuantities[id] - newQuantity
//     ),
//   }));
// };

// const navigateCompetition = (
//   id: number,
//   category: string,
//   competitionName: string
// ) => {
//   dispatch(setDetailComp({ id, category }));
//   const splitName = competitionName.split(" ");
//   const editedName = splitName.join("-");
//   const competitionsToBeFetched = JSON.parse(
//     localStorage.getItem(COMPETITIONS_TO_BE_FETCHED) as string
//   );

//   const newCompetitionToAddToLocalStorage = { id: id, category };

//   const newCompsToFetched = {
//     ...competitionsToBeFetched,
//     ...newCompetitionToAddToLocalStorage,
//   };
//   localStorage.setItem(
//     COMPETITIONS_TO_BE_FETCHED,
//     JSON.stringify(newCompsToFetched)
//   );

//   localStorage.setItem(
//     FETCHED_COMPETITION,
//     JSON.stringify(newCompetitionToAddToLocalStorage)
//   );

//   navigate(`/competition/details/${editedName}`);
// };
// const handleQuantityChangeInput = (id: number, value: number) => {
//   let parsedValue: number;

//   if (isNaN(value)) return;
//   //* check if user input is not more than max ticket per user
//   const competition = competitions.find(
//     (item) => item.id === id
//   ) as CompetitionType;
//   if (value > parseInt(competition.max_ticket_per_user)) {
//     setQuantities((prevQuantities) => ({
//       ...prevQuantities,
//       [id]: parseInt(competition.max_ticket_per_user),
//     }));
//     return;
//   }

//   if (!value) {
//     parsedValue = 0;
//   } else {
//     parsedValue = value;
//   }
//   setQuantities((prevQuantities) => ({
//     ...prevQuantities,
//     [id]: parsedValue,
//   }));
// };

// export const handleAddToCartDummy = async (
//     competition: CompetitionType,
//     cartItems: CompetitionType[],
//     dispatch: Dispatch,
//     quantities: QunatityType,
//     cartKeys: { [key: number]: { key: string } },
//     purchasedTickets: PurchasedTickets[]
//   ) => {
//     if (quantities) {
//     }
//     const isItem = cartItems.find(
//       (item) => item.competition_product_id === competition.competition_product_id
//     );
//     if (isItem) {
//       return;
//     }
//     dispatch(isAddingToCart(true));
//     const quantity = quantities[competition.id];
//     let nonceVal = localStorage.getItem(NONCE_KEY);
//     const storedTimestamp = localStorage.getItem(NONCE_TIMESTAMP) as string;

//     const purchasedTicketsCompetition = purchasedTickets?.find(
//       (item) => parseInt(item.competition_id) === competition.id
//     ) as PurchasedTickets;

//     const boughtTickets = parseInt(purchasedTicketsCompetition?.total_tickets);

//     const ticketsQtyLeft =
//       parseInt(competition.max_ticket_per_user) - boughtTickets;

//     if (ticketsQtyLeft < 0) {
//       toast.error(
//         "Oops! It seems you,ve purchased the maximum tickets for this competition"
//       );
//       return;
//     }

//     //* chekc if nonce exist if not refetch the nonce if nonce exists check timestamp
//     if (!nonceVal || !storedTimestamp) {
//       const res: any = await fetchNonceValue();
//       nonceVal = res.nonce;
//       const timestamp = Date.now();
//       localStorage.setItem(NONCE_KEY, res.nonce);
//       localStorage.setItem(NONCE_TIMESTAMP, timestamp.toString());
//     } else {
//       const timestampDiff = Date.now() - parseInt(storedTimestamp, 10);
//       if (timestampDiff > 11 * 60 * 60 * 1000) {
//         const res: any = await fetchNonceValue();
//         nonceVal = res.nonce;
//         const timestamp = Date.now();
//         localStorage.setItem(NONCE_KEY, res.nonce);
//         localStorage.setItem(NONCE_TIMESTAMP, timestamp.toString());
//       }
//     }
//     const cart_header = localStorage.getItem(CART_HEADER) || undefined;
//     try {
//       const response = await axios.post(
//         "?rest_route=/wc/store/v1/cart/add-item",
//         {
//           id: competition.competition_product_id,
//           quantity,
//           nonce: nonceVal,
//           cart_header,
//         },

//         {
//           headers: {
//             "X-WC-Store-api-nonce": nonceVal,
//             Authorization: `Bearer ${import.meta.env.VITE_TOKEN}`,
//           },
//         }
//       );
//       console.log(response);
//       if (response.status === 200 || response.status === 201) {
//         const cart_header = response.data.cart_header;
//         const items = response.data.items.map((item: any) => {
//           const competition = item.competition;
//           console.log("competiiton of cart", competition);
//           competition.quantity = item.quantity.toString();
//           const competitionWithKey = { ...competition, key: item.key };
//           return competitionWithKey;
//         });

//         console.log("comps with keys", items);

//         items.forEach((item: any) => {
//           const newKeys = {
//             ...cartKeys,
//             [item.id as number]: { key: item.key },
//           };
//           localStorage.setItem(UPDATE_CART_KEY, JSON.stringify(newKeys));
//         });
//         if (cart_header) {
//           localStorage.setItem(CART_HEADER, cart_header);
//         }

//         dispatch(addToCart(items));
//       }
//     } catch (error) {
//       console.log(error);
//     } finally {
//       dispatch(isAddingToCart(false));
//     }
//   };
