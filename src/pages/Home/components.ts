import { lazy } from "react";
const CompsforEveryone = lazy(() => import("../../components/Home/CompsforEveryone"));
const Carousel = lazy(() => import("../../common/CarouselNew"));
const DrawNext = lazy(() => import("../../components/Home/Draw-next"));
const Finished = lazy(() => import("../../components/Home/Finished"));
const InstantWins = lazy(() => import("../../components/Home/Instant-win"));
const Pricing = lazy(() => import("../../components/Home/Pricing"));
const Wrapper = lazy(() => import("../../components/Home/Wrapper"));
const AnchorNav = lazy(() => import("../../components/Home/AnchorNav"));

const components = [
  {
    name: "Carousel",
    component: Carousel,
  },
  {
    name: "Pricing",
    component: Pricing,
  },

  {
    name: "AnchorNav",
    component: AnchorNav,
  },

  {
    name: "Draw-next",
    component: DrawNext,
  },

  {
    name: "Instant-wins",
    component: InstantWins,
  },

  {
    name: "Comps for Everyone",
    component: CompsforEveryone,
  },

  {
    name: "Finished",
    component: Finished,
  },
  {
    name: "Wrapper",
    component: Wrapper,
  },
];

export default components;
