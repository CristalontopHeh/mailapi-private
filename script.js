async function fetchEmails() {
    const res = await fetch("?action=fetch");
    const mails = await res.json();
    const container = document.getElementById("mail-list");
    container.innerHTML = "";

    mails.forEach(mail => {
        const div = document.createElement("div");
        div.className = "mail";
        div.innerHTML = `
            <h3>${mail.subject}</h3>
            <p><strong>De :</strong> ${mail.from}</p>
            <p>${mail.body.replace(/\n/g, "<br>")}</p>
        `;
        container.appendChild(div);
    });
}

setInterval(fetchEmails, 10000);
fetchEmails();
