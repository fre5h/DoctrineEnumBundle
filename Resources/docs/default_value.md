## Default value

Override method `getDefaultValue` in your ENUM class:

```php
<?php
namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class MapLocationType extends AbstractEnumType
{
    public const NORTH = 'N';
    public const EAST = 'E';
    public const SOUTH = 'S';
    public const WEST = 'W';
    public const CENTER = 'C';

    protected static array $choices = [
        self::NORTH => 'North',
        self::EAST => 'East',
        self::SOUTH => 'South',
        self::WEST => 'West',
        self::CENTER => 'Center',
    ];

    public static function getDefaultValue(): ?string
    {
        return self::CENTER; // This value will be used as default in DDL statement
    }
}
```

---

### More features

* [NULL values](./null_values.md "NULL values")
* [Building the form](./building_the_form.md "Building the form")
* [Additional methods](./additional_methods.md "Additional methods")
* [Common types](./common_types.md "Common types")
* [Readable ENUM values in templates](./readable_enum_values_in_template.md "Readable ENUM values in templates")
* [ENUM constants in templates](./enum_constants_in_templates.md "ENUM constants in templates")
* [ENUM values in templates](./enum_values_in_templates.md "ENUM values in templates")
* [Hook for Doctrine migrations](./hook_for_doctrine_migrations.md "Hook for Doctrine migrations")
