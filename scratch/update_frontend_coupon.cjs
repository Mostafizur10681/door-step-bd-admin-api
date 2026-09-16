const fs = require('fs');

const apiTsPath = 'E:/xampp/htdocs/shopiabd-frontend/src/lib/api.ts';
if (fs.existsSync(apiTsPath)) {
  let c = fs.readFileSync(apiTsPath, 'utf8');
  if (!c.includes('validateAndApplyCoupon')) {
    const snippet = `

// ── Coupon Validation & Apply ──
export interface ApiCouponResult {
  id: number;
  code: string;
  name?: string;
  description?: string;
  discount_type: "percentage" | "fixed";
  discount_value: number;
  discount_amount: number;
  formatted_discount: string;
  discount_label: string;
  subtotal: number;
  new_subtotal: number;
  minimum_order_amount?: number | null;
  maximum_discount?: number | null;
  starts_at?: string | null;
  expires_at?: string | null;
}

export async function validateAndApplyCoupon(code: string, subtotal: number, userId?: number | null): Promise<{ success: boolean; message: string; data?: ApiCouponResult }> {
  try {
    const res = await fetch(\`\${API_V1}/coupons/apply\`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({
        code,
        subtotal,
        user_id: userId || undefined
      })
    });
    const data = await res.json();
    return data;
  } catch (err: any) {
    return {
      success: false,
      message: err.message || "Failed to validate coupon"
    };
  }
}
`;
    c += snippet;
    fs.writeFileSync(apiTsPath, c, 'utf8');
    console.log('Successfully updated api.ts');
  } else {
    console.log('api.ts already has validateAndApplyCoupon');
  }
}
