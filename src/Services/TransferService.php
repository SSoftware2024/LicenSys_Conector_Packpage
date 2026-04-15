<?php

namespace TiagoAlves\LicenSysConectorPackpage\Services;

use TiagoAlves\LicenSysConectorPackpage\Utils\LicenSysHttpUtils;

final class TransferService
{
    private LicenSysHttpUtils $httpLicenSys;
    public function __construct()
    {
        $this->httpLicenSys = new LicenSysHttpUtils('1|kOU9ETzGcIYSIPnLdzerPUVnULw9sU2rWiqw8DB5d6ea678b');
    }
    public function send($company_uuid, $historic_company_id, $pix_origin_name, $file) 
    {
        $realPath = $file->getRealPath();
        $new_name_ext = $file->hashName();
        $http = $this->httpLicenSys->getHttpWithHeaders();
        $response = $http->attach(
            'pix_receipt_photo', file_get_contents($realPath), $new_name_ext
        )->post('transfer/proccess',[
            'company_uuid' => $company_uuid,
            'historic_company_id' => $historic_company_id,
            'pix_origin_name' => $pix_origin_name,
        ]); 
        return $response->successful()?$response->json():$response->body();
    }

    public function getQrCodePix(int $historic_company_id)
    {
        $http = $this->httpLicenSys->getHttpWithHeaders();
        $response = $http->get('transfer/getQrCodePix',[
            'historic_company_id' => $historic_company_id,
        ]);
        return $response->json();
    }
}
