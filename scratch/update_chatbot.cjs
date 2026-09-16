const fs = require('fs');

// 1. Update chatbot.ts
const chatbotTsPath = 'E:/xampp/htdocs/shopiabd-frontend/src/lib/chatbot.ts';
if (fs.existsSync(chatbotTsPath)) {
  let content = fs.readFileSync(chatbotTsPath, 'utf8');
  
  if (!content.includes('fetchChatHistoryWithStatus')) {
    const oldCode = `export async function fetchChatHistory(
  conversationId: string | number
): Promise<ChatMessage[]> {
  const sessionId = getGuestSessionToken();
  const authToken = typeof window !== "undefined" ? localStorage.getItem("shopia_token") : null;

  try {
    const res = await fetch(\`\${API_V1}/chats?session_id=\${encodeURIComponent(sessionId)}\`, {
      headers: {
        "Accept": "application/json",
        ...(authToken ? { Authorization: \`Bearer \${authToken}\` } : {}),
      },
    });

    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data?.data)) {
        return data.data.map((item: any) => ({
          id: item.id || ("msg_" + Math.random().toString(36).substring(2, 9)),
          conversation_id: conversationId,
          sender_type: item.sender === "admin" ? "agent" : (item.sender === "user" ? "customer" : "ai"),
          message_type: item.message_type || "text",
          message: item.message,
          metadata: item.metadata || null,
          created_at: item.created_at || new Date().toISOString(),
        }));
      }
    }
  } catch (err) {
    console.warn(\`API /chats?session_id=\${sessionId} failed:\`, err);
  }
  return [];
}`;

    const newCode = `export async function fetchChatHistoryWithStatus(
  conversationId: string | number
): Promise<{ messages: ChatMessage[]; isBlocked: boolean }> {
  const sessionId = getGuestSessionToken();
  const authToken = typeof window !== "undefined" ? localStorage.getItem("shopia_token") : null;

  try {
    const res = await fetch(\`\${API_V1}/chats?session_id=\${encodeURIComponent(sessionId)}\`, {
      headers: {
        "Accept": "application/json",
        ...(authToken ? { Authorization: \`Bearer \${authToken}\` } : {}),
      },
    });

    if (res.ok) {
      const data = await res.json();
      const isBlocked = Boolean(data?.is_blocked);
      if (Array.isArray(data?.data)) {
        const msgs = data.data.map((item: any) => ({
          id: item.id || ("msg_" + Math.random().toString(36).substring(2, 9)),
          conversation_id: conversationId,
          sender_type: item.sender === "admin" ? "agent" : (item.sender === "user" ? "customer" : "ai"),
          message_type: item.message_type || "text",
          message: item.message,
          metadata: item.metadata || null,
          created_at: item.created_at || new Date().toISOString(),
        }));
        return { messages: msgs, isBlocked };
      }
      return { messages: [], isBlocked };
    }
  } catch (err) {
    console.warn(\`API /chats?session_id=\${sessionId} failed:\`, err);
  }
  return { messages: [], isBlocked: false };
}

export async function fetchChatHistory(
  conversationId: string | number
): Promise<ChatMessage[]> {
  const result = await fetchChatHistoryWithStatus(conversationId);
  return result.messages;
}`;

    content = content.replace(oldCode, newCode);
    fs.writeFileSync(chatbotTsPath, content, 'utf8');
    console.log('chatbot.ts updated successfully!');
  } else {
    console.log('chatbot.ts already contains fetchChatHistoryWithStatus');
  }
} else {
  console.log('chatbotTsPath not found:', chatbotTsPath);
}

// 2. Update AIChatbot.tsx
const aiChatbotPath = 'E:/xampp/htdocs/shopiabd-frontend/src/components/common/AIChatbot.tsx';
if (fs.existsSync(aiChatbotPath)) {
  let aiContent = fs.readFileSync(aiChatbotPath, 'utf8');
  
  // import fetchChatHistoryWithStatus
  if (!aiContent.includes('fetchChatHistoryWithStatus')) {
    aiContent = aiContent.replace(
      'fetchChatHistory,',
      'fetchChatHistory,\n  fetchChatHistoryWithStatus,'
    );
  }

  // add isBlocked state
  if (!aiContent.includes('const [isBlocked, setIsBlocked] = useState(false);')) {
    aiContent = aiContent.replace(
      'const [unreadCount, setUnreadCount] = useState(0);',
      'const [unreadCount, setUnreadCount] = useState(0);\n  const [isBlocked, setIsBlocked] = useState(false);'
    );
  }

  // update polling to check isBlocked
  const oldPolling = `        const history = await fetchChatHistory(conversation.id);
        if (history && history.length > 0) {
          setMessages((prev) => {
            if (history.length !== prev.length) {
              return history;
            }
            return prev;
          });

          const hasAgentMsg = history.some((m) => m.sender_type === "agent");
          if (hasAgentMsg && conversation.mode !== "agent") {
            setConversation((c) => (c ? { ...c, mode: "agent", status: "open" } : c));
          }
        }`;

  const newPolling = `        const res = await fetchChatHistoryWithStatus(conversation.id);
        if (typeof res.isBlocked !== "undefined") {
          setIsBlocked(res.isBlocked);
        }
        const history = res.messages;
        if (history && history.length > 0) {
          setMessages((prev) => {
            if (history.length !== prev.length) {
              return history;
            }
            return prev;
          });

          const hasAgentMsg = history.some((m) => m.sender_type === "agent");
          if (hasAgentMsg && conversation.mode !== "agent") {
            setConversation((c) => (c ? { ...c, mode: "agent", status: "open" } : c));
          }
        }`;

  if (aiContent.includes(oldPolling)) {
    aiContent = aiContent.replace(oldPolling, newPolling);
  }

  // update initial load
  const oldInit = `        const res = await startChatConversation();
        if (res && res.success) {
          setConversation(res.conversation);
          setMessages(res.messages || []);
        }`;

  const newInit = `        const res = await startChatConversation();
        if (res && res.success) {
          setConversation(res.conversation);
          setMessages(res.messages || []);
        }
        const statusCheck = await fetchChatHistoryWithStatus(res?.conversation?.id || "init");
        if (statusCheck?.isBlocked) {
          setIsBlocked(true);
        }`;

  if (aiContent.includes(oldInit)) {
    aiContent = aiContent.replace(oldInit, newInit);
  }

  // update form submit replacement
  const oldForm = `          {/* Chat Input Form */}
          <form
            onSubmit={(e) => {
              e.preventDefault();
              handleSendMessage();
            }}
            className="p-2 bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 flex items-center gap-2 shrink-0"
          >
            <input
              ref={inputRef}
              type="text"
              value={inputText}
              onChange={(e) => setInputText(e.target.value)}
              placeholder="Ask products, FAQs, or order #..."
              className="flex-1 bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-2xl px-3 py-2 text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#0B3B82]/30"
            />
            <button
              type="submit"
              disabled={!inputText.trim() || isTyping}
              className="w-8 h-8 rounded-xl bg-[#0B3B82] hover:bg-[#072450] disabled:opacity-40 text-white flex items-center justify-center transition cursor-pointer shrink-0"
            >
              <Send className="w-3.5 h-3.5" />
            </button>
          </form>`;

  const newForm = `          {/* Chat Input or Blocked Notice */}
          {isBlocked ? (
            <div className="p-3 bg-rose-50 dark:bg-rose-950/60 border-t border-rose-200 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-semibold text-center flex items-center justify-center gap-2 shrink-0">
              <span className="text-sm">🚫</span>
              <span>You have been blocked from live chat support.</span>
            </div>
          ) : (
            <form
              onSubmit={(e) => {
                e.preventDefault();
                handleSendMessage();
              }}
              className="p-2 bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 flex items-center gap-2 shrink-0"
            >
              <input
                ref={inputRef}
                type="text"
                value={inputText}
                onChange={(e) => setInputText(e.target.value)}
                placeholder="Ask products, FAQs, or order #..."
                className="flex-1 bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-2xl px-3 py-2 text-xs text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#0B3B82]/30"
              />
              <button
                type="submit"
                disabled={!inputText.trim() || isTyping}
                className="w-8 h-8 rounded-xl bg-[#0B3B82] hover:bg-[#072450] disabled:opacity-40 text-white flex items-center justify-center transition cursor-pointer shrink-0"
              >
                <Send className="w-3.5 h-3.5" />
              </button>
            </form>
          )}`;

  if (aiContent.includes(oldForm)) {
    aiContent = aiContent.replace(oldForm, newForm);
    fs.writeFileSync(aiChatbotPath, aiContent, 'utf8');
    console.log('AIChatbot.tsx updated successfully!');
  } else {
    console.log('Could not find oldForm in AIChatbot.tsx, checking matches...');
  }
}
