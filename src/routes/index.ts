import { lazy } from "react";
import { Routes } from "../types";

const Competitions = lazy(() => import("../pages/Competitions/Competitions"));
const InstanWinPage = lazy(() => import("../pages/Insant-win-page"));
const CompetitionDetailsPage = lazy(
  () => import("../pages/Competition-Details")
);
const BasketPage = lazy(() => import("../pages/Basket/BasketPage"));
const CheckoutPage = lazy(() => import("../pages/Checkout/CheckoutPage"));
const LoginPage = lazy(() => import("../pages/Auth/LoginPage"));
const ResultPage = lazy(() => import("../pages/ResultPage/ResultPage"));
const ConfirmationPage = lazy(
  () => import("../pages/ConfirmationPage/ConfirmationPage")
);

const ClaimPrizePage = lazy(
  () => import("../pages/ClaimPrizePage/ClaimPrizePage")
);

const NotFoundPage = lazy(() => import("../pages/NotFound"));
const ForgotPasswordPage = lazy(
  () => import("../pages/Auth/ForgotPasswordPage")
);
const ResetPasswordPage = lazy(() => import("../pages/Auth/ResetPasswordPage"));

const PrivacyPolicyPage = lazy(
  () => import("../pages/PrivacyPolicyPage/PrivacyPolicyPage")
);

const FreePostalRoutePage = lazy(
  () => import("../pages/FreePostalRoutePage/FreePostalRoutePage")
);

const ContactPage = lazy(() => import("../pages/ContactPage/ContactPage"));
const WinnersPage = lazy(() => import("../pages/WinnersPage/WinnersPage"));
const WinnersListPage = lazy(() => import("../pages/WinnersListPage/WinnersListPage"));

const CommunityChat =  lazy(() => import("../pages/ChatApp/cometchat"));
const FAQs =  lazy(() => import("../pages/FAQs/Faqs"));


const routes: Routes = [
  {
    path: "/competitions/:category",
    name: "Competitions",
    component: Competitions,
  },
  {
    path: "/competitions/instant_win_comps",
    name: "Instant win comps",
    component: InstanWinPage,
  },
  {
    path: "/competitions/instant/wins",
    name: "Instant Wins",
    component: InstanWinPage,
  },
  {
    path: "/competition/details/:competitionDetail",
    name: "Competition details",
    component: CompetitionDetailsPage,
  },
  {
    path: "/competition/info/:id",
    name: "Competition info",
    component: CompetitionDetailsPage,
  },
  {
    path: "/cart",
    name: "Cart Page",
    component: BasketPage,
  },
  {
    path: "/checkout",
    name: "Checkout",
    component: CheckoutPage,
  },
  {
    path: "/auth/login",
    name: "Login",
    component: LoginPage,
  },
  {
    path: "/draw/results",
    name: "Results",
    component: ResultPage,
  },
  {
    path: "/confirmation",
    name: "Confirmation",
    component: ConfirmationPage,
  },
  {
    path: "/claim/prize",
    name: "Claim Prize",
    component: ClaimPrizePage,
  },
  {
    path: "*",
    name: "Not found",
    component: NotFoundPage,
  },
  {
    path: "/auth/forgot/password",
    name: "Forgot Password",
    component: ForgotPasswordPage,
  },
  {
    path: "/auth/reset/password",
    name: "Reset Password",
    component: ResetPasswordPage,
  },
  {
    path: "/legal-terms",
    name: "Privacy policy",
    component: PrivacyPolicyPage,
  },
  {
    path: "/contact",
    name: "Contact us",
    component: ContactPage,
  },
  {
    path: "/results",
    name: "Draw Results",
    component: WinnersPage,
  },
  {
    path: "/free-postal-route",
    name: "Free Postal Route",
    component: FreePostalRoutePage,
  },
  {
    path: "/winners_list",
    name: "Winners",
    component: WinnersListPage,
  },
  {
    path: "/community-chat",
    name: "community-chat",
    component: CommunityChat,
  },
  {
    path: "/faq",
    name: "faqs",
    component: FAQs,
  }
];

export default routes;
