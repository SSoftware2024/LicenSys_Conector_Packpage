<?php

namespace TiagoAlves\LicenSysConectorPackpage\Services;

use Illuminate\Support\Facades\Http;

final class ConectorService
{
    private string $urlApi = '';
    private string $apiKey = '';

    public function __construct()
    {
        $this->urlApi = config('licensys_api.api_url');
        $this->apiKey = config('licensys_api.api_key_crypt');
    }

    public function getUrlApi(): string
    {
        if(empty($this->urlApi)){
            throw new \Exception("API URL not set.");
        }
        return $this->urlApi;
    }

    public function setUrlApi(string $urlApi, bool $overwrite = false): void
    {
        if (!empty($this->urlApi) && $overwrite === true) {
            $this->urlApi = $urlApi;
        } else if (empty($this->urlApi)) {
            $this->urlApi = $urlApi;
        }
    }

    public function getApiKey(): string
    {
        if(empty($this->apiKey)){
            throw new \Exception("API Key not set.");
        }
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey, bool $overwrite = false): void
    {
        if (!empty($this->apiKey) && $overwrite === true) {
            $this->apiKey = $apiKey;
        } else if (empty($this->apiKey)) {
            $this->apiKey = $apiKey;
        }
    }

    public function checkUrl(): bool
    {
        $headers = true;
        try {
            get_headers($this->getUrlApi() . 'check-api');
        } catch (\Throwable $th) {
            $headers = false;
        }
        return $headers;
    }

    public function connect(string $uuid)
    {
        if ($this->checkUrl() === true && !empty($uuid)) {
            $response = Http::post($this->getUrlApi()."connect", [
                'uuid' => $uuid,
                'api_key_crypt' => $this->getApiKey(),
            ]);
            return $response->json();
        }
    }
}
