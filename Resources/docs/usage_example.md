## Usage example

This example will show how to create a custom ENUM field for basketball positions. This ENUM should contain five values:

* `PG` - Point Guard
* `SG` - Shooting Guard
* `SF` - Small Forward
* `PF` - Power Forward
* `C` - Center

Create a class for a new ENUM type `BasketballPositionType`:

```php
<?php
namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * @extends AbstractEnumType<string, string>
 */
final class BasketballPositionType extends AbstractEnumType
{
    public final const POINT_GUARD = 'PG';
    public final const SHOOTING_GUARD = 'SG';
    public final const SMALL_FORWARD = 'SF';
    public final const POWER_FORWARD = 'PF';
    public final const CENTER = 'C';

    protected static array $choices = [
        self::POINT_GUARD => 'Point Guard',
        self::SHOOTING_GUARD => 'Shooting Guard',
        self::SMALL_FORWARD => 'Small Forward',
        self::POWER_FORWARD => 'Power Forward',
        self::CENTER => 'Center'
    ];
}
```

Register `BasketballPositionType` for Doctrine in config.yaml:

```yaml
doctrine:
    dbal:
        types:
            BasketballPositionType: App\DBAL\Types\BasketballPositionType
```

Create a `Player` entity that has a `position` field:

```php
<?php
namespace App\Entity;

use App\DBAL\Types\BasketballPositionType;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

#[ORM\Entity]
#[ORM\Table(name: 'players')]
class Player
{
     #[ORM\Id]
     #[ORM\Column(type: 'integer', name: 'id')]
     #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    // Note, that type of field should be same as you set in Doctrine config (in this case it is BasketballPositionType)
    #[ORM\Column(type: BasketballPositionType::class)]
    #[DoctrineAssert\EnumType(entity: BasketballPositionType::class)]
    private $position;

    public function getId()
    {
        return $this->id;
    }

    public function setPosition(string $position)
    {
        BasketballPositionType::assertValidChoice($position);
        
        $this->position = $position;
    }

    public function getPosition(): string
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
use App\DBAL\Types\BasketballPositionType;
```

---

### More features

* [NULL values](./null_values.md "NULL values")
* [Default value](./default_value.md "Default value")
* [Building the form](./building_the_form.md "Building the form")
* [Additional methods](./additional_methods.md "Additional methods")
* [Common types](./common_types.md "Common types")
* [Readable ENUM values in templates](./readable_enum_values_in_template.md "Readable ENUM values in templates")
* [ENUM constants in templates](./enum_constants_in_templates.md "ENUM constants in templates")
* [ENUM values in templates](./enum_values_in_templates.md "ENUM values in templates")
* [Hook for Doctrine migrations](./hook_for_doctrine_migrations.md "Hook for Doctrine migrations")
