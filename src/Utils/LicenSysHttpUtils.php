<?php

namespace TiagoAlves\LicenSysConectorPackpage\Utils;

use Illuminate\Support\Facades\Http;
use TiagoAlves\LicenSysConectorPackpage\Services\ConectorService;

class LicenSysHttpUtils
{
    private string $bearerToken = '';
    private ConectorService $connector;

    public function __construct(string $bearerToken)
    {
        $this->connector = new ConectorService();
        $this->setBearerToken($bearerToken);
    }

    public function setBearerToken(string $bearerToken)
    {
        $this->bearerToken = $bearerToken;
    }
    public function getBearerToken()
    {
        throw_if(empty($this->bearerToken), \Exception::class, 'Token não definido');
        return $this->bearerToken;
    }
    public function getHttpWithHeaders()
    {
        return Http::baseUrl($this->connector->getUrlApi())
            ->withHeaders($this->defaultHeaders());
    }

    private function defaultHeaders(){
        return [
            'Authorization' => "Bearer {$this->getBearerToken()}",
            'Accept' => 'application/json'
        ];
    }
}
