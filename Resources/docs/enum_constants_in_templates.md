## ENUM constants in templates

There is also another custom TWIG filter `|enum_constant`. It allows to use constants from ENUM classes in templates to print their values or to compare with other values.

```jinja
{{ 'SHOOTING_GUARD'|enum_constant }}
{{ 'NORTH_WEST'|enum_constant }}

{% if player.position == 'SHOOTING_GUARD'|enum_constant %}
    <span class="custom-class">{{ player.position }}</span>
{% endif %}
```

Same problem as for `|readable_enum` filter is present here too. If some constant is defined in few ENUM classes then an exception will be thrown.
You can specify the correct class for this constant and it solves the problem.

```jinja
{{ 'CENTER'|enum_constant('BasketballPositionType') }}
{{ 'CENTER'|enum_constant('MapLocationType') }}
```

---

### More features

* [NULL values](./null_values.md "NULL values")
* [Building the form](./building_the_form.md "Building the form")
* [Additional methods](./additional_methods.md "Additional methods")
* [Readable ENUM values in templates](./readable_enum_values_in_template.md "Readable ENUM values in templates")
* [Hook for Doctrine migrations](./hook_for_doctrine_migrations.md "Hook for Doctrine migrations")
