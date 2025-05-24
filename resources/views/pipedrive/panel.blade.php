<div id="invoices">Loading invoices...</div>

<!-- Include Pipedrive SDK -->
<script src="https://cdn.pipedriveassets.com/webapp/js/api/v1/api.js"></script>

<script>
  Pipedrive.execute(() => {
    Pipedrive.Application.persons.get().then(person => {
      const email = person.email ? person.email[0].value : null;
      if (!email) {
        document.getElementById('invoices').innerText = 'No email found';
        return;
      }

      fetch('/panel?email=' + encodeURIComponent(email))
        .then(res => res.json())
        .then(data => {
          if (data.error) {
            document.getElementById('invoices').innerText = 'Error: ' + data.error;
            return;
          }

          let html = '<ul>';
          data.invoices.forEach(inv => {
            html += `<li>Invoice ID: ${inv.id}, Paid: ${inv.amount_paid}, Due: ${inv.amount_due}</li>`;
          });
          html += '</ul>';

          document.getElementById('invoices').innerHTML = html;
        })
        .catch(err => {
          document.getElementById('invoices').innerText = 'Failed to load invoices';
        });
    });
  });
</script>
