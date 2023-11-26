Phariscope DoctrineEnumType
========

To persist php enum fields with Doctrine, you have to create doctrine an enum type class for each enum.

To keep it easy and reduce the amount of tests use EnumType.


# Installation

```console
composer require phariscope/doctrineenumtype
```

# Usage


Create your enum object as usual. For example:
```php
enum Example: string
{
    case EXAMPLE = 'EXAMPLE';
    case FAKE = 'FAKE';
    case REAL = 'REAL';
}

```

Create your doctrine enum type test. For example:
```php
class ExampleTypeTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals('enum_example', (new ExampleType())->getName());
    }
```

Your production code source will be very short and look like to this:
```php
use Phariscope\DoctrineEnumType\EnumType;

class ExampleType extends EnumType
{
    protected string $name = "enum_example";
    protected string $className = EnumExample::class;
}
```

Now you can use this new 'ExampleType' with Doctrine ORM.

```xml
<field name="myExample" type="enum_example" column="my_example"/>
```

of course don't forget to declare this new type to doctrine (`Type::addType("enum_example", ExampleType::class)` somewhere in your application).

# To Contribute to phariscope/DoctrineEnumType

## Requirements

* docker
* git

## Install


```console
git clone git@github.com:phariscope/DoctrineEnumType.git
cd DoctrineEnumType
./install
```

Installation will load php docker image and composer install, so all commands contained in the 'bin' folder will be easily accessbile.

Example:
```console
bin/php -v
```

## Unit test

```console
bin/phpunit
```

Using Test-Driven Development (TDD) principles (thanks to Kent Beck and others), following good practices (thanks to Uncle Bob and others).

## Quality

* phpcs PSR12
* phpstan level 9
* coverage 100%
* infection MSI >99%

Quick check with:
```console
./codecheck
```

Check coverage with:
```console
bin/phpunit --coverage-html var
```
and view 'var/index.html' with your browser

Check infection with:
```console
bin/infection
```
and view 'var/infection.html' with your browser