const fs = require('fs');
const path = require('path');

function searchDir(dir, pattern) {
  const files = fs.readdirSync(dir);
  for (const f of files) {
    const full = path.join(dir, f);
    const stat = fs.statSync(full);
    if (stat.isDirectory()) {
      if (f !== 'node_modules' && f !== '.next' && f !== '.git') {
        searchDir(full, pattern);
      }
    } else if (/\.(tsx|ts|js|jsx)$/.test(f)) {
      const content = fs.readFileSync(full, 'utf8');
      if (pattern.test(content)) {
        console.log('Match found in:', full);
      }
    }
  }
}

searchDir('E:/xampp/htdocs/shopiabd-frontend/src', /01879198066|8801879198066/);
