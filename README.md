# WIP: PHP Steam Client 
 
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

$tryAuthCount = 0;

if (!$client->isLoggedIn()) {
    $auth = $client->auth();
    while ($auth['code'] !== Auth::SUCCESS) {
         if (++$tryAuthCount >= 5) {
            throw new Exception('To many auth fails. For this you can get banned by IP if you continue.');
        }
        
        switch ($auth['code']) {
            case Auth::CAPTCHA:
                cli()->yellow()->out($auth['response']->get('message'));
                $captchaLink = $client->getCaptchaLink();
                cli()->yellow()->out($captchaLink);
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
                cli()->yellow()->out($auth['response']->get('message'));
                $input = cli()->input('>>> Enter 2FA code:');
                $twoFactorCode = $input->prompt();
                $client->setTwoFactorCode($twoFactorCode);
                $auth = $client->auth();
    
            case Auth::FAIL:
                print_r($auth);
                throw new Exception('Fail auth.');
                break;

            case Auth::BAD_RSA:
                throw new Exception('Fail RSA');
                break;

            case Auth::THROTTLE:
                throw new Exception($auth['response']->get('message'));
                break;

            case Auth::UNEXPECTED:
                print_r($auth);
                throw new Exception('Unexpected error 1');
                break;
    
            case Auth::BAD_CREDENTIALS:
                cli()->lightRed()->out($auth['response']->get('message'));
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
                throw new Exception("Unexpected error 2");
                break;
        }
    }
}

/** We are now logged in */
$balance = $client->market()->getBalance();
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
