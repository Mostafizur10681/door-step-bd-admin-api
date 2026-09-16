const fs = require('fs');

const aiChatbotPath = 'E:/xampp/htdocs/shopiabd-frontend/src/components/common/AIChatbot.tsx';
if (fs.existsSync(aiChatbotPath)) {
  let content = fs.readFileSync(aiChatbotPath, 'utf8');
  
  if (!content.includes('AlertCircle,')) {
    content = content.replace('Clock,', 'Clock,\n  AlertCircle,');
  }

  const badBlock = `          {/* Chat Input or Blocked Notice */}
          {isBlocked ? (
            <div className="p-3 bg-rose-50 dark:bg-rose-950/60 border-t border-rose-200 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-semibold text-center flex items-center justify-center gap-2 shrink-0">
              <span className="text-sm">dYs</span>
              <span>You have been blocked from live chat support.</span>
            </div>
          )`;

  const cleanBlock = `          {/* Chat Input or Blocked Notice */}
          {isBlocked ? (
            <div className="p-3.5 bg-rose-50 dark:bg-rose-950/60 border-t border-rose-200 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-semibold text-center flex items-center justify-center gap-2 shrink-0">
              <AlertCircle className="w-4 h-4 text-rose-500 shrink-0" />
              <span>You have been blocked from live chat support by an administrator.</span>
            </div>
          )`;

  content = content.replace(badBlock, cleanBlock);
  fs.writeFileSync(aiChatbotPath, content, 'utf8');
  console.log('Cleaned up blocked notice in AIChatbot.tsx');
}
