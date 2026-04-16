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

    public function getDataLicense() {}
    public function getMonths() {}
}
