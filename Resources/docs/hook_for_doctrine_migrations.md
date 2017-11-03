## Hook for Doctrine migrations

If you use [Doctrine migrations](https://github.com/doctrine/migrations "Doctrine migrations") in your project you should be able to create migrations for you custom ENUM types.
If you want to create migration for the **new** ENUM type, then just use console commands `doctrine:migrations:diff` to create migration and `doctrine:migrations:migrate` to execute it.

For the previous example of `BasketballPositionType` for MySQL DB (e.g.) Doctrine will generate SQL statement, that looks like this:

```sql
CREATE TABLE players (
    id INT AUTO_INCREMENT NOT NULL,
    position ENUM('PG', 'SG', 'SF', 'PF', 'C') NOT NULL COMMENT '(DC2Type:BasketballPositionType)',
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
```

You can see here the comment *'(DC2Type:BasketballPositionType)'* for `position` column. Doctrine will know that this column should be processed as `BasketballPositionType`.

If you later need to add new values to ENUM or delete some existing, you also need to create new migrations. But Doctrine won't detect any changes in your ENUM... :(

Fortunately you can do simple **hook** =) Access your database and delete comment for `position` column. Then run console command `doctrine:migrations:diff` it will create correct migrations.

You should repeat these steps after each update of your custom ENUM type!

---

### More features

* [NULL values](./null_values.md "NULL values")
* [Building the form](./building_the_form.md "Building the form")
* [Additional methods](./additional_methods.md "Additional methods")
* [Readable ENUM values in templates](./readable_enum_values_in_template.md "Readable ENUM values in templates")
* [ENUM constants in templates](./enum_constants_in_templates.md "ENUM constants in templates")
