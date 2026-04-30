# LicenSys Conector Packpage

Pacote Laravel para integração com a API LicenSys.

## Instalação

```bash
composer require tiagoalves/licensys-conector-packpage
```

## Configuração

### 1. Variáveis de Ambiente

Adicione ao arquivo `.env`:

```env
LICENSYS_API_URL=https://api.licensys.com.br
LICENSYS_API_KEY=sua_api_key_aqui
```

### 2. Registrar o Provider

O provider já está registrado em `bootstrap/providers.php`:

```php
return [
    App\Providers\AppServiceProvider::class,
    TiagoAlves\LicenSysConectorPackpage\Providers\RootProvider::class,
];
```

### 3. Publicar Configuração (Opcional)

```bash
php artisan vendor:publish --provider="TiagoAlves\LicenSysConectorPackpage\Providers\RootProvider" --tag="licensys_conector_packpage"
```

---

## Arquitetura dos Serviços

```
TiagoAlves\LicenSysConectorPackpage\
├── config/
│   └── licensys_api.php          # Configurações da API
├── Services/
│   ├── ConectorService.php       # Conexão e autenticação
│   ├── LicenseManagerService.php # Gestão de licenças
│   └── TransferService.php       # Transferências e PIX
```

---

## Configuração da API

| Parâmetro | Variável de Ambiente | Descrição |
|-----------|---------------------|------------|
| `api_url` | `LICENSYS_API_URL` | URL base da API LicenSys |
| `api_key_crypt` | `LICENSYS_API_KEY` | Chave de API para autenticação |

---

## Serviços

### 1. ConectorService

**Namespace:** `TiagoAlves\LicenSysConectorPackpage\Services\ConectorService`

**Descrição:** Gerencia a conexão inicial e autenticação com a API. Utiliza credenciais estáticas (URL e API Key).

#### Métodos

| Método | Parâmetros | Retorno | Descrição |
|--------|------------|---------|-----------|
| `getUrlApi()` | - | `string` | Retorna a URL da API |
| `setUrlApi(string $urlApi, bool $overwrite = false)` | `$urlApi`, `$overwrite` | `void` | Define a URL da API |
| `getApiKey()` | - | `string` | Retorna a API Key |
| `setApiKey(string $apiKey, bool $overwrite = false)` | `$apiKey`, `$overwrite` | `void` | Define a API Key |
| `checkUrl()` | - | `bool` | Verifica se a URL da API está acessível |
| `connect(string $uuid)` | `$uuid` | `array` | Conecta à API com o UUID da empresa |
| `revokeMyTokens(string $uuid)` | `$uuid` | `array` | Revoga os tokens da sessão |

#### Uso

```php
use TiagoAlves\LicenSysConectorPackpage\Services\ConectorService;

$connector = new ConectorService();

// Verificar conexão
if ($connector->checkUrl()) {
    // Conectar com UUID da empresa
    $response = $connector->connect('uuid-da-empresa');
}

// Revogar tokens
$connector->revokeMyTokens('uuid-da-empresa');
```



### 2. LicenseManagerService

**Namespace:** `TiagoAlves\LicenSysConectorPackpage\Services\LicenseManagerService`

**Descrição:** Gerencia operações relacionadas a licenças e dados de empresas. Requer Bearer Token válido (obtido via `ConectorService::connect()`).

#### Construtor

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `$bearerToken` | `string` | Token de autenticação Bearer |

#### Métodos

| Método | Parâmetros | Retorno | Endpoint API |
|--------|------------|---------|--------------|
| `getDataLicense(string $company_uuid)` | `$company_uuid` - UUID da empresa | `array` | `GET /license_manager/getDataLicense` |
| `getMonths(string $company_uuid, int $month = 0, string\|int $year = 'all', string\|array $monthly_fee = 'all')` | `$company_uuid`, `$month`, `$year`, `$monthly_fee` | `array` | `GET /license_manager/getMonths` |

#### Uso

```php
use TiagoAlves\LicenSysConectorPackpage\Services\ConectorService;
use TiagoAlves\LicenSysConectorPackpage\Services\LicenseManagerService;

// 1. Obter token via ConectorService
$connector = new ConectorService();
$connectResponse = $connector->connect('uuid-da-empresa');
$bearerToken = $connectResponse['token']; // Depends da resposta da API

// 2. Usar LicenseManagerService com o token
$licenseManager = new LicenseManagerService($bearerToken);

// Obter dados da licença
$licenseData = $licenseManager->getDataLicense('uuid-da-empresa');

// Obter meses/anos
$months = $licenseManager->getMonths('uuid-da-empresa', 0, 'all', 'all');
```

---

### 3. TransferService

**Namespace:** `TiagoAlves\LicenSysConectorPackpage\Services\TransferService`

**Descrição:** Gerencia transferências e operações de PIX. Requer Bearer Token válido.

#### Construtor

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `$bearerToken` | `string` | Token de autenticação Bearer |

#### Métodos

| Método | Parâmetros | Retorno | Endpoint API |
|--------|------------|---------|--------------|
| `send($company_uuid, $historic_company_id, $pix_origin_name, $file)` | `$company_uuid`, `$historic_company_id`, `$pix_origin_name`, `$file` (UploadFile) | `array` | `POST /transfer/proccess` |
| `getQrCodePix(int $historic_company_id)` | `$historic_company_id` | `array` | `GET /transfer/getQrCodePix` |

#### Uso

```php
use TiagoAlves\LicenSysConectorPackpage\Services\TransferService;

// Requer Bearer Token (obtido via ConectorService)
$transferService = new TransferService($bearerToken);

// Enviar transferência com comprovante PIX
$response = $transferService->send(
    'uuid-da-empresa',           // company_uuid
    123,                        // historic_company_id
    'Nome do titular PIX',       // pix_origin_name
    $request->file('comprovante') // Arquivo do comprovante
);

// Obter QR Code PIX
$qrCode = $transferService->getQrCodePix(123);
```

---

## Fluxo de Autenticação

```
┌─────────────────────┐
│   ConectorService   │
│  (API Key + URL)    │
└─────────┬───────────┘
          │
          ▼
   connect(uuid)
          │
          ▼
   ┌──────────────┐
   │ Retorna      │
   │ Bearer Token │
   └──────────────┘
          │
          ▼
┌─────────────────────────────────┐
│  LicenseManagerService          │
│  ou                             │
│  TransferService               │
│  (Bearer Token)                │
└─────────────────────────────────┘
```

---

## Endpoints da API

| Serviço | Método | Endpoint | Descrição |
|---------|--------|----------|-----------|
| Conector | POST | `/connect` | Autentica e obtém Bearer Token |
| Conector | POST | `/revokeMyTokens` | Revoga tokens da sessão |
| Conector | GET | `/check-api` | Verifica disponibilidade da API |
| LicenseManager | GET | `/license_manager/getDataLicense` | Obtém dados da licença |
| LicenseManager | GET | `/license_manager/getMonths` | Obtém meses/anos de licença |
| Transfer | POST | `/transfer/proccess` | Envia transferência com arquivo |
| Transfer | GET | `/transfer/getQrCodePix` | Obtém QR Code PIX |

---

## Exceptions

| Serviço | Exception | Condição |
|---------|-----------|----------|
| ConectorService | `\Exception` | API URL não configurada |
| ConectorService | `\Exception` | API Key não configurada |
| LicenSysHttpUtils | `\Exception` | Bearer Token não definido |

---

## Exemplo Completo

```php
use TiagoAlves\LicenSysConectorPackpage\Services\ConectorService;
use TiagoAlves\LicenSysConectorPackpage\Services\LicenseManagerService;
use TiagoAlves\LicenSysConectorPackpage\Services\TransferService;

// 1. Conectar
$connector = new ConectorService();
$response = $connector->connect('uuid-empresa-123');
$token = $response['token'] ?? null;

if ($token) {
    // 2. Consultar licença
    $licenseService = new LicenseManagerService($token);
    $licenseData = $licenseService->getDataLicense('uuid-empresa-123');
    
    // 3. Ver meses
    $months = $licenseService->getMonths('uuid-empresa-123', 0, 'all', 'all');
    
    // 4. Enviar transferência
    $transferService = new TransferService($token);
    $result = $transferService->send(
        'uuid-empresa-123',
        1,
        'Nome Titular',
        $file
    );
}
```