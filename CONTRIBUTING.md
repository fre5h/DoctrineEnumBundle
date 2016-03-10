# Contributing

First of all, **thank you** for contributing, **you are awesome**!

Here are a few rules to follow in order to ease code reviews, and discussions before
maintainers accept and merge your work.

As Symfony follows the next standards: [PSR-0](http://www.php-fig.org/psr/psr-0/), [PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/), [PSR-4](http://www.php-fig.org/psr/psr-4/). You also MUST follow them.

You MUST follow the [Symfony Coding Standards](http://symfony.com/doc/current/contributing/code/standards.html).
If you don't know about any of them, you should really read the recommendations.  
You can use a helpful tool [phpcs](https://github.com/squizlabs/PHP_CodeSniffer) with a last version of [Symfony standards](https://github.com/escapestudios/Symfony2-coding-standard).
Or you can use a [PHP-CS-Fixer](http://cs.sensiolabs.org/) tool to fix all coding style issues.

You MUST run the test suite.

You MUST write (or update) unit tests.

You SHOULD write documentation.

Please, write [commit messages that make
sense](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html),
and [rebase your branch](http://git-scm.com/book/en/Git-Branching-Rebasing)
before submitting your Pull Request.

One may ask you to [squash your
commits](http://gitready.com/advanced/2009/02/10/squashing-commits-with-rebase.html)
too. This is used to "clean" your Pull Request before merging it (we don't want
commits such as `fix tests`, `fix 2`, `fix 3`, etc.).

Thank you!
