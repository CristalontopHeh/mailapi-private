<?php
function fetchEmails() {
    $hostname = '{imap.migadu.com:993/imap/ssl}INBOX';
    $username = 'admin@cristalmails.dedyn.io';
    $password = 'cristal-XIUZIX-XIAJDO-XOOAK';
    $webhookUrl = 'https://discord.com/api/webhooks/1399129112957685861/r1XiAuFQqK8D0iFMqN-vpOlEh1bcrw4VAsGpKDDVE7rYjliqPcz_sTiHp5a2snRAj8QL';

    $inbox = @imap_open($hostname, $username, $password);
    if (!$inbox) {
        return [["subject" => "❌ Connexion IMAP échouée", "from" => "Serveur", "body" => imap_last_error()]];
    }

    $emails = [];
    $emails_ids = imap_search($inbox, 'ALL');
    if ($emails_ids) {
        rsort($emails_ids); // plus récents en premier
        foreach (array_slice($emails_ids, 0, 10) as $email_number) {
            $overview = imap_fetch_overview($inbox, $email_number, 0)[0];
            $body = trim(strip_tags(imap_fetchbody($inbox, $email_number, 1)));

            $mail = [
                "subject" => $overview->subject ?? "(Sans sujet)",
                "from" => $overview->from ?? "(Expéditeur inconnu)",
                "body" => mb_substr($body, 0, 1000)
            ];
            $emails[] = $mail;

            sendToDiscord($mail, $webhookUrl);
        }
    }
    imap_close($inbox);
    return $emails;
}

function sendToDiscord($mail, $webhookUrl) {
    $content = "**📧 Nouveau mail reçu**\n\n";
    $content .= "**De :** " . $mail['from'] . "\n";
    $content .= "**Sujet :** " . $mail['subject'] . "\n";
    $content .= "**Contenu :**\n" . mb_substr($mail['body'], 0, 500);

    $payload = json_encode(["content" => $content]);
    $ch = curl_init($webhookUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    curl_exec($ch);
    curl_close($ch);
}
