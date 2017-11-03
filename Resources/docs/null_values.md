## NULL values

`NULL` values are also supported by ENUM field. You can set *nullable* parameter of column to `true` or `false` depends on if you want or not to allow `NULL` values:

```php
/** @ORM\Column(name="position", type="BasketballPositionType", nullable=true) */
protected $position;

// or

/** @ORM\Column(name="position", type="BasketballPositionType", nullable=false) */
protected $position;
```

---

### More features

* [Building the form](./building_the_form.md "Building the form")
* [Additional methods](./additional_methods.md "Additional methods")
* [Readable ENUM values in templates](./readable_enum_values_in_template.md "Readable ENUM values in templates")
* [ENUM constants in templates](./enum_constants_in_templates.md "ENUM constants in templates")
* [Hook for Doctrine migrations](./hook_for_doctrine_migrations.md "Hook for Doctrine migrations")
