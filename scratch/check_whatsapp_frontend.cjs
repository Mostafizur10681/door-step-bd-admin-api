const fs = require('fs');

const files = [
  'E:/xampp/htdocs/shopiabd-frontend/src/components/common/WhatsAppButton.tsx',
  'E:/xampp/htdocs/shopiabd-frontend/src/components/WhatsAppButton.tsx',
  'E:/xampp/htdocs/shopiabd-frontend/src/app/layout.tsx'
];

files.forEach(f => {
  if (fs.existsSync(f)) {
    console.log('=== ' + f + ' ===');
    console.log(fs.readFileSync(f, 'utf8'));
  } else {
    console.log('File not found: ' + f);
  }
});
