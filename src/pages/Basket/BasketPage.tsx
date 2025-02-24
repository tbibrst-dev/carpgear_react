import { useSelector } from "react-redux";
import BasketDynamic from "../../components/Basket/Basket-dynamic";
import { RootState } from "../../redux/store";
import { useEffect, useState } from "react";
import { CompetitionType, QunatityType } from "../../types";
import { navigateCompetition, quantitySetter } from "../../utils";

const BasketPage = () => {
  const cartItems = useSelector((state: RootState) => state.cart.cartItems);
  const [quantities, setQuantities] = useState<QunatityType>({});

  useEffect(() => {
    if (cartItems) {
      const quantities = quantitySetter(cartItems);
      setQuantities(quantities);
    }
  }, [cartItems]);

  const handleQuantityChange = (
    id: number,
    newQuantity: number,
    action: "increment" | "decrement"
  ) => {
    const competition = cartItems.find((comp) => comp.id === id);
    if (
      Number(competition?.max_ticket_per_user) === quantities[id] &&
      action === "increment"
    ) {
      return;
    }
    setQuantities((prevQuantities) => ({
      ...prevQuantities,
      [id]: Math.max(
        0,
        action === "increment"
          ? prevQuantities[id] + newQuantity
          : prevQuantities[id] - newQuantity
      ),
    }));
  };

  const quantityChangeInput = (value: number, id: number) => {
    if (!value) {
      setQuantities((prevQuantities) => ({
        ...prevQuantities,
        [id]: 0,
      }));
    }

    const competition = cartItems.find(
      (item) => item.id === id
    ) as CompetitionType;
    if (value > parseInt(competition.max_ticket_per_user)) {
      setQuantities((prevQuantities) => ({
        ...prevQuantities,
        [id]: parseInt(competition.max_ticket_per_user),
      }));
      return;
    }

    setQuantities((prevQuantities) => ({
      ...prevQuantities,
      [id]: value,
    }));
  };

  return (
    <div>
      <BasketDynamic
        cartItems={cartItems}
        handleQuantityChange={handleQuantityChange}
        quantities={quantities}
        quantityChangeInput={quantityChangeInput}
        navigateCompetition={navigateCompetition}
      />
    </div>
  );
};

export default BasketPage;
