<?php

namespace Mydansun\Sendcloud;

use GuzzleHttp\Client;
use Mydansun\Sendcloud\Exceptions\BadResponseDataException;
use Mydansun\Sendcloud\Exceptions\OperationFailedException;

class Sendcloud
{
    protected $apiUser;
    protected $apiKey;
    protected $from;
    protected $client;

    public function __construct($apiUser, $apiKey, $from)
    {
        $this->apiUser = $apiUser;
        $this->apiKey = $apiKey;
        $this->from = $from;
        $this->client = new Client([
            'base_uri' => 'http://api.sendcloud.net/apiv2/',
            'timeout' => 5.0,
        ]);
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * 构造提交参数
     * @param array $data
     * @return array
     */
    protected function payload(array $data)
    {
        return array_merge($data, [
            'apiUser' => $this->apiUser,
            'apiKey' => $this->apiKey,
            'from' => $this->from
        ]);
    }

    /**
     * @param $to
     * @param $subject
     * @param $html
     * @return array
     * @throws OperationFailedException
     */
    public function sendMail($to, $subject, $html)
    {
        if (is_array($to)) {
            $toList = $to;
        } else {
            $toList = [strval($to)];
        }

        return $this->post("mail/send", [
            'to' => join(";", $toList),
            'subject' => $subject,
            'html' => $html
        ]);

    }

    /**
     * 发送列表模板邮件
     * @param string $addressListName
     * @param $templateName
     * @return array
     * @throws OperationFailedException
     */
    public function sendTemplateMail(string $addressListName, $templateName)
    {
        return $this->post("mail/sendtemplate", [
            'to' => $addressListName,
            'templateInvokeName' => $templateName,
            'useAddressList' => 'true'
        ]);
    }

    /**
     * @param $endpoint
     * @param array $data
     * @return array
     * @throws OperationFailedException
     */
    protected function post($endpoint, array $data)
    {
        $response = $this->client->post($endpoint, [
            'form_params' => $this->payload($data)
        ]);
        $responseData = json_decode($response->getBody()->getContents(), true);
        if (
            !isset($responseData['info']) ||
            !isset($responseData['result']) ||
            !isset($responseData['statusCode']) ||
            !isset($responseData['message']) ||
            !is_array($responseData['info'])
        ) {
            throw new BadResponseDataException($responseData);
        }
        if (!$responseData['result']) {
            throw new OperationFailedException($responseData['statusCode'], $responseData['message']);
        }
        return $responseData['info'];
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }
}