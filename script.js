setInterval(fetchEmails, 10000); // 10 sec

function fetchEmails() {
    fetch('index.php?action=fetch')
        .then(response => response.text())
        .then(html => {
            document.getElementById('mail-list').innerHTML = html;
        })
        .catch(error => console.error('Erreur fetch:', error));
}
