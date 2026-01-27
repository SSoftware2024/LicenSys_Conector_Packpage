<?php
namespace TiagoAlves\LicenSysConectorPackpage\Services;

final class ConectorService
{
    private string $urlApi = 'https://api.licensys.com.br';
    private string $apiKey;

    public function getUrlApi(): string
    {
        return $this->urlApi;
    }

    public function setUrlApi(string $urlApi, bool $overwrite = false): void
    {
        if(!empty($this->urlApi) && $overwrite === true){
            $this->urlApi = $urlApi;
        }else if(empty($this->urlApi)){
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

    public function connect(): bool
    {
        //enviar para conexao, com chave de api
        //retornar token
        // Lógica de conexão com a API LicenSys
        return true;
    }
}
