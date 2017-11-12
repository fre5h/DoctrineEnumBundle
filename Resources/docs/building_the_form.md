## Building the form

When build `BasketballPositionType` as form field, you don't need to specify some additional parameters. Just add property to the form builder and [EnumTypeGuesser](./../../Form/EnumTypeGuesser.php "EnumTypeGuesser") will do all work for you. That's how:

```php
$builder->add('position');
```

If you need to add some extra parameters, just skip the second *field type* parameter:

```php
$builder->add('position', null, [
    'required' => true,
    'attr' => [
        'class' => 'some-class'
    ]
]);
```

If for some reason you need to specify full config, it can look like this:

```php
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

$builder->add('position', ChoiceType::class, [
    'choices' => BasketballPositionType::getChoices()
]);
```

[EnumTypeGuesser](./../../Form/EnumTypeGuesser.php "EnumTypeGuesser") process **only** DBAL types that are children of [AbstractEnumType](./../../DBAL/Types/AbstractEnumType.php "AbstractEnumType").
All other custom DBAL types, which are defined, will be skipped from guessing.

---

### More features

* [NULL values](./null_values.md "NULL values")
* [Additional methods](./additional_methods.md "Additional methods")
* [Readable ENUM values in templates](./readable_enum_values_in_template.md "Readable ENUM values in templates")
* [ENUM constants in templates](./enum_constants_in_templates.md "ENUM constants in templates")
* [Hook for Doctrine migrations](./hook_for_doctrine_migrations.md "Hook for Doctrine migrations")
