<?php

namespace Steam;

class Auth
{
    public const BAD_RSA            = 1;
    public const CAPTCHA            = 2;
    public const EMAIL              = 3;
    public const TWO_FACTOR         = 4;
    public const BAD_CREDENTIALS    = 5;
    public const SUCCESS            = 6;
    public const FAIL               = 7;
    public const THROTTLE           = 8;
    public const UNEXPECTED         = 9;
}