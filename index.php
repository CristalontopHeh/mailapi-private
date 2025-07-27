<?php
include 'imap.php';

if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $emails = fetchEmails();
    header('Content-Type: application/json');
    echo json_encode($emails);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>📬 CristalMail</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <h1>📥 CristalMail - Boîte de réception</h1>
    <p>Les e-mails arrivent en temps réel, et sont envoyés sur Discord !</p>
    <div id="mail-list"></div>
</body>
</html>
