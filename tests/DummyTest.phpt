<?php
declare(strict_types=1);
use Tester\Assert;

// require tester library
require __DIR__ . '/../vendor/autoload.php';          //install with composer

// Konfigurace prostredi velmi zprehledni vypisy chyb.
// Nemusite pouzit, pokud preferujete vychozi vypis PHP.
Tester\Environment::setup();

Assert::true(true);
