## Example of using

In this example will be shown how to create a custom ENUM field for basketball positions. This ENUM should contain five values:

* `PG` - Point Guard
* `SG` - Shooting Guard
* `SF` - Small Forward
* `PF` - Power Forward
* `C` - Center

Create a class for new ENUM type `BasketballPositionType`:

```php
<?php
namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class BasketballPositionType extends AbstractEnumType
{
    const POINT_GUARD = 'PG';
    const SHOOTING_GUARD = 'SG';
    const SMALL_FORWARD = 'SF';
    const POWER_FORWARD = 'PF';
    const CENTER = 'C';

    protected static $choices = [
        self::POINT_GUARD => 'Point Guard',
        self::SHOOTING_GUARD => 'Shooting Guard',
        self::SMALL_FORWARD => 'Small Forward',
        self::POWER_FORWARD => 'Power Forward',
        self::CENTER => 'Center'
    ];
}
```

Register `BasketballPositionType` for Doctrine in config.yml:

```yml
doctrine:
    dbal:
        types:
            BasketballPositionType: AppBundle\DBAL\Types\BasketballPositionType
```

Create a `Player` entity that has a `position` field:

```php
<?php
namespace App\Entity;

use App\DBAL\Types\BasketballPositionType;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="players")
 */
class Player
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Note, that type of a field should be same as you set in Doctrine config
     * (in this case it is BasketballPositionType)
     *
     * @ORM\Column(name="position", type="BasketballPositionType", nullable=false)
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\BasketballPositionType")     
     */
    protected $position;

    public function getId()
    {
        return $this->id;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

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
use AppBundle\DBAL\Types\BasketballPositionType;
```

`NULL` values are also supported by ENUM field. You can set *nullable* parameter of column to `true` or `false` depends on if you want or not to allow `NULL` values:

```php
/** @ORM\Column(name="position", type="BasketballPositionType", nullable=true) */
protected $position;

// or

/** @ORM\Column(name="position", type="BasketballPositionType", nullable=false) */
protected $position;
```
