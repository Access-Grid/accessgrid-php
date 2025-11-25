# AccessGrid PHP SDK

Official PHP SDK for the AccessGrid API.

## Installation

Install via Composer:

```bash
composer require accessgrid/accessgrid-php
```

## Requirements

- PHP 7.4 or higher
- cURL extension
- JSON extension
- Hash extension

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use AccessGrid\AccessGridClient;

// Initialize the client
$client = new AccessGridClient('your-account-id', 'your-secret-key');

// Issue a new access card
$card = $client->getAccessCards()->issue([
    'template_id' => 'your-template-id',
    'full_name' => 'John Doe',
    'expiration_date' => '2024-12-31'
]);

echo "Card issued: " . $card->id . "\n";

// Get a specific card
$card = $client->getAccessCards()->get('0xc4rd1d');
echo "Card ID: " . $card->id . "\n";
echo "State: " . $card->state . "\n";
echo "Full Name: " . $card->full_name . "\n";
echo "Install URL: " . $card->install_url . "\n";
echo "Expiration Date: " . $card->expiration_date . "\n";
echo "Card Number: " . $card->card_number . "\n";
echo "Site Code: " . $card->site_code . "\n";
echo "Devices: " . count($card->devices) . "\n";
echo "Metadata: " . json_encode($card->metadata) . "\n";

// List cards for a template
$cards = $client->getAccessCards()->list('your-template-id');
foreach ($cards as $card) {
    echo $card . "\n";
}

// Suspend a card
$suspendedCard = $client->getAccessCards()->suspend($card->id);
echo "Card suspended: " . $suspendedCard->state . "\n";
```

## API Reference

### AccessGridClient

The main client class for interacting with the AccessGrid API.

#### Constructor

```php
new AccessGridClient(string $accountId, string $secretKey, string $baseUrl = 'https://api.accessgrid.com')
```

### Access Cards Service

Access the access cards service via `$client->getAccessCards()`.

#### Methods

- `issue(array $data): AccessCard` - Issue a new access card
- `provision(array $data): AccessCard` - Alias for issue()
- `get(string $cardId): AccessCard` - Get details about a specific access card
- `update(string $cardId, array $data): AccessCard` - Update an existing card
- `list(string $templateId, ?string $state = null): AccessCard[]` - List cards for a template
- `suspend(string $cardId): AccessCard` - Suspend a card
- `resume(string $cardId): AccessCard` - Resume a suspended card
- `unlink(string $cardId): AccessCard` - Unlink a card
- `delete(string $cardId): AccessCard` - Delete a card

### Console Service

Access the console service via `$client->getConsole()`.

#### Methods

- `createTemplate(array $data): Template` - Create a new card template
- `updateTemplate(string $templateId, array $data): Template` - Update a template
- `readTemplate(string $templateId): Template` - Get template details
- `getLogs(string $templateId, array $params = []): array` - Get event logs

## Error Handling

The SDK throws the following exceptions:

- `AccessGrid\Exceptions\AccessGridException` - Base exception for all API errors
- `AccessGrid\Exceptions\AuthenticationException` - Thrown for authentication failures

```php
try {
    $card = $client->getAccessCards()->issue($data);
} catch (AccessGrid\Exceptions\AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
} catch (AccessGrid\Exceptions\AccessGridException $e) {
    echo "API error: " . $e->getMessage();
}
```

## License

MIT License