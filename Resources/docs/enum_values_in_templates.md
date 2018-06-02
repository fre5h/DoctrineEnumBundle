## ENUM values in templates

There are two TWIG functions which can help you to get array of ENUM values in your templates.
They are `enum_values()` and `enum_readable_values()`.

#### Example of using `enum_values()`

```jinja
{% for value in enum_values('BasketballPositionType') %}
    {{ value }}<br />
{% endfor %}
```

##### Result HTML
```html
PG
SG
SF
PF
C
```

#### Example of using `enum_readable_values()`

```jinja
{% for key, value in enum_readable_values('BasketballPositionType') %}
    {{ key }} => {{ value }}<br />
{% endfor %}
```

##### Result HTML
```html
PG => Point Guard
SG => Shooting Guard
SF => Small Forward
PF => Power Forward
C => Center
```

---

### More features

* [NULL values](./null_values.md "NULL values")
* [Building the form](./building_the_form.md "Building the form")
* [Additional methods](./additional_methods.md "Additional methods")
* [Readable ENUM values in templates](./readable_enum_values_in_template.md "Readable ENUM values in templates")
* [ENUM constants in templates](./enum_constants_in_templates.md "ENUM constants in templates")
* [Hook for Doctrine migrations](./hook_for_doctrine_migrations.md "Hook for Doctrine migrations")
