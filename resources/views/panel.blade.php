<!DOCTYPE html>
<html>
<head>
  <title>Stripe Transactions</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 14px; padding: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
  </style>
</head>
<body>
  <div id="app">
    <h3>Stripe Transactions</h3>
    <p>Loading data...</p>
  </div>

  <script>
    window.pipedriveApp.on('context', async function(context) {
      try {
        const email = context.person.email[0].value;

        const response = await fetch(`/api/stripe-transactions?email=${email}`);
        const data = await response.json();

        let html = '<table><tr><th>ID</th><th>Amount</th><th>Status</th><th>Date</th><th>Receipt</th></tr>';

        data.forEach(txn => {
          html += `<tr>
            <td>${txn.id}</td>
            <td>${txn.amount}</td>
            <td>${txn.status}</td>
            <td>${txn.date}</td>
            <td>${txn.receipt ? `<a href="${txn.receipt}" target="_blank">View</a>` : '-'}</td>
          </tr>`;
        });

        html += '</table>';
        document.getElementById('app').innerHTML = html;

      } catch (err) {
        document.getElementById('app').innerHTML = `<p style="color:red;">Error loading data: ${err.message}</p>`;
      }
    });
  </script>

  <script src="https://app.pipedrive.com/pd-app.js"></script>
</body>
</html>
