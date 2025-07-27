<?php

function fetchEmails() {
    $hostname = '{imap.migadu.com:993/imap/ssl}INBOX';
    $username = 'admin@cristalmails.dedyn.io';
    $password = 'cristal-XIUZIX-XIAJDO-XOOAK';
    $webhook = 'https://discord.com/api/webhooks/1399129112957685861/r1XiAuFQqK8D0iFMqN-vpOlEh1bcrw4VAsGpKDDVE7rYjliqPcz_sTiHp5a2snRAj8QL';

    $inbox = @imap_open($hostname, $username, $password);
    if (!$inbox) return [];

    $emails = imap_search($inbox, 'ALL');
    $results = [];

    if ($emails) {
        rsort($emails);
        foreach ($emails as $email_number) {
            $overview = imap_fetch_overview($inbox, $email_number, 0)[0];
            $message = imap_fetchbody($inbox, $email_number, 1);
            $message = substr(trim($message), 0, 500); // raccourcir
            $mail = [
                'subject' => $overview->subject ?? '(Sans sujet)',
                'from' => $overview->from ?? '(Inconnu)',
                'body' => $message
            ];
            $results[] = $mail;

            // Discord envoi
            $json = json_encode([
                'content' => "**📧 Nouveau mail reçu**\n**De :** {$mail['from']}\n**Sujet :** {$mail['subject']}\n**Contenu :**\n" . $mail['body']
            ]);
            $ch = curl_init($webhook);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    imap_close($inbox);
    return $results;
}
