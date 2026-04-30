<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WhatsAppService
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;
    protected $endpoint;
    protected $defaultModuleId;
    protected $defaultMenuId;
    protected $defaultTitle;
    protected $defaultDelay;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
        ]);

        $this->baseUrl         = rtrim(config('whatsapp.base_url'), '/');
        $this->apiKey          = config('whatsapp.api_key');
        $this->endpoint        = config('whatsapp.endpoint.notifications');
        $this->defaultModuleId = config('whatsapp.default.master_module_id');
        $this->defaultMenuId   = config('whatsapp.default.master_menu_id');
        $this->defaultTitle    = config('whatsapp.default.title');
        $this->defaultDelay    = config('whatsapp.default.delay');
    }

    protected function getUrl()
    {
        return $this->baseUrl . $this->endpoint;
    }

    protected function send(array $payload)
    {
        try {
            $response = $this->client->request('POST', $this->getUrl(), [
                'headers' => [
                    'Authorization' => $this->apiKey,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
                'json' => $payload,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'message' => 'Whastapp is sent successfully.',
                'data'    => $data,
            ];
        } catch (RequestException $ex) {
            $resp = $ex->getResponse();
            $body = $resp ? (string) $resp->getBody() : null;

            return [
                'success' => false,
                'message' => $ex->getMessage(),
                'error'   => $body,
            ];
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => $ex->getMessage(),
            ];
        }
    }

    public function sendText($recipientPhone, $message, $recipientName = 'Member', $title = null, $delay = null)
    {
        if (empty($recipientPhone)) {
            return [
                'success' => false,
                'message' => 'recipient_phone wajib diisi',
            ];
        }

        if (empty($message)) {
            return [
                'success' => false,
                'message' => 'message wajib diisi',
            ];
        }

        $payload = [
            'master_module_id' => $this->defaultModuleId,
            'master_menu_id'   => $this->defaultMenuId,
            'recipient_name'   => $recipientName,
            'recipient_phone'  => $recipientPhone,
            'type'             => 'text',
            'title'            => $title ?? $this->defaultTitle,
            'message'          => $message,
            'assets'           => null,
            'asset_name'       => null,
            'delay'            => 1,
        ];

        return $this->send($payload);
    }

    public function sendImage($recipientPhone, $message = '', $imageUrl = null, $recipientName = 'Member', $title = null, $delay = null)
    {
        if (empty($recipientPhone)) {
            return [
                'success' => false,
                'message' => 'recipient_phone wajib diisi',
            ];
        }

        $payload = [
            'master_module_id' => $this->defaultModuleId,
            'master_menu_id'   => $this->defaultMenuId,
            'recipient_name'   => $recipientName,
            'recipient_phone'  => $recipientPhone,
            'type'             => !empty($imageUrl) ? 'image' : 'text',
            'title'            => $title ?? $this->defaultTitle,
            'message'          => $message,
            'assets'           => $imageUrl,
            'asset_name'       => !empty($imageUrl) ? basename(parse_url($imageUrl, PHP_URL_PATH)) : null,
            'delay'            => 1,
        ];

        return $this->send($payload);
    }

    public function sendCustom(array $payload)
    {
        $payload['master_module_id'] = $payload['master_module_id'] ?? $this->defaultModuleId;
        $payload['master_menu_id']   = $payload['master_menu_id'] ?? $this->defaultMenuId;
        $payload['title']            = $payload['title'] ?? $this->defaultTitle;
        $payload['delay']            = $payload['delay'] ?? $this->defaultDelay;

        if (empty($payload['recipient_phone'])) {
            return [
                'success' => false,
                'message' => 'recipient_phone wajib diisi',
            ];
        }

        if (!isset($payload['message'])) {
            $payload['message'] = '';
        }

        return $this->send($payload);
    }
}