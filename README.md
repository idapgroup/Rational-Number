# Relational Namber

PHP Class for Rational numbers manipulations

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```
composer require idapgroup/rational-number
```

or add

```json
{
  "require": {
    "idapgroup/rational-number": "^1.0.0"
  }
}
```

to the requirement section of your `composer.json` file.

## Quickstart

```php
$number = new Rational(100);
```

### Create number

```php
$number = new Rational(100);
```

### Operations with numbers

```php
$firstNumber = new Rational(100);
$secondNumber = new Rational(200);

//Multiplication 
$mulResult = $firstNumber->mul($secondNumber);

//Division
$divResult = $firstNumber->div($secondNumber);

//Summarizing
$sumResult = $firstNumber->add($secondNumber);

//Subtraction
$subResult = $firstNumber->sub($secondNumber);

//Сomparison
$equalsResult = $firstNumber->equals($secondNumber);

```
