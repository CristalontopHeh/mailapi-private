<?php
include 'imap.php';

$emails = fetchEmails();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>📬 CristalMail</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>📥 CristalMail - Boîte de réception</h1>
    <p>Les e-mails arrivent en temps réel, et sont envoyés sur Discord !</p>
    <div id="mail-list">
        <?php foreach ($emails as $mail): ?>
            <div class="mail">
                <h3><?= htmlspecialchars($mail['subject']) ?></h3>
                <p><strong>De :</strong> <?= htmlspecialchars($mail['from']) ?></p>
                <p><?= nl2br(htmlspecialchars($mail['body'])) ?></p>
            </div><hr>
        <?php endforeach; ?>
    </div>
</body>
</html>
