document.getElementById('order-now').addEventListener('click', function() {
    const pizzaQty = parseInt(document.getElementById('pizza').value);
    const rotiQty = parseInt(document.getElementById('roti').value);
    const riceQty = parseInt(document.getElementById('rice').value);
    const totalPaidInput = document.getElementById('total-paid');

    const pizzaPrice = 1500;
    const rotiPrice = 1500;
    const ricePrice = 500;

    const pizzaTotal = pizzaQty * pizzaPrice;
    const rotiTotal = rotiQty * rotiPrice;
    const riceTotal = riceQty * ricePrice;

    const totalPayable = pizzaTotal + rotiTotal + riceTotal;

    const date = new Date();
    const formattedDate = date.toISOString().split('T')[0];
    const formattedTime = date.toLocaleTimeString();
    const efNo = Math.floor(Math.random() * 100) + 1;

    document.getElementById('ef-no').innerText = efNo;
    document.getElementById('payment-date').innerText = formattedDate;
    document.getElementById('payment-time').innerText = formattedTime;

    const receiptBody = document.getElementById('receipt-body');
    receiptBody.innerHTML = '';
    if (pizzaQty > 0) {
        receiptBody.innerHTML += `<tr><td>Pizza</td><td>${pizzaQty}</td><td>LKR ${pizzaTotal}</td></tr>`;
    }
    if (rotiQty > 0) {
        receiptBody.innerHTML += `<tr><td>Roti</td><td>${rotiQty}</td><td>LKR ${rotiTotal}</td></tr>`;
    }
    if (riceQty > 0) {
        receiptBody.innerHTML += `<tr><td>Rice</td><td>${riceQty}</td><td>LKR ${riceTotal}</td></tr>`;
    }

    document.getElementById('total-payable').innerText = totalPayable;

    totalPaidInput.addEventListener('input', function() {
        const totalPaid = parseInt(this.value);
        const balance = totalPaid - totalPayable;
        document.getElementById('balance').innerText = balance >= 0 ? balance : '0';
    });

    const formData = new FormData();
    formData.append('pizza', pizzaQty);
    formData.append('roti', rotiQty);
    formData.append('rice', riceQty);
    formData.append('total_paid', parseInt(totalPaidInput.value));

    fetch('order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error(data.error);
            return;
        }
        // Update receipt with server response
        document.getElementById('ef-no').innerText = data.ef_no;
        document.getElementById('payment-date').innerText = data.payment_date.split(' ')[0];
        document.getElementById('payment-time').innerText = data.payment_date.split(' ')[1];
        totalPaidInput.value = data.total_paid;
        document.getElementById('balance').innerText = data.balance;
    })
    .catch(error => console.error('Error:', error));
});
