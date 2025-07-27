<?php

function fetchEmails() {
    $hostname = '{imap.migadu.com:993/imap/ssl}INBOX';
    $username = 'admin@cristalmails.dedyn.io';
    $password = 'cristal-XIUZIX-XIAJDO-XOOAK';

    $inbox = @imap_open($hostname, $username, $password) or die('❌ Connexion IMAP échouée.');

    $emails = imap_search($inbox, 'ALL');
    $result = [];

    if ($emails) {
        rsort($emails); // du plus récent au plus ancien
        $sentIds = file_exists('sent.json') ? json_decode(file_get_contents('sent.json'), true) : [];

        foreach ($emails as $mailId) {
            $header = imap_headerinfo($inbox, $mailId);
            $uid = imap_uid($inbox, $mailId);

            if (in_array($uid, $sentIds)) {
                continue; // déjà envoyé
            }

            $structure = imap_fetchstructure($inbox, $mailId);
            $body = imap_fetchbody($inbox, $mailId, 1);
            if ($structure->encoding == 3) {
                $body = base64_decode($body);
            } elseif ($structure->encoding == 4) {
                $body = quoted_printable_decode($body);
            }

            $subject = isset($header->subject) ? imap_utf8($header->subject) : '(Sans sujet)';
            $from = isset($header->fromaddress) ? imap_utf8($header->fromaddress) : '(Inconnu)';

            $mailData = [
                'subject' => $subject,
                'from' => $from,
                'body' => mb_strimwidth(strip_tags($body), 0, 300, '...'),
                'uid' => $uid
            ];

            $result[] = $mailData;
            sendToDiscord($mailData);

            $sentIds[] = $uid;
        }

        file_put_contents('sent.json', json_encode($sentIds));
    }

    imap_close($inbox);
    return $result;
}

function sendToDiscord($mail) {
    $webhook = "https://discord.com/api/webhooks/1399129112957685861/r1XiAuFQqK8D0iFMqN-vpOlEh1bcrw4VAsGpKDDVE7rYjliqPcz_sTiHp5a2snRAj8QL";

    $message = "**📧 Nouveau mail reçu**\n\n" .
               "**De :** " . $mail['from'] . "\n" .
               "**Sujet :** " . $mail['subject'] . "\n" .
               "**Contenu :**\n" . $mail['body'];

    $payload = json_encode(["content" => $message]);

    $ch = curl_init($webhook);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
