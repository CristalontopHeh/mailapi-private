<?php

function sendToDiscord($mail) {
    $webhook = 'https://discord.com/api/webhooks/1399129112957685861/r1XiAuFQqK8D0iFMqN-vpOlEh1bcrw4VAsGpKDDVE7rYjliqPcz_sTiHp5a2snRAj8QL';

    $data = [
        'content' => "**📧 Nouveau mail reçu**\n\n**De :** {$mail['from']}\n**Sujet :** {$mail['subject']}\n**Contenu :**\n" . substr($mail['body'], 0, 500) . '...'
    ];

    $json = json_encode($data);
    $ch = curl_init($webhook);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function fetchEmails() {
    $hostname = '{imap.migadu.com:993/imap/ssl}INBOX';
    $username = 'admin@cristalmails.dedyn.io';
    $password = 'cristal-XIUZIX-XIAJDO-XOOAK';

    $inbox = @imap_open($hostname, $username, $password);
    if (!$inbox) {
        return [['subject' => 'Erreur IMAP', 'from' => 'Système', 'body' => 'Impossible de se connecter à la boîte mail.']];
    }

    $emails = imap_search($inbox, 'ALL');
    $result = [];
    $sent = file_exists('sent.json') ? json_decode(file_get_contents('sent.json'), true) : [];

    if ($emails) {
        rsort($emails);
        foreach ($emails as $msgno) {
            $uid = imap_uid($inbox, $msgno);
            if (in_array($uid, $sent)) continue;

            $header = imap_headerinfo($inbox, $msgno);
            $from = $header->from[0]->mailbox . '@' . $header->from[0]->host;
            $subject = isset($header->subject) ? imap_utf8($header->subject) : '(Sans sujet)';

            // 💥 BLACKLIST
            $blacklisted = ['support@migadu.com'];
            if (in_array(strtolower($from), $blacklisted)) {
                $sent[] = $uid;
                continue;
            }

            $structure = imap_fetchstructure($inbox, $msgno);
            $body = imap_fetchbody($inbox, $msgno, 1);
            if ($structure && isset($structure->encoding)) {
                if ($structure->encoding == 3) {
                    $body = base64_decode($body);
                } elseif ($structure->encoding == 4) {
                    $body = quoted_printable_decode($body);
                }
            }

            $mail = [
                'subject' => $subject,
                'from' => $from,
                'body' => strip_tags($body),
                'uid' => $uid
            ];

            $result[] = $mail;
            sendToDiscord($mail);
            $sent[] = $uid;
        }
    }

    file_put_contents('sent.json', json_encode($sent));
    imap_close($inbox);
    return $result;
}
