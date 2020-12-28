# PHP Steam Client 
 
## Example

Simple auth in CLI:
```php
// steam.php

use Steam\Auth;
use Steam\Client;

require 'vendor/autoload.php';

$client = new Client([
    'username' => 'gaben',
    'password' => 'hackmedaddy',
    'sessionDir' => __DIR__ . '/storage/sessions',
]);

$auth = $client->auth();

$tryAuthCount = 0;

if (!$client->isLoggedIn()) {
    while ($auth['code'] !== Auth::SUCCESS) {
        if (++$tryAuthCount >= 5) {
            throw new Exception('To many auth fails. For this you can get banned by IP if you continue.');
        }
    
        switch ($auth['code']) {
            case Auth::CAPTCHA:
                $captchaLink = $client->getCaptchaLink();
                cli()->yellow()->out("Please enter the code from the captcha picture: {$captchaLink}");
                $input = cli()->input('>>> Enter captcha code:');
                $captchaResolveText = $input->prompt();
                $client->setCaptchaText($captchaResolveText);
                $auth = $client->auth();
                break;
    
            case Auth::EMAIL:
                $input = cli()->input('>>> Enter e-mail code:');
                $emailCode = $input->prompt();
                $client->setEmailCode($emailCode);
                $auth = $client->auth();
                break;
                
            case Auth::TWO_FACTOR:
                $input = cli()->input('>>> Enter 2FA code:');
                $twoFactorCode = $input->prompt();
                $client->setTwoFactorCode($twoFactorCode);
                $auth = $client->auth();
                break;
    
            case Auth::FAIL:
                throw new Exception("Login fail.");
                break;
    
            case Auth::BAD_CREDENTIALS:
                cli()->lightRed()->out('Error: Invalid username or password in profile.');
                $input = cli()->confirm('Want to enter new credentials?');
    
                if (!$input->confirmed()) {
                    cli()->lightRed()->out('Client has been stopped.');
                    exit;
                }
    
                $username = cli()->input('>>> Enter username:')->prompt();
                $password = cli()->password('>>> Enter password:')->prompt();
    
                $client->setUsername($username);
                $client->setPassword($password);
                $auth = $client->auth();
                break;
    
            default:
                throw new Exception("Unexpected error.");
                break;
        }
    }
}

/** We are now logged in */
$balance = $client->market()->getBalance()
print_r($balance);

/** Output */
Array
(
    [raw] => 13,37 pуб.
    [clean] => 13.37
)
```

```bash
php steam.php
```
