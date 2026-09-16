const fs = require('fs');
const apiPath = 'E:/xampp/htdocs/shopiabd-frontend/src/lib/api.ts';
if (fs.existsSync(apiPath)) {
  console.log(fs.readFileSync(apiPath, 'utf8'));
} else {
  console.log('api.ts not found');
}
