<?php

namespace Mydansun\Sendcloud\Transport;

use Mydansun\Sendcloud\Exceptions\OperationFailedException;
use Mydansun\Sendcloud\Sendcloud;
use Swift_Mime_SimpleMessage;
use Illuminate\Mail\Transport\Transport;

class SendcloudTransport extends Transport
{
    /**
     * Sendcloud instance.
     *
     * @var Sendcloud
     */
    protected $client;
    /**
     * @var string
     */
    protected $unSubTemplate;

    /**
     * Create a new Sendcloud transport instance.
     *
     * @param Sendcloud $client
     * @param string $unSubTemplate
     */
    public function __construct(Sendcloud $client, $unSubTemplate)
    {
        $this->client = $client;
        $this->unSubTemplate = $unSubTemplate;
    }

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @param null $failedRecipients
     * @return int
     * @throws OperationFailedException
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $to = $this->getTo($message);

        $message->setBcc([]);

        $html = $message->getBody();
        if (!empty($this->unSubTemplate)) {
            $html .= $this->unSubTemplate;
        }
        $this->client->sendMail($to, $message->getSubject(), $html);

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get the "to" payload field for the API request.
     *
     * @param \Swift_Mime_SimpleMessage $message
     * @return array
     */
    protected function getTo(Swift_Mime_SimpleMessage $message)
    {
        return collect($this->allContacts($message))->map(function ($display, $address) {
            return $address;
        })->values()->toArray();
    }

    /**
     * Get all of the contacts for the message.
     *
     * @param \Swift_Mime_SimpleMessage $message
     * @return array
     */
    protected function allContacts(Swift_Mime_SimpleMessage $message)
    {
        return array_merge(
            (array)$message->getTo(), (array)$message->getCc(), (array)$message->getBcc()
        );
    }
}
