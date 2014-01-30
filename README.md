# DoctrineEnumBundle

Provides support of *MySQL* **ENUM type** for Doctrine in Symfony applications

[![License](https://poser.pugx.org/fresh/doctrine-enum-bundle/license.png)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![Build Status](https://secure.travis-ci.org/fre5h/DoctrineEnumBundle.png?branch=master)](https://travis-ci.org/fre5h/DoctrineEnumBundle)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/fre5h/DoctrineEnumBundle/badges/quality-score.png?s=be81f9b30a3996e7786cff5b4e0c0d972a64a37b)](https://scrutinizer-ci.com/g/fre5h/DoctrineEnumBundle/)
[![Latest Stable Version](https://poser.pugx.org/fresh/doctrine-enum-bundle/v/stable.png)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![Total Downloads](https://poser.pugx.org/fresh/doctrine-enum-bundle/downloads.png)](https://packagist.org/packages/fresh/doctrine-enum-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0cff4816-374a-474e-a1d5-9d5db34562e3/mini.png)](https://insight.sensiolabs.com/projects/0cff4816-374a-474e-a1d5-9d5db34562e3)
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/fre5h/doctrineenumbundle/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

[![knpbundles.com](http://knpbundles.com/fre5h/DoctrineEnumBundle/badge-short)](http://knpbundles.com/fre5h/DoctrineEnumBundle)

## Requirements

* Symfony 2.1 *and later*
* PHP 5.4 *and later*
* Doctrine 2.2 *and later*

## Installation

### Install via Composer

Add the following lines to your `composer.json` file and then run `php composer.phar install` or `php composer.phar update`:

```json
{
    "require": {
        "fresh/doctrine-enum-bundle": "v2.4"
    }
}
```

### Register the bundle

To start using the bundle, register it in `app/AppKernel.php`:

```php
public function registerBundles()
{
    $bundles = [
        // Other bundles...
        new Fresh\Bundle\DoctrineEnumBundle\FreshDoctrineEnumBundle(),
    ];
}
```

### Update config.yml

Add the following lines for doctrine configuration in `config.yml` file:

```yml
# Doctrine Configuration
doctrine:
    dbal:
        # Other options...
        mapping_types:
            enum: string
```

## Using

### Example

In this example will be shown how to create custom ENUM field for basketball positions. This ENUM should contain five values:

* `PG` - Point guard
* `SG` - Shooting guard
* `SF` - Small forward
* `PF` - Power forward
* `C`  - Center

Create class for new ENUM type `BasketballPositionType`:

```php
<?php
namespace Application\Bundle\DefaultBundle\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Basketball position type
 */
class BasketballPositionType extends AbstractEnumType
{
    const POINT_GUARD    = 'PG';
    const SHOOTING_GUARD = 'SG';
    const SMALL_FORWARD  = 'SF';
    const POWER_FORWARD  = 'PF';
    const CENTER         = 'C';

    /**
     * @var array Readable choices
     * @static
     */
    protected static $choices = [
        self::POINT_GUARD    => 'Point guard',
        self::SHOOTING_GUARD => 'Shooting guard',
        self::SMALL_FORWARD  => 'Small forward',
        self::POWER_FORWARD  => 'Power forward',
        self::CENTER         => 'Center'
    ];
}
```

Register `BasketballPositionType` for Doctrine in config.yml:

```yml
# Doctrine Configuration
doctrine:
    dbal:
        # Other options...
        types:
            BasketballPositionType: Application\Bundle\DefaultBundle\DBAL\Types\BasketballPositionType
```

Create `Player` entity that has `position` field:

```php
<?php
namespace Application\Bundle\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Bundle\DefaultBundle\DBAL\Types\BasketballPositionType;
use Fresh\Bundle\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * Player Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="players")
 */
class Player
{
    /**
     * @var int $id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $position
     *
     * @DoctrineAssert\Enum(entity="Application\Bundle\DefaultBundle\DBAL\Types\BasketballPositionType")
     *
     * @ORM\Column(name="position", type="BasketballPositionType", nullable=false)
     */
    protected $position;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set position
     *
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }
}
```

Now you can set a position for `Player` inside some action or somewhere else:

```php
$player->setPosition(BasketballPositionType::POINT_GUARD);
```

But don't forget to define `BasketballPositionType` in the *use* section:

```php
use Application\Bundle\DefaultBundle\DBAL\Types\BasketballPositionType;
```

`NULL` values are also supported by ENUM field.
You can set *nullable* parameter of column to `true` or `false` depends on if you want or not to allow `NULL` values:

```php
/** @ORM\Column(name="position", type="BasketballPositionType", nullable=true) */
protected $position;
// or
/** @ORM\Column(name="position", type="BasketballPositionType", nullable=false) */
protected $position;
```

##### Building the form
When build `BasketballPositionType` as form field, you don't need to specify some additional parameters. Just add property to the form builder and [EnumTypeGuesser](./Form/EnumTypeGuesser.php "EnumTypeGuesser") will do all work for you. That's how:

```php
$builder->add('position');
```

If you need to add some extra parameters, just skip the second (`field type`) parameter:

```php
$builder->add('position', null, [
    'required' => true,
    'attr'     => [
        'class' => 'some-class'
    ]
]);
```

If for some reason you need to specify full config, it can look like this:
```php
$builder->add('position', 'choice', [
    'choices' => BasketballPositionType::getChoices()
]);
```

[EnumTypeGuesser](./Form/EnumTypeGuesser.php "EnumTypeGuesser") process **only** DBAL types that are children of [AbstractEnumType](./DBAL/Types/AbstractEnumType.php "AbstractEnumType").
All other custom DBAL types, which are defined, will be skipped from guessing.

##### Readable ENUM values in templates
You would want to show ENUM values rendered in your templates in *readable format* instead of the values that would be stored in DB. It is easy to do by using the Twig filter `|readable` that was implemented for that purpose. In the example below if the player is a point guard of his team then his position will be rendered in template as `Point guard` instead of `PG`.

```jinja
{{ player.position|readable }}
```

How it works? If there is no additional parameter for the filter, [ReadableEnumValueExtension](./Twig/Extension/ReadableEnumValueExtension.php "ReadableEnumValueExtension") tries to find which ENUM type of the registered ENUM types consists this value. If only one ENUM type found, then it is possible to get the readable value from it. Otherwise it will throw an exception.

For example `BasketballPositionType` and `MapLocationType` can have same ENUM value `C` with its readable variant `Center`. The code below will throw an exception, because without additional parameter for `|readable` filter, it can't determine which ENUM type to use in which case:

```jinja
{{ set player_position = 'C' }}
{{ set location_on_the_map = 'C' }}

{{ player_position|readable }}
{{ location_on_the_map|readable }}
```

So, that correct usage of `|readable` filter in this case should be with additional parameter that specifies the ENUM type:

```jinja
{{ set player_position = 'C' }}
{{ set location_on_the_map = 'C' }}

{{ player_position|readable('BasketballPositionType') }}
{{ location_on_the_map|readable('MapLocationType') }}
```

### Hook for Doctrine migrations

If you use [Doctrine migrations](https://github.com/doctrine/migrations "Doctrine migrations") in your project you should be able to create migrations for you custom ENUM types. If you want to create migration for the **new** ENUM type, then just use console commands `doctrine:migrations:diff` to create migration and `doctrine:migrations:migrate` to execute it.

For the previous example of `BasketballPositionType` Doctrine will generate SQL statement, that looks like this:

```sql
CREATE TABLE players (
    id INT AUTO_INCREMENT NOT NULL,
    position ENUM('PG', 'SG', 'SF', 'PF', 'C') NOT NULL COMMENT '(DC2Type:BasketballPositionType)',
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
```

You can see here the comment *'(DC2Type:BasketballPositionType)'* for `position` column. Doctrine will know that this column should be processed as `BasketballPositionType`.

If you later will need to add new values to ENUM or delete some existed, you also will need to create new migrations. But Doctrine won't detect any changes in your ENUM... :(

Fortunately you can do simple **hook** =) Access your database and delete comment for `position` column. After that run console command `doctrine:migrations:diff` it will create correct migrations.

You should repeat these steps after each update of your custom ENUM type!
