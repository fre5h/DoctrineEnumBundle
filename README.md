# FreshDoctrineEnumBundle

[![Build Status](https://secure.travis-ci.org/fre5h/DoctrineEnumBundle.png?branch=master)](https://travis-ci.org/fre5h/DoctrineEnumBundle)

Doctrine2 ENUM type for Symfony2 applications.

## Requirements

* Symfony 2.1
* PHP 5.4
* Doctrine 2.2

## Installation

### Install via Composer

Add the following lines to your `composer.json` file and then run `php composer.phar install` or `php composer.phar update`:

```json
{
    "require": {
        "fresh/doctrine-enum-bundle": "dev-master"
    }
}
```

### Register the bundle

To start using the bundle, register it in `app/AppKernel.php`:

```php
public function registerBundles()
{
    $bundles = array(
        // Other bundles...
        new Fresh\Bundle\DoctrineEnumBundle\FreshDoctrineEnumBundle(),
    );
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

### Examples

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
     * @var string Name of this type
     */
    protected $name = 'BasketballPositionType';

    /**
     * @var array Readable choices
     * @static
     */
    protected static $choices = [
        self::POINT_GUARD    => 'Point guard',
        self::SHOOTING_GUARD => 'Shooting guard',
        self::SMALL_FORWARD  => 'Small forward',
        self::POWER_FORWARD  => 'Power forward',
        self::CENTER         => 'Center',
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
 * @ORM\Table(name="players")
 */
class Player
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $position
     *
     * @DoctrineAssert\Enum(entity="Application\Bundle\StatsBundle\DBAL\Types\BasketballPositionType")
     *
     * @ORM\Column(name="position", type="BasketballPositionType", nullable=false)
     */
    protected $position;

    /**
     * Get id
     *
     * @return integer
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

But don't forget to define `BasketballPositionType` in use section:

```php
use Application\Bundle\DefaultBundle\DBAL\Types\BasketballPositionType;
```

When build `BasketballPositionType` as form field, select `choice` type for field and fill choices via `BasketballPositionType::getChoices()` method:

```php
$builder->add('position', 'choice', ['choices' => BasketballPositionType::getChoices()]);
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

You see here is a comment *'(DC2Type:BasketballPositionType)'* for `position` column. Doctrine will know that this column should be processed as `BasketballPositionType`.

If you later need to add new values to ENUM or delete some existed, you also will need to create new migrations. But Doctrine won't detect any changes in your ENUM... :(

Fortunately you can do simple **hook** =) Access your database *(via phpMyAdmin, console etc.)* and delete comment for column `position`. After that console command `doctrine:migrations:diff` will create correct migrations.

You should repeat these steps after each update of your custom ENUM type!

- - -

######It's simple. Enjoy! ;)
