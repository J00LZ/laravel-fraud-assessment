<?php

use App\Models\Customer;
use App\Models\Scan;

// We use `isValid(true)` every time to ensure we ignore the `valid` field in the database.


test('two customers that have completely different data are both valid', function () {
    $scan = Scan::create();
    $customer1 = new Customer();
    $customer1->customerId = 1;
    $customer1->firstName = "John";
    $customer1->lastName = "Doe";
    $customer1->ip = "123.456.789.123";
    $customer1->iban = "NL91ABNA0417164300";
    $customer1->phoneNumber = "+31612345678";
    $customer1->dateOfBirth = "1990-01-01";
    $customer1->scan()->associate($scan);
    $customer1->save();

    $customer2 = new Customer();
    $customer2->customerId = 2;
    $customer2->firstName = "Jane";
    $customer2->lastName = "Smith";
    $customer2->ip = "987.654.321.987";
    $customer2->iban = "NL32RABO0195610843";
    $customer2->phoneNumber = "+31687654321";
    $customer2->dateOfBirth = "1985-05-15";
    $customer2->scan()->associate($scan);
    $customer2->save();

    expect($customer1->isValid(true))->toBeEmpty();
    expect($customer2->isValid(true))->toBeEmpty();

});

test('two customers with the same ip are invalid', function() {
    $scan = Scan::create();
    $customer1 = new Customer();
    $customer1->customerId = 1;
    $customer1->firstName = "John";
    $customer1->lastName = "Doe";
    $customer1->ip = "123.456.789.123";
    $customer1->iban = "NL91ABNA0417164300";
    $customer1->phoneNumber = "+31612345678";
    $customer1->dateOfBirth = "1990-01-01";
    $customer1->scan()->associate($scan);
    $customer1->save();

    $customer2 = new Customer();
    $customer2->customerId = 2;
    $customer2->firstName = "Jane";
    $customer2->lastName = "Smith";
    $customer2->ip = "123.456.789.123";
    $customer2->iban = "NL32RABO0195610843";
    $customer2->phoneNumber = "+31687654321";
    $customer2->dateOfBirth = "1985-05-15";
    $customer2->scan()->associate($scan);
    $customer2->save();

    expect($customer1->isValid(true))->not->toBeEmpty();
    expect($customer2->isValid(true))->not->toBeEmpty();
});

test('two customers with the same iban are invalid', function() {
    $scan = Scan::create();
    $customer1 = new Customer();
    $customer1->customerId = 1;
    $customer1->firstName = "John";
    $customer1->lastName = "Doe";
    $customer1->ip = "123.456.789.123";
    $customer1->iban = "NL91ABNA0417164300";
    $customer1->phoneNumber = "+31612345678";
    $customer1->dateOfBirth = "1990-01-01";
    $customer1->scan()->associate($scan);
    $customer1->save();

    $customer2 = new Customer();
    $customer2->customerId = 2;
    $customer2->firstName = "Jane";
    $customer2->lastName = "Smith";
    $customer2->ip = "987.654.321.987";
    $customer2->iban = "NL91ABNA0417164300";
    $customer2->phoneNumber = "+31687654321";
    $customer2->dateOfBirth = "1985-05-15";
    $customer2->scan()->associate($scan);
    $customer2->save();

    expect($customer1->isValid(true))->not->toBeEmpty();
    expect($customer2->isValid(true))->not->toBeEmpty();
});

test('a customer that is too young is not valid', function() {
    $scan = Scan::create();
    $customer1 = new Customer();
    $customer1->customerId = 1;
    $customer1->firstName = "John";
    $customer1->lastName = "Doe";
    $customer1->ip = "123.456.789.123";
    $customer1->iban = "NL91ABNA0417164300";
    $customer1->phoneNumber = "+31612345678";
    $customer1->dateOfBirth = "2020-01-01";
    $customer1->scan()->associate($scan);
    $customer1->save();

    expect($customer1->isValid(true))->not->toBeEmpty();
});

test('a customer that is german is not valid', function() {
    $scan = Scan::create();
    $customer1 = new Customer();
    $customer1->customerId = 1;
    $customer1->firstName = "John";
    $customer1->lastName = "Doe";
    $customer1->ip = "123.456.789.123";
    $customer1->iban = "NL91ABNA0417164300";
    $customer1->phoneNumber = "+49612345678";
    $customer1->dateOfBirth = "1990-01-01";
    $customer1->scan()->associate($scan);
    $customer1->save();

    expect($customer1->isValid(true))->not->toBeEmpty();
});