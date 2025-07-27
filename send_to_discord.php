<?php
function sendToDiscord($email) {
    $webhook = 'https://discord.com/api/webhooks/1399129112957685861/r1XiAuFQqK8D0iFMqN-vpOlEh1bcrw4VAsGpKDDVE7rYjliqPcz_sTiHp5a2snRAj8QL';

    $data = [
        "content" => "**📧 Nouveau mail reçu**\n\n**De :** " . $email['from'] . "\n**Sujet :** " . $email['subject'] . "\n**Contenu :**\n" . substr($email['body'], 0, 1500)
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json",
            'content' => json_encode($data)
        ]
    ];

    file_get_contents($webhook, false, stream_context_create($options));
}
