## NULL values

`NULL` values are also supported by ENUM field. You can set *nullable* parameter of column to `true` or `false` depends on if you want or not to allow `NULL` values:

```php
#[ORM\Column(type: 'BasketballPositionType', nullable: true)]
private $position;

// or

#[ORM\Column(type: 'BasketballPositionType', nullable: false)]
private $position;
```

---

### More features

* [Default value](./default_value.md "Default value")
* [Building the form](./building_the_form.md "Building the form")
* [Additional methods](./additional_methods.md "Additional methods")
* [Common types](./common_types.md "Common types")
* [Readable ENUM values in templates](./readable_enum_values_in_template.md "Readable ENUM values in templates")
* [ENUM constants in templates](./enum_constants_in_templates.md "ENUM constants in templates")
* [ENUM values in templates](./enum_values_in_templates.md "ENUM values in templates")
* [Hook for Doctrine migrations](./hook_for_doctrine_migrations.md "Hook for Doctrine migrations")
