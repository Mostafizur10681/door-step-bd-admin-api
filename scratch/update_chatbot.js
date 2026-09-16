const fs = require('fs');
const path = require('path');

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
    console.log('chatbot.ts updated!');
  }
}
