## Additional methods

[AbstractEnumType](./../../DBAL/Types/AbstractEnumType.php "AbstractEnumType") provides few additional methods, which might be useful.

If you need to check if some string value exists in the array of ENUM values:

```php
BasketballPositionType::isValueExist('Pitcher'); // false
```

If you need to get value in readable format:

```php
BasketballPositionType::getReadableValue(BasketballPositionType::SHOOTING_GUARD); // Will return: Shooting Guard
```

If you need to get value in readable format:

```php
BasketballPositionType::getValues(); // Will return: ['PG', 'SG', 'SF', 'PF', 'C']
```
