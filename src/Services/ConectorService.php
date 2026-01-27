<?php

namespace TiagoAlves\LicenSysConectorPackpage\Services;

use Illuminate\Support\Facades\Http;

final class ConectorService
{
    private string $urlApi = '';
    private string $apiKey = '';

    public function __construct()
    {
        $this->urlApi = env('LICENSYS_API_URL', 'http://127.0.0.1:9000/api/');
        $this->apiKey = env('LICENSYS_API_KEY','teste');
    }

    public function getUrlApi(): string
    {
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
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function checkUrl(): bool
    {
        $headers = true;
        try {
            get_headers($this->urlApi . 'check-api');
        } catch (\Throwable $th) {
            $headers = false;
        }
        return $headers;
    }

    public function connect()
    {
        if ($this->checkUrl() === true) {
            $response = Http::get("{$this->urlApi}connect");
            //enviar para conexao, com chave de api, uuid
            //retornar token
            // Lógica de conexão com a API LicenSys
            ds($response->json());
        }
    }
}
