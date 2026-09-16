const test = async () => {
  try {
    const res = await fetch('http://127.0.0.1:8000/api/v1/messages', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({
        name: 'John Doe',
        email: 'john@example.com',
        phone: '01812345678',
        subject: 'Order Tracking & Delivery Status',
        message: 'Hello, I would like to check on the delivery timeline for my upcoming shipment.'
      })
    });
    const data = await res.json();
    console.log('Message submission response:', data);
  } catch (e) {
    console.error('Error submitting message:', e);
  }
};
test();
