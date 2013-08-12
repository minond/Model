Model [![Build Status](https://travis-ci.org/minond/Model.png?branch=master)](https://travis-ci.org/minond/Model)
=====

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

```php
User::setConnection(new PDO('sqlite::memory:'));
```

Current storage traits are:
* Efficio\Dataset\Storage\Model\SessionStorage
* Efficio\Dataset\Storage\Model\DatabaseStorage

Since storage information is defined in the model level any sort of custom storage
may be used.
