composer require accessgrid/accessgrid

<?php

require 'vendor/autoload.php';

use AccessGridClient;

$accountId = $_ENV['ACCOUNT_ID'];
$secretKey = $_ENV['SECRET_KEY'];

$client = new Client($accountId, $secretKey);

$card = $client->accessCards->provision([
    'card_template_id' => '0xd3adb00b5',
    'employee_id' => '123456789',
    'tag_id' => 'DDEADB33FB00B5',
    'allow_on_multiple_devices' => true,
    'full_name' => 'Employee name',
    'email' => 'employee@yourwebsite.com',
    'phone_number' => '+19547212241',
    'classification' => 'full_time',
    'start_date' => (new DateTime('now', new DateTimeZone('UTC')))->format('c'),
    'expiration_date' => '2025-02-22T21:04:03.664Z',
    'employee_photo' => '[image_in_base64_encoded_format]'
]);

echo "Install URL: {$card->url}\n";

<?php

require 'vendor/autoload.php';

use AccessGridClient;

$accountId = $_ENV['ACCOUNT_ID'];
$secretKey = $_ENV['SECRET_KEY'];

$client = new Client($accountId, $secretKey);

$card = $client->accessCards->update([
   'card_id' => '0xc4rd1d',
   'employee_id' => '987654321',
   'full_name' => 'Updated Employee Name',
   'classification' => 'contractor',
   'expiration_date' => (new DateTime('now', new DateTimeZone('UTC')))->modify('+3 months')->format('c'),
   'employee_photo' => '[image_in_base64_encoded_format]'
]);

echo "Card updated successfully\n";

<?php

require 'vendor/autoload.php';

use AccessGridClient;

$accountId = $_ENV['ACCOUNT_ID'];
$secretKey = $_ENV['SECRET_KEY'];

$client = new Client($accountId, $secretKey);

$client->accessCards->suspend([
   'card_id' => '0xc4rd1d'
]);

echo "Card suspended successfully\n";

<?php

require 'vendor/autoload.php';

use AccessGridClient;

$accountId = $_ENV['ACCOUNT_ID'];
$secretKey = $_ENV['SECRET_KEY'];

$client = new Client($accountId, $secretKey);

$client->accessCards->resume([
   'card_id' => '0xc4rd1d'
]);

echo "Card resumed successfully\n";

<?php

require 'vendor/autoload.php';

use AccessGridClient;

$accountId = $_ENV['ACCOUNT_ID'];
$secretKey = $_ENV['SECRET_KEY'];

$client = new Client($accountId, $secretKey);

$client->accessCards->unlink([
   'card_id' => '0xc4rd1d'
]);

echo "Card unlinked successfully\n";

<?php

require 'vendor/autoload.php';

use AccessGridClient;

$accountId = $_ENV['ACCOUNT_ID'];
$secretKey = $_ENV['SECRET_KEY'];

$client = new Client($accountId, $secretKey);

$client->accessCards->delete([
   'card_id' => '0xc4rd1d'
]);

echo "Card deleted successfully\n";

<?php

require 'vendor/autoload.php';

use AccessGridClient;

$accountId = $_ENV['ACCOUNT_ID'];
$secretKey = $_ENV['SECRET_KEY'];

$client = new Client($accountId, $secretKey);

$template = $client->console->createTemplate([
   'name' => 'Employee NFC key',
   'platform' => 'apple',
   'use_case' => 'employee_badge',
   'protocol' => 'desfire',
   'allow_on_multiple_devices' => true,
   'watch_count' => 2,
   'iphone_count' => 3,
   'design' => [
       'background_color' => '#FFFFFF',
       'label_color' => '#000000',
       'label_secondary_color' => '#333333',
       'background_image' => '[image_in_base64_encoded_format]',
       'logo_image' => '[image_in_base64_encoded_format]',
       'icon_image' => '[image_in_base64_encoded_format]'
   ],
   'support_info' => [
       'support_url' => 'https://help.yourcompany.com',
       'support_phone_number' => '+1-555-123-4567',
       'support_email' => 'support@yourcompany.com',
       'privacy_policy_url' => 'https://yourcompany.com/privacy',
       'terms_and_conditions_url' => 'https://yourcompany.com/terms'
   ]
]);

echo "Template created successfully: {$template->id}\n";

<?php

require 'vendor/autoload.php';

use AccessGridClient;

$accountId = $_ENV['ACCOUNT_ID'];
$secretKey = $_ENV['SECRET_KEY'];

$client = new Client($accountId, $secretKey);

$template = $client->console->updateTemplate([
   'card_template_id' => '0xd3adb00b5',
   'name' => 'Updated Employee NFC key',
   'allow_on_multiple_devices' => true,
   'watch_count' => 2,
   'iphone_count' => 3,
   'support_info' => [
       'support_url' => 'https://help.yourcompany.com',
       'support_phone_number' => '+1-555-123-4567',
       'support_email' => 'support@yourcompany.com',
       'privacy_policy_url' => 'https://yourcompany.com/privacy',
       'terms_and_conditions_url' => 'https://yourcompany.com/terms'
   ]
]);

echo "Template updated successfully: {$template->id}\n";

<?php

require 'vendor/autoload.php';

use AccessGridClient;

$accountId = $_ENV['ACCOUNT_ID'];
$secretKey = $_ENV['SECRET_KEY'];

$client = new Client($accountId, $secretKey);

$template = $client->console->readTemplate([
   'card_template_id' => '0xd3adb00b5'
]);

echo "Template ID: {$template->id}\n";
echo "Name: {$template->name}\n";
echo "Platform: {$template->platform}\n";
echo "Protocol: {$template->protocol}\n";
echo "Multi-device: {$template->allow_on_multiple_devices}\n";

<?php

require 'vendor/autoload.php';

use AccessGridClient;

$accountId = $_ENV['ACCOUNT_ID'];
$secretKey = $_ENV['SECRET_KEY'];

$client = new Client($accountId, $secretKey);

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


<?php

require 'vendor/autoload.php';

$accountId = getenv('ACCOUNT_ID');
$secretKey = getenv('SECRET_KEY');

$client = new AccessGrid\Client($accountId, $secretKey);

try {
    $response = $client->console->iosPreflight([
        'card_template_id' => '0xt3mp14t3-3x1d',
        'access_pass_ex_id' => '0xp455-3x1d'
    ]);

    echo \"Provisioning Credential ID: \" . $response->provisioningCredentialIdentifier . \"\\n\";
    echo \"Sharing Instance ID: \" . $response->sharingInstanceIdentifier . \"\\n\";
    echo \"Card Template ID: \" . $response->cardTemplateIdentifier . \"\\n\";
    echo \"Environment ID: \" . $response->environmentIdentifier . \"\\n\";
} catch (Exception $e) {
    echo \"Error retrieving provisioning identifiers: \" . $e->getMessage() . \"\\n\";
}
