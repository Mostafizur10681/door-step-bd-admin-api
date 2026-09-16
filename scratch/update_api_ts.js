const fs = require('fs');
const path = require('path');

const apiTsPath = 'E:/xampp/htdocs/shopiabd-frontend/src/lib/api.ts';
let apiTs = fs.readFileSync(apiTsPath, 'utf8');

if (!apiTs.includes('getContactSettings')) {
  const contactSettingsCode = `
export interface ApiContactSettings {
  id?: string | number;
  badge_text?: string;
  hero_title?: string;
  hero_subtitle?: string;
  phone?: string;
  secondary_phone?: string;
  email?: string;
  secondary_email?: string;
  whatsapp_number?: string;
  address?: string;
  business_hours_weekday?: string;
  business_hours_weekend?: string;
  response_time_note?: string;
  map_title?: string;
  map_subtitle?: string;
  map_url?: string;
  location_directions?: string;
  form_title?: string;
  form_subtitle?: string;
  form_topics?: string[];
  emergency_notice?: string;
  features?: { icon?: string; title?: string; desc?: string }[];
  support_title?: string;
  support_desc?: string;
  support_phone?: string;
  support_image?: string;
}

export async function getContactSettings() {
  return fetchFromApi<{ success: boolean; data: ApiContactSettings }>("/contact-settings");
}
`;
  apiTs += contactSettingsCode;
  fs.writeFileSync(apiTsPath, apiTs, 'utf8');
  console.log('Successfully added getContactSettings to api.ts');
} else {
  console.log('getContactSettings already exists in api.ts');
}
