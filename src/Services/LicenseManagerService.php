<?php

namespace TiagoAlves\LicenSysConectorPackpage\Services;

use TiagoAlves\LicenSysConectorPackpage\Utils\LicenSysHttpUtils;

final class LicenseManagerService
{
    private LicenSysHttpUtils $httpLicenSys;

    public function __construct(string $bearerToken)
    {
        $this->httpLicenSys = new LicenSysHttpUtils($bearerToken);
    }

    public function getDataLicense(string $company_uuid)
    {
        $http = $this->httpLicenSys->getHttpWithHeaders();
        $response = $http->get('license_manager/getDataLicense', [
            'uuid' => $company_uuid,
        ]);

        return $response->successful() ? $response->json() : $response->body();
    }

    public function getMonths(string $company_uuid, int $month = 0, string|int $year = 'all', string|array $monthly_fee = 'all')
    {
        $http = $this->httpLicenSys->getHttpWithHeaders();
        $response = $http->get('license_manager/getMonths', [
            'uuid' => $company_uuid,
            'month' => $month,
            'year' => $year,
            'monthly_fee' => $monthly_fee,
        ]);

        return $response->successful() ? $response->json() : $response->body();
    }
}
