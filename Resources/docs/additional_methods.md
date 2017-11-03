## Additional methods

[AbstractEnumType](./../../DBAL/Types/AbstractEnumType.php "AbstractEnumType") provides few additional methods, which might be useful.

##### If you need to check if some string value exists in the array of ENUM values:

```php
BasketballPositionType::isValueExist('Pitcher');
// Will return: false
```

##### If you need to get value in the readable format:

```php
BasketballPositionType::getReadableValue(BasketballPositionType::SHOOTING_GUARD);
// Will return: Shooting Guard
```

##### If you need to get values in the readable format:

```php
BasketballPositionType::getValues();
// Will return: ['PG', 'SG', 'SF', 'PF', 'C']
```

---

### More features

* [NULL values](./null_values.md "NULL values")
* [Building the form](./building_the_form.md "Building the form")
* [Readable ENUM values in templates](./readable_enum_values_in_template.md "Readable ENUM values in templates")
* [ENUM constants in templates](./enum_constants_in_templates.md "ENUM constants in templates")
* [Hook for Doctrine migrations](./hook_for_doctrine_migrations.md "Hook for Doctrine migrations")
