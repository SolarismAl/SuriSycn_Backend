<?php

namespace App\Mail\Transport;

use Google\Client as Google_Client;
use Google\Service\Gmail as Google_Service_Gmail;
use Google\Service\Gmail\Message as Google_Service_Gmail_Message;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class GmailApiTransport extends AbstractTransport
{
    protected Google_Client $client;

    public function __construct(array $config, ?EventDispatcherInterface $dispatcher = null, ?LoggerInterface $logger = null)
    {
        parent::__construct($dispatcher, $logger);

        $this->client = new Google_Client();
        $this->client->setClientId($config['client_id'] ?? '');
        $this->client->setClientSecret($config['client_secret'] ?? '');
        $this->client->refreshToken($config['refresh_token'] ?? '');
        $this->client->setAccessType('offline');
    }

    protected function doSend(SentMessage $message): void
    {
        $service = new Google_Service_Gmail($this->client);
        $gmailMessage = new Google_Service_Gmail_Message();

        $rawMessage = $message->toString();
        // The Gmail API requires base64url encoding without trailing '='
        $base64Message = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');

        $gmailMessage->setRaw($base64Message);
        
        $service->users_messages->send('me', $gmailMessage);
    }

    public function __toString(): string
    {
        return 'gmail-api';
    }
}
