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

### Initializing the Client

```php
<?php

require 'vendor/autoload.php';

use AccessGrid\Client;

$accountId = $_ENV['ACCOUNT_ID'];
$secretKey = $_ENV['SECRET_KEY'];

$client = new Client($accountId, $secretKey);
```

### Issuing an Access Card

```php
$card = $client->accessCards->provision([
    'card_template_id' => '0xd3adb00b5',
    'employee_id' => '123456789',
    'tag_id' => 'DDEADB33FB00B5',
    'full_name' => 'Employee name',
    'email' => 'employee@yourwebsite.com',
    'phone_number' => '+19547212241',
    'classification' => 'full_time',
    'department' => 'Engineering',
    'location' => 'San Francisco',
    'site_name' => 'HQ Building A',
    'workstation' => '4F-207',
    'mail_stop' => 'MS-401',
    'company_address' => '123 Main St, San Francisco, CA 94105',
    'start_date' => (new DateTime('now', new DateTimeZone('UTC')))->format('c'),
    'expiration_date' => '2026-04-01T00:00:00.000Z',
    'employee_photo' => '[image_in_base64_encoded_format]',
    'title' => 'Engineering Manager',
    'metadata' => [
        'department' => 'engineering',
        'badge_type' => 'contractor'
    ]
]);

echo "Install URL: {$card->url}\n";
```

### Getting an Access Card

```php
$card = $client->accessCards->get('0xc4rd1d');

echo "Card ID: {$card->id}\n";
echo "State: {$card->state}\n";
echo "Full Name: {$card->full_name}\n";
echo "Install URL: {$card->install_url}\n";
echo "Expiration Date: {$card->expiration_date}\n";
echo "Card Number: {$card->card_number}\n";
echo "Site Code: {$card->site_code}\n";
echo "Devices: " . count($card->devices) . "\n";
echo "Metadata: " . json_encode($card->metadata) . "\n";
```

### Updating an Access Card

```php
$card = $client->accessCards->update([
   'card_id' => '0xc4rd1d',
   'employee_id' => '987654321',
   'full_name' => 'Updated Employee Name',
   'classification' => 'contractor',
   'department' => 'Marketing',
   'location' => 'New York',
   'site_name' => 'NYC Office',
   'workstation' => '2F-105',
   'mail_stop' => 'MS-200',
   'company_address' => '456 Broadway, New York, NY 10013',
   'expiration_date' => (new DateTime('now', new DateTimeZone('UTC')))->modify('+3 months')->format('c'),
   'employee_photo' => '[image_in_base64_encoded_format]',
   'title' => 'Senior Developer'
]);

echo "Card updated successfully\n";
```

### Listing Access Cards

```php
$cards = $client->accessCards->list('0xd3adb00b5');
foreach ($cards as $card) {
    echo $card . "\n";
}

// With state filter
$activeCards = $client->accessCards->list('0xd3adb00b5', 'active');
```

### Managing Card States

```php
// Suspend a card
$client->accessCards->suspend(['card_id' => '0xc4rd1d']);

// Resume a card
$client->accessCards->resume(['card_id' => '0xc4rd1d']);

// Unlink a card
$client->accessCards->unlink(['card_id' => '0xc4rd1d']);

// Delete a card
$client->accessCards->delete(['card_id' => '0xc4rd1d']);
```

## Console (Enterprise Features)

### Creating a Card Template

```php
$template = $client->console->createTemplate([
   'name' => 'Employee Access Pass',
   'platform' => 'apple',
   'use_case' => 'employee_badge',
   'protocol' => 'desfire',
   'allow_on_multiple_devices' => true,
   'watch_count' => 2,
   'iphone_count' => 3,
   'background_color' => '#FFFFFF',
   'label_color' => '#000000',
   'label_secondary_color' => '#333333',
   'support_url' => 'https://help.yourcompany.com',
   'support_phone_number' => '+1-555-123-4567',
   'support_email' => 'support@yourcompany.com',
   'privacy_policy_url' => 'https://yourcompany.com/privacy',
   'terms_and_conditions_url' => 'https://yourcompany.com/terms',
   'metadata' => [
       'version' => '2.1',
       'approval_status' => 'approved'
   ]
]);

echo "Template created successfully: {$template->id}\n";
```

### Updating a Card Template

```php
$template = $client->console->updateTemplate([
   'card_template_id' => '0xd3adb00b5',
   'name' => 'Updated Employee Access Pass',
   'allow_on_multiple_devices' => true,
   'watch_count' => 2,
   'iphone_count' => 3,
   'background_color' => '#FFFFFF',
   'label_color' => '#000000',
   'label_secondary_color' => '#333333',
   'support_url' => 'https://help.yourcompany.com',
   'support_phone_number' => '+1-555-123-4567',
   'support_email' => 'support@yourcompany.com',
   'privacy_policy_url' => 'https://yourcompany.com/privacy',
   'terms_and_conditions_url' => 'https://yourcompany.com/terms',
   'metadata' => [
       'version' => '2.2',
       'last_updated_by' => 'admin'
   ]
]);

echo "Template updated successfully: {$template->id}\n";
```

### Reading a Card Template

```php
$template = $client->console->readTemplate([
   'card_template_id' => '0xd3adb00b5'
]);

echo "Template ID: {$template->id}\n";
echo "Name: {$template->name}\n";
echo "Platform: {$template->platform}\n";
echo "Protocol: {$template->protocol}\n";
echo "Multi-device: {$template->allow_on_multiple_devices}\n";
```

### Event Logs

```php
$events = $client->console->eventLog([
   'card_template_id' => '0xd3adb00b5',
   'filters' => [
       'device' => 'mobile',
       'start_date' => (new DateTime('30 days ago'))->format('c'),
       'end_date' => (new DateTime('now'))->format('c'),
       'event_type' => 'install'
   ]
]);

foreach ($events as $event) {
   echo "Event: {$event->type} at {$event->timestamp} by {$event->user_id}\n";
}
```

### Ledger Items

```php
$result = $client->console->ledgerItems([
    'page' => 1,
    'per_page' => 50,
    'start_date' => (new DateTime('30 days ago'))->format('c'),
    'end_date' => (new DateTime('now'))->format('c')
]);

foreach ($result['ledger_items'] as $item) {
    echo "Amount: {$item['amount']}, Kind: {$item['kind']}, Date: {$item['created_at']}\n";
}
```

### iOS In-App Provisioning Preflight

```php
$response = $client->console->iosPreflight([
    'card_template_id' => '0xt3mp14t3-3x1d',
    'access_pass_ex_id' => '0xp455-3x1d'
]);

echo "Provisioning Credential ID: " . $response->provisioningCredentialIdentifier . "\n";
echo "Sharing Instance ID: " . $response->sharingInstanceIdentifier . "\n";
echo "Card Template ID: " . $response->cardTemplateIdentifier . "\n";
echo "Environment ID: " . $response->environmentIdentifier . "\n";
```

### Pass Template Pairs

```php
$result = $client->console->listPassTemplatePairs([
   'page' => 1,
   'per_page' => 50
]);

foreach ($result['pass_template_pairs'] as $pair) {
   echo "Pair: {$pair->name} (ID: {$pair->id})\n";
   if ($pair->androidTemplate) {
       echo "  Android: {$pair->androidTemplate->name}\n";
   }
   if ($pair->iosTemplate) {
       echo "  iOS: {$pair->iosTemplate->name}\n";
   }
}
```

### HID Organizations

```php
// Create HID org
$org = $client->console->hid->orgs->create([
    'name' => 'My Org',
    'full_address' => '1 Main St, NY NY',
    'phone' => '+1-555-0000',
    'first_name' => 'Ada',
    'last_name' => 'Lovelace'
]);

// List all HID orgs
$orgs = $client->console->hid->orgs->list();

// Complete HID org registration
$result = $client->console->hid->orgs->activate([
    'email' => 'admin@example.com',
    'password' => 'hid-password-123'
]);
```

### Landing Pages

```php
// List all landing pages
$landingPages = $client->console->listLandingPages();

foreach ($landingPages as $page) {
    echo "ID: {$page->id}, Name: {$page->name}, Kind: {$page->kind}\n";
    echo "  Password Protected: {$page->password_protected}\n";
    if ($page->logo_url) {
        echo "  Logo URL: {$page->logo_url}\n";
    }
}

// Create a landing page
$landingPage = $client->console->createLandingPage([
    'name' => 'Miami Office Access Pass',
    'kind' => 'universal',
    'additional_text' => 'Welcome to the Miami Office',
    'bg_color' => '#f1f5f9',
    'allow_immediate_download' => true
]);

echo "Landing page created: {$landingPage->id}\n";
echo "Name: {$landingPage->name}, Kind: {$landingPage->kind}\n";

// Update a landing page
$landingPage = $client->console->updateLandingPage('0xlandingpage1d', [
    'name' => 'Updated Miami Office Access Pass',
    'additional_text' => 'Welcome! Tap below to get your access pass.',
    'bg_color' => '#e2e8f0'
]);

echo "Landing page updated: {$landingPage->id}\n";
echo "Name: {$landingPage->name}\n";
```

### Credential Profiles

```php
// List all credential profiles
$profiles = $client->console->credentialProfiles->list();

foreach ($profiles as $profile) {
    echo "ID: {$profile->id}, Name: {$profile->name}, AID: {$profile->aid}\n";
}

// Create a credential profile
$profile = $client->console->credentialProfiles->create([
    'name' => 'Main Office Profile',
    'app_name' => 'KEY-ID-main',
    'keys' => [
        ['value' => 'your_32_char_hex_master_key_here'],
        ['value' => 'your_32_char_hex__read_key__here']
    ]
]);

echo "Profile created: {$profile->id}\n";
echo "AID: {$profile->aid}\n";
```

## Error Handling

```php
try {
    $card = $client->accessCards->provision($data);
} catch (AccessGrid\Exceptions\AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
} catch (AccessGrid\Exceptions\AccessGridException $e) {
    echo "API error: " . $e->getMessage();
}
```

## License

MIT License

## Feature Matrix

| Feature | Supported |
|---|:---:|
| POST /v1/key-cards (issue) | Y |
| GET /v1/key-cards/{id} | Y |
| PATCH /v1/key-cards/{id} | Y |
| GET /v1/key-cards (list) | Y |
| POST .../suspend | Y |
| POST .../resume | Y |
| POST .../unlink | Y |
| POST .../delete | Y |
| POST /v1/console/card-templates | Y |
| PUT /v1/console/card-templates/{id} | Y |
| GET /v1/console/card-templates/{id} | Y |
| GET .../logs | Y |
| GET /v1/console/pass-template-pairs | Y |
| GET /v1/console/ledger-items | Y |
| POST /v1/console/ios-preflight | Y |
| GET /v1/console/landing-pages | Y |
| POST /v1/console/landing-pages | Y |
| PATCH /v1/console/landing-pages/{id} | Y |
| GET /v1/console/credential-profiles | Y |
| POST /v1/console/credential-profiles | Y |
| Webhooks (list/create/delete) | Y |
| HID orgs (create/activate/list) | Y |
