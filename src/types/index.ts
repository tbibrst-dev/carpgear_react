interface RouteObject {
  name: string;
  path: string;
  component: React.LazyExoticComponent<() => JSX.Element>;
}

export type Routes = Array<RouteObject>;

export interface RewardsType {
  
  competition_id: string;
  id: string;
  image: string;
  quantity?: string;
  title: string;
  type: string;
  value: string;
  prcnt_available?: string;
  user_id?: string | null;
  reward_open: boolean;
  full_name: string | null;
  ticket_number: number;
  show_description?: any;
  prize_description?: any;
}

export interface InstantWinTickets {
  id: string;
  competition_id: string;
  instant_id: string;
  ticket_number: string;
  user_id: null | number;
  full_name: string | null;
}

export interface CompetitionType {  
  id: number;
  category: string;
  draw_date: string;
  draw_time: string;
  closing_date: string;
  closing_time: string;
  description: string;
  image: string;
  gallery_images: string;
  price_per_ticket: string;
  quantity: string;
  title: string;
  status: string;
  total_sell_tickets: string;
  total_ticket_sold: string;
  created_at: string;
  max_ticket_per_user: string;
  reward_wins: RewardsType[];
  instant_wins: RewardsType[];
  instant_wins_tickets?: InstantWinTickets[];
  faq?: string;
  competition_rules?: string;
  enable_instant_wins: string;
  enable_reward_wins: string;
  comp_question: string;
  question: string;
  question_options: string;
  competition_product_id: number;
  hide_ticket_count: string;
  hide_timer: string;
  disable_tickets: string;
  tickets?: string[];
  live_draw_info: string;
  live_draw: string;
  promotional_messages?:string;
  won?:any;
  sale_price?:any;
  order_id?:any;
  totals?:any;
  instant_win_only?:any;
  sliderSorting?:any;
  via_mobile_app?:any;
  sale_end_date?:any;
  sale_start_date?:any;
  images_thumb?:any;
  images_thumb_cat?:any;
  sale_end_time?:any;
  sale_start_time?:any;



}

export interface SettingsType {
  instant_wins_info: string;
  live_draw_info: string;
  main_competition: string;
  postal_entry_info: string;
  reward_prize_info: string;
  slider_speed: string;
  work_step_1: string;
  work_step_2: string;
  work_step_3: string;
}

export interface MetaData {
  id: string;
  page: string;
  page_title: string;
  meta_title: string;
  meta_description: string;
}

export interface QunatityType {
  [key: number]: number;
}

export interface AnswersType {
  answer1: string;
  answer2: string;
  answer3: string;
}

export interface AuthErrors {
  email: string;
  password: string;
}

export interface User {
  billing_address_1: string;
  billing_address_2: string;
  billing_city: string;
  billing_company: string;
  billing_country: string;
  billing_email: string;
  billing_first_name: string;
  billing_last_name: string;
  billing_postcode: string;
  billing_phone: string;
  billing_state: string;
  description: string;
  email: string;
  first_name: string;
  last_name: string;
  limit_duration: string;
  limit_value: string;
  lockout_period: string;
  name: string;
  nickname: string;
  token: string;
  current_spending: string;
  lock_account: string;
  locking_period: string;
  currentPassword?: string;
  newPassword?: string;
  confirmPassword?: string;
  comchatid?: string;
  id?:any;
  account_number?:any;
  sort_code?:any;
}

export interface NonceApiResponse {
  status: string;
  nonce: string;
  code: string;
}

export type OrderDetails = {
  categories: string;
  email: string;
  name: string;
  order_created: {
    date: string;
    timezone_type: number;
    timezone: string;
  };
  order_number: number;
  reset_cart_header: boolean;
  token: string;
  comps: string;
  instant_winner: boolean;
  instant_wins: string;
};

export type Question = {
  [key: number]: [key: string];
};

export type InstantWinDetails = {
  id: number;
  competition_id: number;
  instant_id: number;
  ticket_number: number;
  user_id: number | null;
  title: string;
  type: string;
  value: number;
  quantity: number;
  image: string;
};

export type PurchasedTickets = {
  total_tickets: string;
  competition_id: string;
};

export type PointLogs = {
  admin_user_id: string;
  data?: any;
  date: string;
  date_display: string;
  date_display_human: string;
  description: string;
  id: string;
  order_id: null;
  points: string;
  type: string;
  user_id: string;
  user_points_id: string;
};

export type OrderType = {
  item_count: number;
  order_date: string;
  order_id_: number;
  order_number: string;
  order_status: string;
  order_total: string;
};
