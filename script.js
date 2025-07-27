function fetchMails() {
    fetch('?action=fetch')
        .then(res => res.text())
        .then(html => {
            document.getElementById('mail-list').innerHTML = html;
        });
}

setInterval(fetchMails, 10000); // auto-refresh chaque 10 secondes
window.onload = fetchMails;
