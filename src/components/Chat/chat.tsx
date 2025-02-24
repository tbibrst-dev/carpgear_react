import React, {
  useState,
  useRef,
  useEffect,
  useCallback,
  useContext,
} from "react";
import { useSelector } from "react-redux";
import { Link, useLocation, useNavigate } from "react-router-dom";
import {
  CometChatUIKit,
  UIKitSettingsBuilder,
  CometChatGroupsWithMessages,
  CreateGroupConfiguration,
  CreateGroupStyle,
  CometChatThemeContext,
  GroupsConfiguration,
  MessageHeaderConfiguration,
  MessagesConfiguration,
  MessagesStyle,
  MessageComposerConfiguration,
  WithMessagesStyle,
  GroupsStyle,
  MessageListConfiguration,
  CometChatTheme,
  CometChatPalette,
  DatePatterns,
  DateStyle,
  AvatarStyle,
  CometChatGroupEvents,
  ListItemStyle,
  CometChatLocalize,
  ThreadedMessagesConfiguration,
  MessageHeaderStyle,
  CometChatDetails,
  CometChatCreateGroup,
  ReactionsConfiguration,
  ReactionInfoConfiguration,
  ReactionInfoStyle

} from "@cometchat/chat-uikit-react";
import { BaseMessage, CometChat, Group, TextMessage, User } from "@cometchat/chat-sdk-javascript";
import { RootState } from "../../redux/store";
import { AUTH_TOKEN_KEY, decryptToken, TOKEN } from "../../utils";
import axios from "axios";
import { createComponent } from "@lit-labs/react";


// import { useLocation } from "react-router-dom";

type PropsTypes = {
  onToggleChat: (isOpen: boolean) => void;
};

const ChatComponent: React.FC<PropsTypes> = ({ onToggleChat }) => {

  const navigate = useNavigate();


  const { theme } = useContext(CometChatThemeContext);
  const user = useSelector((state: RootState) => state.userReducer.user);
  const [isChatOpen, setIsChatOpen] = useState(false);
  const chatBarRef = useRef<HTMLDivElement | null>(null);
  const [isCometChatReady, setIsCometChatReady] = useState(false);
  const [unreadCount, setUnreadCount] = useState(0);
  const visitorUserLoginID = "cggvisitoruser_012";
  const [unreadMessageCounts, setUnreadMessageCounts] = useState<any>();
  const [pinnedMessage, setPinnedMessage] = useState<any>();
  const [pinnedMessageShow, setPinnedMessageShow] = useState<any>();

  // const visitorUserLoginNAME = 'CGG Visitor User';
  const [groupNew, setGroupNew] = useState<CometChat.Group | undefined>(undefined);
  const [showGroupDetailsModal, setShowGroupDetailsModal] = useState<boolean>(false);
  const [group, setGroup] = useState<CometChat.Group | undefined>(undefined)
  const [openGroupModal, setOpenGroupModal] = useState(false);



  const CreateGroup = createComponent({
    tagName: "cometchat-create-group",
    elementClass: CometChatCreateGroup,
    react: React,
  });


  const location = useLocation();
  // Check if the current path is "/community-chat"
  const shouldShowChatPopup = location.pathname !== "/community-chat";

  const COMETCHAT_CONSTANTS = {
    APP_ID: "263121674dc8214a",
    REGION: "EU",
    AUTH_KEY: "e80c650d29606870c059ad9855c852625714f532",
    API_KEY: "0667e4d462fcb9414846d323ef572bbeea036392",
  };
  const [loggedInUser, setLoggedInUser] = useState<CometChat.User | null>();

  const initCometChat = useCallback(async (user: any) => {
    const { APP_ID, REGION, AUTH_KEY } = COMETCHAT_CONSTANTS;

    if (APP_ID && REGION && AUTH_KEY) {
      const UIKitSettings = new UIKitSettingsBuilder()
        .setAppId(APP_ID)
        .setRegion(REGION)
        .setAuthKey(AUTH_KEY)
        .subscribePresenceForAllUsers()
        .build();

      try {

        CometChatLocalize.init("en", {
          en: {
            "GROUPS": "COMMUNITY",
          }
        })

        await CometChatUIKit.init(UIKitSettings);
        const loggedInUser = await CometChatUIKit.getLoggedinUser();

        setLoggedInUser(loggedInUser);

        if (!loggedInUser) {
          try {
            if (user && user.comchatid) {

              await CometChatUIKit.login(user.comchatid);

              setIsCometChatReady(true);

              CometChatUIKit.getLoggedinUser().then((user) => {
                setLoggedInUser(user);
              });
            } else {

              await CometChatUIKit.login(visitorUserLoginID);
              setIsCometChatReady(true);

              CometChatUIKit.getLoggedinUser().then((user) => {
                setLoggedInUser(user);
              });
            }
          } catch (loginError) {
            setIsCometChatReady(false);
          }
        } else {
          setIsCometChatReady(true);
        }
      } catch (initError) {
        console.error("Initialization Error:", initError);
      }
    } else {
      console.error("COMETCHAT_CONSTANTS values are not defined.");
    }
  }, []);

  useEffect(() => {
    const { APP_ID, REGION, AUTH_KEY } = COMETCHAT_CONSTANTS;

    const UIKitSettings = new UIKitSettingsBuilder()
      .setAppId(APP_ID)
      .setRegion(REGION)
      .setAuthKey(AUTH_KEY)
      .subscribePresenceForAllUsers()
      .build();

    const fetchLoggedInUser = async () => {
      try {
        await CometChatUIKit.init(UIKitSettings);

        const loggedInUser = await CometChatUIKit.getLoggedinUser();


        if (isChatOpen) {
          if (loggedInUser) {

            // const uid = visitorUserLoginID;
            // const user_id = new CometChat.User(uid);


            CometChatUIKit.logout()
              .then(() => {
                initCometChat(user);
              })
              .catch((error) => {
                console.log("Logged-out user failed with exception:", error);
              });
          } else {
            CometChatUIKit.logout()
              .then(() => {
                initCometChat(user);
              })
              .catch((error) => {
                console.log("Logged-out user failed with exception:", error);
              });
            initCometChat(visitorUserLoginID);
          }
        } else {
          setIsCometChatReady(false);
        }
      } catch (error) {
        console.error("Error fetching logged-in user:", error);
      }
    };

    fetchLoggedInUser();
    fetchPinnedMessage();
  }, [isChatOpen, user, initCometChat]);

  const fetchPinnedMessage = async () => {
    const encodedToken = localStorage.getItem(AUTH_TOKEN_KEY) as string;
    const token = decryptToken(encodedToken);
    try {
      const res = await axios.post(
        "?rest_route=/api/v1/pinned-message",
        { token },
        { headers: { Authorization: TOKEN } }
      );
      console.log(res);
      if (res.data.success) {
        console.log('++++++++++++++++', res)
        setPinnedMessage(res.data.pinnedMessageText);
        setPinnedMessageShow(res.data.showPinnedMessage);

      }
    } catch (error) {
      console.log(error);
    }
  }

  const toggleChatBox = () => {
    if (chatBarRef.current) {
      chatBarRef.current.classList.toggle("toggle");
    }
    setIsChatOpen((prevState) => !prevState);
    onToggleChat(!isChatOpen);
    setUnreadCount(0);
    setGroup(undefined)
  };

  useEffect(() => {
    if (isChatOpen) {
      // Add the no-scroll class to the body to disable scrolling
      document.body.classList.add("no-scroll");
    } else {
      // Remove the no-scroll class when the chat is closed
      document.body.classList.remove("no-scroll");
    }

    // Clean up on component unmount
    return () => {
      document.body.classList.remove("no-scroll");
    };
  }, [isChatOpen]);

  useEffect(() => {
    if (theme) {
      theme.palette.setMode("dark");
      theme.palette.setPrimary({ light: "white", dark: "#EEC273" });
      theme.palette.setSecondary({ light: "black", dark: "#30302f" });
      theme.palette.setAccent({ light: "#FFFFFF", dark: "#FFFFFF" });
      theme.palette.setAccent200({ light: "#f5bc42", dark: "#f5bc42" });
      theme.typography.setFontFamily("Mozaic GEO, monospace");
    }
  }, [theme]);

  const createGroupStyle = new CreateGroupStyle({
    borderRadius: "15px",
    height: "280px",
    width: "100%",
    createGroupButtonBackground: "#FFBB41",
    createGroupButtonTextColor: "#212529",

    activeGroupTypeBackground: "#202323",
    activeGroupTypeTextColor: "#202323",
    activeGroupTypeBorder: "#202323",

    groupTypeTextBorderRadius: "#202323",
    groupTypeTextBoxShadow: "#202323",
    groupTypeTextBackground: "#202323",

    groupTypeTextColor: "#202323",
    groupTypeBorderRadius: "#202323",
    groupTypeBorder: "#202323",

    titleTextColor: "#FFFFFF",
    background: "#202323",
    nameInputBackground: "#282B2B",
    nameInputBorder: "#FFFFFF1A",
    nameInputTextColor: "white",
    nameInputPlaceholderTextColor: "white",
    closeIconTint: "#BABABA",
  });

  const groupRequestBuild = new CometChat.GroupsRequestBuilder()
    .setLimit(30)
    .joinedOnly(true);
  // .build();

  const messagesStyle = new MessagesStyle({
    borderRadius: "none",
    width: "100%",
  });

  // const openChatNewWindow = () => {
  //   window.open("/community-chat", "_blank");
  // };

  const openChatNewWindow = () => {
    setIsChatOpen(false);
    document.body.classList.remove("no-scroll");
    onToggleChat(!isChatOpen);
    navigate('/community-chat');
    // window.open("/community-chat", "_blank");
  };


  const groupsWithMessagesStyle = new WithMessagesStyle({
    background: "#202323",
    messageTextColor: "#FFFFFF",
  });

  const groupsStyle = new GroupsStyle({
    titleTextColor: "#FFFFFF",
    searchTextColor: "#FFFFFF",
    subTitleTextColor: "#FFFFFF",
    separatorColor: "#282B2B",
    searchBorder: "1px solid #FFFFFF1A",
    searchBackground: "#282B2B",
    titleTextFont: "30px sans-serif",
  });

  document.addEventListener("DOMContentLoaded", () => {
    const userDivs = document.querySelectorAll(".cc-message-bubble__header"); // Select all user name divs


    userDivs.forEach((userDiv) => {
      const randomColorClass = `color-${Math.floor(Math.random() * 6) + 1}`; // Random class between 1-6
      userDiv.classList.add(randomColorClass); // Add the random color class
    });
  });

  const loginClick = () => {
    setIsChatOpen(false);
    document.body.classList.remove("no-scroll");
    onToggleChat(!isChatOpen);
  };



  const tailwindColors = [
    "#e2e8f0", // slate-200
    "#cbd5e1", // slate-300
    "#94a3b8", // slate-400
    "#64748b", // slate-500
    "#e5e7eb", // gray-200
    "#d1d5db", // gray-300
    "#9ca3af", // gray-400
    "#6b7280", // gray-500
    "#e4e4e7", // zinc-200
    "#d4d4d8", // zinc-300
    "#a1a1aa", // zinc-400
    "#71717a", // zinc-500
    "#e7e5e4", // stone-200
    "#d6d3d1", // stone-300
    "#a8a29e", // stone-400
    "#78716c", // stone-500
    "#fecaca", // red-200
    "#fca5a5", // red-300
    "#f87171", // red-400
    "#ef4444", // red-500
    "#fed7aa", // orange-200
    "#fdba74", // orange-300
    "#fb923c", // orange-400
    "#f97316", // orange-500
    "#fde68a", // amber-200
    "#fcd34d", // amber-300
    "#fbbf24", // amber-400
    "#f59e0b", // amber-500
    "#fef08a", // yellow-200
    "#fde047", // yellow-300
    "#facc15", // yellow-400
    "#eab308", // yellow-500
    "#d9f99d", // lime-200
    "#bef264", // lime-300
    "#a3e635", // lime-400
    "#84cc16", // lime-500
    "#bbf7d0", // green-200
    "#86efac", // green-300
    "#4ade80", // green-400
    "#22c55e", // green-500
    "#a7f3d0", // emerald-200
    "#6ee7b7", // emerald-300
    "#34d399", // emerald-400
    "#10b981", // emerald-500
    "#99f6e4", // teal-200
    "#5eead4", // teal-300
    "#2dd4bf", // teal-400
    "#14b8a6", // teal-500
    "#a5f3fc", // cyan-200
    "#67e8f9", // cyan-300
    "#22d3ee", // cyan-400
    "#06b6d4", // cyan-500
    "#bae6fd", // sky-200
    "#7dd3fc", // sky-300
    "#38bdf8", // sky-400
    "#0ea5e9", // sky-500
    "#bfdbfe", // blue-200
    "#93c5fd", // blue-300
    "#60a5fa", // blue-400
    "#3b82f6", // blue-500
    "#c7d2fe", // indigo-200
    "#a5b4fc", // indigo-300
    "#818cf8", // indigo-400
    "#6366f1", // indigo-500
    "#ddd6fe", // violet-200
    "#c4b5fd", // violet-300
    "#a78bfa", // violet-400
    "#8b5cf6", // violet-500
    "#e9d5ff", // purple-200
    "#d8b4fe", // purple-300
    "#c084fc", // purple-400
    "#a855f7", // purple-500
    "#f5d0fe", // fuchsia-200
    "#f0abfc", // fuchsia-300
    "#e879f9", // fuchsia-400
    "#d946ef", // fuchsia-500
    "#fbcfe8", // pink-200
    "#f9a8d4", // pink-300
    "#f472b6", // pink-400
    "#ec4899", // pink-500
    "#fecdd3", // rose-200
    "#fda4af", // rose-300
    "#fb7185", // rose-400
    "#f43f5e", // rose-500
  ];

  const generateColorFromName = (name: string): string => {
    let hash = 0;

    // Create a hash from the name
    for (let i = 0; i < name.length; i++) {
      hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }

    // Ensure the hash is non-negative
    hash = Math.abs(hash);

    // Map hash to a color index
    const colorIndex = hash % tailwindColors.length;

    // Return the corresponding Tailwind color
    return tailwindColors[colorIndex];
  };

  const getHeaderView = (message: CometChat.BaseMessage) => {
    const sendrData = message.getSender();

    const color = generateColorFromName(sendrData.getUid());
    return message.getSender().getUid() !== user?.comchatid ? (
      <cometchat-label
        style={{ color, textTransform: "capitalize" }}
      // text={sendrData.getName()}
      />
    ) : (
      ""
    );
  };

  const getMessageOptions = (
    loggedInUser: User,
    message: BaseMessage,
    theme: CometChatTheme,
    group?: Group | undefined
  ) => {
    let defaultOption = CometChatUIKit.getDataSource().getMessageOptions(
      loggedInUser,
      message,
      theme,
      group
    );
    defaultOption = defaultOption.filter((opt) => opt.id !== "sendMessagePrivately")
    return defaultOption;
  };

  function getTemplates() {
    const defaultTemplates =
      CometChatUIKit.getDataSource().getAllMessageTemplates(
        new CometChatTheme({ palette: new CometChatPalette({ mode: "dark" }) })
      );
    defaultTemplates.forEach((template) => {
      template.options = (loggedInUser: CometChat.User, message: CometChat.BaseMessage, theme: CometChatTheme, group?: CometChat.Group) => getMessageOptions(loggedInUser, message, theme, group);
      if (template.category === "message" && template.type === "text") {
        template.headerView = (message: CometChat.BaseMessage) =>
          getHeaderView(message);
        template.contentView = (message: TextMessage) => {
          const color = generateColorFromName(message.getSender().getUid());
          if (message.getDeletedAt()) {
            return (
              <div
                style={{
                  border: "1px dashed white",
                  color: "white",
                  padding: "6px 12px",
                  borderRadius: "10px",
                }}
              >
                Message is deleted
              </div>
            );
          } else {

            return message.getSender().getUid() === user?.comchatid ? (
              <cometchat-text-bubble
                text={message.getText()}
                style={{ color: "#202323", fontWeight: '400', fontFamily: 'Mozaic GEO', fontSize: '14px' }}
              />

            ) : (
              <div style={{ padding: '16px 16px' }}>
                <div style={{ color, textTransform: 'capitalize', fontWeight: '700', fontFamily: 'Mozaic GEO', fontSize: '14px' }}>
                  {message.getSender().getName()}

                </div>
                <div style={{ color: '#FFFFFFBF', fontWeight: '400', fontFamily: 'Mozaic GEO', fontSize: '14px' }}>
                  {message.getText()}

                </div>
              </div>

            );
          }
        };
        template.statusInfoView = () => null;
        template.footerView = (message: {
          getSender: () => {
            (): any;
            new(): any;
            getUid: { (): string | undefined; new(): any };
          };
          getSentAt: () => any;
        }) => {
          return (
            <div
              style={{
                display: "flex",
                justifyContent: "flex-end",
                alignItems: "center",
                fontFamily: "Mozaic GEO",
                fontWeight: '400',
                color: '#FFFFFF66',
                lineHeight: '13.2px',
                marginTop: '8px'

              }}
            >
              <cometchat-date
                timestamp={message.getSentAt()}
                pattern={DatePatterns.time}
                dateStyle={JSON.stringify(
                  new DateStyle({
                    background: "transparent",
                    textFont: "400 12px Mozaic GEO",
                    padding: "0 7px 10px 5px",
                    textColor: '#FFFFFF66',
                  })
                )}
              ></cometchat-date>
            </div>
          );
        };
      }
    });
    return defaultTemplates;
  }

  const updateUnreadCount = (message: any) => {
    try {
      const groupID = message.receiverId;
      if (message.receiverType === "group") {
        try {
          setUnreadMessageCounts((prevCounts: any) => {
            console.log('prevCounts++++++++++++++', prevCounts);
            console.log('groupID+++++++++++++++++++', groupID);
            if (!groupID) {
              console.warn("Invalid groupID, skipping update.");
              return prevCounts || {};
            }
            return {
              ...(prevCounts || {}), // Fallback to an empty object
              [groupID]: ((prevCounts && prevCounts[groupID]) || 0) + 1,
            };
          });
          setUnreadCount(1);
        } catch (error) {
          console.error("Error updating unread counts:", error);
        }
      }
    } catch (error) {
      console.error("Error in updateUnreadCount:", error);
    }
  };


  useEffect(() => {
    CometChat.getUnreadMessageCount().then((data: any) => {
      if (data.groups) {
        setUnreadMessageCounts(data.groups);
      }
    });
  }, []);

  useEffect(() => {
    const listenerID = "UNREAD_MESSAGE_COUNT_LISTENER";
    CometChat.addMessageListener(
      listenerID,
      new CometChat.MessageListener({
        onTextMessageReceived: (message: any) => {
          updateUnreadCount(message);
        },
        onMediaMessageReceived: (message: any) => {
          updateUnreadCount(message);
        },
        onCustomMessageReceived: (message: any) => {
          updateUnreadCount(message);
        },
      })
    );

    return () => {
      // Clean up listener on unmount
      CometChat.removeMessageListener(listenerID);
    };
  }, []);


  const messageHeaderStyle = new MessageHeaderStyle({
    background: "#282B2B",
    border: "1px solid #FFFFFF1A",
    // borderRadius: "20px",
    // subtitleTextColor: "#2d55a6",
  });

  const getDetailsData = (user?: CometChat.User, group?: CometChat.Group) => {
    let defaultData = CometChatUIKit.getDataSource().getDefaultDetailsTemplate(loggedInUser as any, user as any, group as any, new CometChatTheme({}));
    console.log(defaultData)
    if (group?.getScope() === "participant") {
      defaultData = defaultData.filter((option) => option.id !== "primary")
    }
    return defaultData;
  }

  return (
    <div id="chatPage" className="chat_page maincometChatWrapper">
      {/* {isCometChatReady ? (
        <div className="link-full-page ">
          <h6>COMMUNITY</h6>
          <FontAwesomeIcon
            icon={faArrowUpRightFromSquare}
            title="Open In New Window"
            onClick={openChatNewWindow}
            className="faArrowUpRightFromSquareicon"
          />
        </div>
      ) : (
        ""
      )} */}

      {shouldShowChatPopup ? (
        <button
          onClick={toggleChatBox}
          className={`chat_button ${isChatOpen ? "hide" : ""}`}
        >
          <img
            src={
              unreadCount
                ? "/images/notificationbellreddot.svg"
                : "/images/notificationbellred.svg"
            }
            alt="notification"
            className="communityImage"
          />
        </button>
      ) : (
        ""
      )}

      <div
        id="chatbar"
        ref={chatBarRef}
        className={`chat_box ${isChatOpen}`}
        style={{
          display: `${isChatOpen ? "block" : "none"}`,
        }}
      >
        <div id="chatBody" className="chat_box_body">
          {isCometChatReady ? (
            <button onClick={toggleChatBox} className="chat_button_second">
              <img
                src="/images/community.png"
                alt="images"
                className="communityImage"
              ></img>
            </button>
          ) : (
            ""
          )}

          {
            openGroupModal && (
              <div
                style={{
                  height: "80%",
                  width: "100%",
                  right: "0",
                  display: "flex",
                  justifyContent: "center",
                  alignItems: "center",
                  position: "absolute",
                  zIndex: "200",
                  backgroundColor: "#2B2B2B",
                  top: "20",


                }}
              >
                <CreateGroup
                  createGroupStyle={createGroupStyle}
                  createClick={(group: any) => {
                    let groupName = group.getName()
                    groupName = groupName.toUpperCase()
                    group.setName(groupName)
                    CometChat.createGroup(group).then((group) => {
                      console.log("Group is created successfully: ", group)
                      CometChatGroupEvents.ccGroupCreated.next(group);
                      setOpenGroupModal(false)
                    })
                  }}
                  closeCallback={() => {
                    setOpenGroupModal(false)
                  }} />

              </div>
            )

          }

          {
            showGroupDetailsModal && (
              <div
                style={{
                  height: "80%",
                  width: "100%",
                  right: "0",
                  display: "flex",
                  justifyContent: "center",
                  alignItems: "center",
                  position: "absolute",
                  zIndex: "200",
                  backgroundColor: "#2B2B2B",
                }}
              >
                <CometChatDetails
                  data={getDetailsData(undefined, group)}
                  group={groupNew}
                  onClose={() => {
                    setShowGroupDetailsModal(false);
                  }}
                />
              </div>
            )
          }


          {isCometChatReady ? (
            <div className="cometChatWrapper">
              <CometChatGroupsWithMessages

                group={group}

                isMobileView={true}
                createGroupConfiguration={
                  new CreateGroupConfiguration({
                    createGroupStyle: createGroupStyle,
                    createClick: (group) => {
                      let groupName = group.getName();
                      groupName = groupName.toUpperCase();
                      group.setName(groupName);
                      CometChat.createGroup(group).then((group) => {
                        CometChatGroupEvents.ccGroupCreated.next(group);
                      });
                    }
                  })
                }
                groupsConfiguration={
                  new GroupsConfiguration({
                    menu: <></>,
                    groupsRequestBuilder: groupRequestBuild,
                    groupsStyle: groupsStyle,
                    avatarStyle: new AvatarStyle({
                      width: "38px",
                      height: "38px",
                      nameTextColor: "Black",
                      outerViewBorderSpacing: "18px 5px",
                    }),

                    onItemClick: (group: CometChat.Group) => {
                      const groupID = group.getGuid();
                      setUnreadMessageCounts((prevCounts: any) => ({
                        ...prevCounts,
                        [groupID]: null,
                      }));
                      setGroup(group)
                      setUnreadCount(0);
                    },

                    subtitleView: (group: any) => {

                      return (
                        <div
                          style={{
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "space-between",
                          }}
                        >
                          <p style={{ color: "white", fontSize: "12px" }}>
                            {group.membersCount} Members
                          </p>
                          <div
                            style={{
                              display: "flex",
                              alignItems: "center",
                              justifyContent: "space-between",
                              width: "20px",
                            }}
                          >

                            {unreadMessageCounts &&
                              unreadMessageCounts[group.guid] && (
                                <div
                                  style={{
                                    height: "5px",
                                    width: "5px",
                                    borderRadius: "50%",
                                    backgroundColor: "red",
                                  }}
                                ></div>
                              )}
                          </div>
                        </div>
                      );
                    },

                    listItemStyle: new ListItemStyle({
                      titleFont: "900 15px Mozaic GEO",
                      background: "#fffff",
                      titleColor: "White",
                      separatorColor: "#FFFFFF1A",
                    }),
                  })
                }
                groupsWithMessagesStyle={groupsWithMessagesStyle}
                messagesConfiguration={
                  new MessagesConfiguration({


                    threadedMessageConfiguration: new ThreadedMessagesConfiguration({

                      messageListConfiguration: new MessageListConfiguration({
                        templates: getTemplates(),
                        hideReceipt: true,
                        scrollToBottomOnNewMessages: true,
                        showAvatar: false,


                      }),


                      messageComposerConfiguration:
                        new MessageComposerConfiguration({
                          hideVoiceRecording: true,
                          emojiIconURL: "https://cggprelive.co.uk/images/smileemoji.svg",
                          attachmentIconURL: "https://cggprelive.co.uk/images/paperclip2.svg",
                          sendButtonIconURL: "https://cggprelive.co.uk/images/send-message-1.svg",
                          // messageComposerStyle: new MessageComposerStyle({
                          //   textFont: '300 15px  Mozaic GEO',
                          //   textColor: '#FFFFFF80',
                          //   attachIcontint: '#BABABA',
                          //   emojiIconTint: '#BABABA',
                          //   dividerTint: '#FFFFFF1A'
                          // }),
                          // actionSheetStyle: new ActionSheetStyle({
                          //   titleFont: '300 15px Mozaic GEO',
                          //   listItemBackground: '#FFFFFF1A',
                          //   listItemIconBackground: '#FFFFFF1A',
                          // }),



                        }),



                    }),

                    messageHeaderConfiguration: new MessageHeaderConfiguration({
                      // menu:{getMenus},
                      backButtonIconURL: "https://cggprelive.co.uk/images/backarrow.svg",
                      hideBackButton: false,
                      avatarStyle: new AvatarStyle({
                        width: '38px',
                      }),
                      messageHeaderStyle: messageHeaderStyle,
                      menu: (...args: any) => {
                        const whichGroupDetails = args[1];
                        console.log('whichGroupDetails', whichGroupDetails);

                        setGroupNew(whichGroupDetails);
                        return (
                          <>
                            <span
                              onClick={() => {
                                setShowGroupDetailsModal(true);
                              }}
                            >
                              <img src="https://cggprelive.co.uk/images/groupinfo.svg" style={{ width: "20px", height: "20px", cursor: "pointer", marginRight: "15px" }} />
                            </span>

                            <span
                              style={{ backgroundColor: "transparent", border: "0", cursor: "pointer" }}
                              onClick={openChatNewWindow}
                            >
                              <img
                                src="https://cggprelive.co.uk/images/openfullscreens.svg"
                                alt="Create Group"
                                style={{ height: "20px", width: "20px" }}
                              />

                            </span> </>
                        );
                      },
                      onBack: () => {
                        setGroup(undefined)
                      }


                    }),
                    messageListConfiguration: new MessageListConfiguration({
                      templates: getTemplates(),
                      hideReceipt: true,
                      scrollToBottomOnNewMessages: true,
                      disableMentions: true,
                      showAvatar: false,
                      reactionsConfiguration: new ReactionsConfiguration({
                        reactionInfoConfiguration: new ReactionInfoConfiguration({
                          reactionInfoStyle: new ReactionInfoStyle({
                            // there are other styling too that you can modify
                            background: "#282B2B",
                            reactedTextColor: '#FFFFFF',
                            reactionFontSize: "12px",
                            borderRadius: '8px',
                            width: '200px'
                          })
                        }),
                      }),
                      headerView: pinnedMessage && pinnedMessageShow == 1 ? (
                        <div
                          style={{
                            color: '#FFFFFF',
                            backgroundColor: '#12524B',
                            fontFamily: 'Mozaic GEO',
                            fontSize: '14px',
                            fontWeight: '400',
                            lineHeight: '18.85px',
                            textAlign: 'left',
                            textUnderlinePosition: 'from-font',
                            textDecorationSkipInk: 'none',
                            width: '100%',
                            padding: '12px 16px',
                            borderBottom: '1px solid #FFFFFF1A',
                            display: 'flex',
                            justifyContent: 'space-between'

                          }}

                        >

                          <div>
                            <span className="static_message">Pinned message by Carp Gear Giveaways</span> <br></br>
                            <span
                              className="pinned_dynamic_message"
                              dangerouslySetInnerHTML={{ __html: pinnedMessage }}
                            ></span>
                          </div>
                          <div style={{ alignContent: 'center' }}>
                            <svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M1.83784 5.66214C1.09757 5.66214 0.5 6.25971 0.5 6.99998C0.5 7.74025 1.09757 8.33782 1.83784 8.33782C2.57811 8.33782 3.17568 7.74025 3.17568 6.99998C3.17568 6.25971 2.57811 5.66214 1.83784 5.66214ZM1.83784 0.310791C1.09757 0.310791 0.5 0.908359 0.5 1.64863C0.5 2.3889 1.09757 2.98647 1.83784 2.98647C2.57811 2.98647 3.17568 2.3889 3.17568 1.64863C3.17568 0.908359 2.57811 0.310791 1.83784 0.310791ZM1.83784 11.0135C1.09757 11.0135 0.5 11.62 0.5 12.3513C0.5 13.0827 1.10649 13.6892 1.83784 13.6892C2.56919 13.6892 3.17568 13.0827 3.17568 12.3513C3.17568 11.62 2.57811 11.0135 1.83784 11.0135ZM4.51351 13.2432H17V11.4594H4.51351V13.2432ZM4.51351 7.89187H17V6.10809H4.51351V7.89187ZM4.51351 0.756737V2.54052H17V0.756737H4.51351Z" fill="white" fill-opacity="0.5" />
                            </svg>

                          </div>


                        </div>
                      ) : null, // Render nothing if no pinned message

                    }),

                    messageComposerView:
                      loggedInUser?.getUid() == null ||
                        loggedInUser?.getUid() == visitorUserLoginID
                        ? () => {
                          return (
                            <div
                              className="defaultComposer"
                              style={{ color: "white" }}
                            >
                              <div className="defaultComposer-second-div">
                                <p>You need an account to send a message</p>
                                <Link
                                  to="/auth/login"
                                  className=""
                                  style={{ color: "#000" }}
                                  onClick={loginClick}
                                >
                                  <button
                                    type="button"
                                    className="check-login"
                                  >
                                    <svg
                                      width="14"
                                      height="15"
                                      viewBox="0 0 14 15"
                                      fill="none"
                                      xmlns="http://www.w3.org/2000/svg"
                                    >
                                      <path
                                        d="M8.25187 8.73312H5.74864C4.4213 8.73461 3.14876 9.26256 2.21019 10.2011C1.27162 11.1397 0.743678 12.4122 0.742188 13.7396C0.742102 13.8723 0.784186 14.0015 0.862364 14.1087C0.940542 14.2159 1.05077 14.2955 1.17712 14.336C3.0776 14.8277 5.03803 15.0482 7.00025 14.9912C8.96247 15.0482 10.9229 14.8277 12.8234 14.336C12.9497 14.2955 13.06 14.2159 13.1381 14.1087C13.2163 14.0015 13.2584 13.8723 13.2583 13.7396C13.2568 12.4122 12.7289 11.1397 11.7903 10.2011C10.8517 9.26256 9.5792 8.73461 8.25187 8.73312Z"
                                        fill="#0F1010"
                                      ></path>
                                      <path
                                        d="M7.00053 7.48152C8.94053 7.48152 10.4425 5.30997 10.4425 3.44194C10.4425 2.52908 10.0798 1.65361 9.43435 1.00812C8.78886 0.362632 7.91339 0 7.00053 0C6.08767 0 5.2122 0.362632 4.56671 1.00812C3.92122 1.65361 3.55859 2.52908 3.55859 3.44194C3.55859 5.30997 5.06053 7.48152 7.00053 7.48152Z"
                                        fill="#0F1010"
                                      ></path>
                                    </svg>{" "}
                                    Login
                                  </button>
                                </Link>
                              </div>
                            </div>
                          );
                        }
                        : null,
                    // messageListView: messageListConfig,
                    messageComposerConfiguration:
                      new MessageComposerConfiguration({
                        hideVoiceRecording: true,
                        emojiIconURL: "https://cggprelive.co.uk/images/smileemoji.svg",
                        attachmentIconURL: "https://cggprelive.co.uk/images/paperclip2.svg",
                        sendButtonIconURL: "https://cggprelive.co.uk/images/send-message-1.svg",
                      }),
                    messagesStyle: messagesStyle,
                    // detailsConfiguration: new DetailsConfiguration({}),
                  })
                }
              />
            </div>
          ) : (
            <div className="withoutLogin loading">
              <div className="loginDiv">
                <span className="connecting-padding">
                  Connecting you with your community... Just a moment!
                </span>
                <span className="loader-chat"></span>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default ChatComponent;
