<?php
include 'send_to_discord.php';

function fetchEmails() {
    $hostname = '{imap.migadu.com:993/imap/ssl}INBOX';
    $username = 'admin@cristalmails.dedyn.io';
    $password = 'cristal-XIUZIX-XIAJDO-XOOAK';

    $inbox = @imap_open($hostname, $username, $password)
        or die('❌ Connexion IMAP échouée : vérifie les identifiants.');

    $emails = imap_search($inbox, 'ALL');
    $output = [];

    if ($emails) {
        rsort($emails);
        foreach ($emails as $email_number) {
            $overview = imap_fetch_overview($inbox, $email_number, 0)[0];
            $body = imap_fetchbody($inbox, $email_number, 1.1);
            if (trim($body) === '') {
                $body = imap_fetchbody($inbox, $email_number, 1);
            }
            $message = [
                'from' => $overview->from,
                'subject' => $overview->subject,
                'body' => quoted_printable_decode($body)
            ];
            $output[] = $message;
            sendToDiscord($message);
        }
    }

    imap_close($inbox);
    return $output;
}
