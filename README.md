# Model

[![Build Status](https://travis-ci.org/minond/Model.png?branch=master)](https://travis-ci.org/minond/Model)
[![Coverage Status](https://coveralls.io/repos/minond/Model/badge.png?branch=master)](https://coveralls.io/r/minond/Model?branch=master)
[![Latest Stable Version](https://poser.pugx.org/minond/model/v/stable.png)](https://packagist.org/packages/minond/model)
[![Dependencies Status](https://depending.in/minond/Model.png)](http://depending.in/minond/Model)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/minond/Model/badges/quality-score.png?s=b6c1dd42cd64ad32f9f117b4c0b8fa0c5a42d800)](https://scrutinizer-ci.com/g/minond/Model/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8b8fe3b9-93e8-4b86-9a63-38c455bdd624/mini.png)](https://insight.sensiolabs.com/projects/8b8fe3b9-93e8-4b86-9a63-38c455bdd624)

Models that are simple to create and use. Light weight but easily extensible.

#### Getters and setters will be simulated using the magic call method:

```php
use Efficio\Dataset\Model;

class User extends Model
{
    protected $first_name;
    protected $last_name;
    protected $full_name;
}

```

```php
$me = new User;

// methods
$me->setFirstName('Marcos');
$me->setLastName('Minond');

// or properties
$me->first_name = 'Marcos';
$me->last_name = 'Marcos';
```

Both getter and setter methods may be overwritten:

```php
use Efficio\Dataset\Model;

class User extends Model
{
    protected $first_name;
    protected $last_name;
    protected $full_name;

    public function getFullName()
    {
        return implode(' ', [ $this->first_name, $this->last_name ]);
    }
}
```

```php
$me = new User;
$me->first_name = 'Marcos';
$me->last_name = 'Marcos';

echo $me->full_name; // outputs 'Marcos Minond'
```

#### Traits are used to specify how models are stored:

```php
use Efficio\Dataset\Model;
use Efficio\Dataset\Storage\Model\DatabaseStorage;

class User extends Model
{
    use DatabaseStorage;

    protected $first_name;
    // ...
}
```

Current storage traits are:
* `Efficio\Dataset\Storage\Model\DatabaseStorage` - store in a database using PDO.
* `Efficio\Dataset\Storage\Model\FileStorage` - store in flat files.
* `Efficio\Dataset\Storage\Model\NullStorage` - no storage.
* `Efficio\Dataset\Storage\Model\SessionStorage` - stored in the `$_SESSION` array

Since storage information is defined in the model level any sort of custom storage
may be used.

#### Configuring storage methods

##### DatabaseStorage

```php
// User uses DatabaseStorage trait
User::setConnection(new PDO('sqlite::memory:'));
```

##### FileStorage
```php
// User uses FileStorage trait
User::setDirectory('./cache/models/');
```
