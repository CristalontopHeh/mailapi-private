<?php
include 'imap.php';

if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $emails = fetchEmails();
    foreach ($emails as $mail) {
        echo "<div class='mail'>";
        echo "<h3>" . htmlspecialchars($mail['subject']) . "</h3>";
        echo "<p><strong>De :</strong> " . htmlspecialchars($mail['from']) . "</p>";
        echo "<p>" . nl2br(htmlspecialchars($mail['body'])) . "</p>";
        echo "</div><hr>";
    }
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
    <div id="mail-list">
        <?php
        $emails = fetchEmails();
        foreach ($emails as $mail) {
            echo "<div class='mail'>";
            echo "<h3>" . htmlspecialchars($mail['subject']) . "</h3>";
            echo "<p><strong>De :</strong> " . htmlspecialchars($mail['from']) . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($mail['body'])) . "</p>";
            echo "</div><hr>";
        }
        ?>
    </div>
</body>
</html>
