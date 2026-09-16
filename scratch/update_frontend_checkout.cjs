const fs = require('fs');

// 1. Update CreateOrderPayload in api.ts
const apiPath = 'E:/xampp/htdocs/shopiabd-frontend/src/lib/api.ts';
if (fs.existsSync(apiPath)) {
  let apiContent = fs.readFileSync(apiPath, 'utf8');
  if (!apiContent.includes('coupon_code?: string;')) {
    apiContent = apiContent.replace(
      'shipping_amount?: number;',
      'shipping_amount?: number;\n  coupon_code?: string;\n  coupon_discount?: number;'
    );
    fs.writeFileSync(apiPath, apiContent, 'utf8');
    console.log('Updated CreateOrderPayload in api.ts');
  }
}

// 2. Read & update checkout/page.tsx
const checkoutPath = 'E:/xampp/htdocs/shopiabd-frontend/src/app/checkout/page.tsx';
if (fs.existsSync(checkoutPath)) {
  let checkoutContent = fs.readFileSync(checkoutPath, 'utf8');

  // Update useShop destructured properties
  checkoutContent = checkoutContent.replace(
    'const { cart, user, clearCart, showToast } = useShop();',
    'const { cart, user, clearCart, showToast, appliedCoupon, couponDiscount } = useShop();'
  );

  // Update grandTotal calculation
  checkoutContent = checkoutContent.replace(
    'const grandTotal = subtotal + finalShippingFee;',
    'const grandTotal = Math.max(0, (subtotal - couponDiscount) + finalShippingFee);'
  );

  // Add coupon fields to placeOrder payload
  if (!checkoutContent.includes('coupon_code: appliedCoupon')) {
    checkoutContent = checkoutContent.replace(
      'shipping_amount: finalShippingFee,',
      'shipping_amount: finalShippingFee,\n      coupon_code: (appliedCoupon && couponDiscount > 0) ? appliedCoupon.code : undefined,\n      coupon_discount: couponDiscount > 0 ? couponDiscount : undefined,'
    );
  }

  // Add Discount row in checkout summary if applied
  const subtotalMarker = '<span>৳{subtotal.toLocaleString("en-US", { minimumFractionDigits: 2 })}</span>\n                </div>';
  if (checkoutContent.includes(subtotalMarker) && !checkoutContent.includes('Discount ({appliedCoupon.code})')) {
    const discountRow = `<span>৳{subtotal.toLocaleString("en-US", { minimumFractionDigits: 2 })}</span>
                </div>

                {/* Coupon Discount */}
                {appliedCoupon && couponDiscount > 0 && (
                  <div className="py-2 border-t border-slate-200 flex justify-between font-bold text-emerald-600">
                    <span>Discount ({appliedCoupon.code})</span>
                    <span>-৳{couponDiscount.toLocaleString("en-US", { minimumFractionDigits: 2 })}</span>
                  </div>
                )}`;
    checkoutContent = checkoutContent.replace(subtotalMarker, discountRow);
  }

  fs.writeFileSync(checkoutPath, checkoutContent, 'utf8');
  console.log('Updated checkout/page.tsx');
}
