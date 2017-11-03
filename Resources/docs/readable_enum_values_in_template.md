## Readable ENUM values in templates

You might want to show ENUM values rendered in your templates in *readable format* instead of values that are stored in DB.
It is easy to do by using the custom TWIG filter `|readable_enum` that was implemented for this purpose.
In the example below if Player is a Point Guard in their basketball team then position will be rendered in template as `Point Guard` instead of `PG`.

```jinja
{{ player.position|readable_enum }}
```

How it works? If there is no additional parameter for the filter, [ReadableEnumValueExtension](./../../Twig/Extension/ReadableEnumValueExtension.php "ReadableEnumValueExtension")
tries to find which ENUM type from registered ENUM types has this value.
If only one ENUM type found, then it is possible to get the readable value from it. Otherwise it will throw an exception.

For example `BasketballPositionType` and `MapLocationType` can have same ENUM value `C` with its readable variant `Center`.
The code below will throw an exception, because without additional parameter for `|readable_enum` filter, it can't determine which ENUM type to use in which case:

```jinja
{{ set player_position = 'C' }}
{{ set location_on_the_map = 'C' }}

{{ player_position|readable_enum }}
{{ location_on_the_map|readable_enum }}
```

So, the correct usage of `|readable_enum` filter in this case should be with additional parameter, that specifies the ENUM type:

```jinja
{{ set player_position = 'C' }}
{{ set location_on_the_map = 'C' }}

{{ player_position|readable_enum('BasketballPositionType') }}
{{ location_on_the_map|readable_enum('MapLocationType') }}
```

---

### More features

* [NULL values](./null_values.md "NULL values")
* [Building the form](./building_the_form.md "Building the form")
* [Additional methods](./additional_methods.md "Additional methods")
* [ENUM constants in templates](./enum_constants_in_templates.md "ENUM constants in templates")
* [Hook for Doctrine migrations](./hook_for_doctrine_migrations.md "Hook for Doctrine migrations")
