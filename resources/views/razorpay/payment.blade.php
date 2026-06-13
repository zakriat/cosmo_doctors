<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "{{ $key }}",
        "amount": "{{ $amount }}",
        "currency": "{{ $currency }}",
        "name": "{{ $name }}",
        "description": "{{ $description }}",
        "order_id": "{{ $order_id }}",
        "handler": function (response){
            window.location.href = "/payment/success?gateway=razorpay&razorpay_payment_id=" + response.razorpay_payment_id;
        },
        "prefill": {},
        "theme": {
            "color": "#3399cc"
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.open();
</script>