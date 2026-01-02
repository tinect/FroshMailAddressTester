# FroshMailValidation

This plugin for Shopware 6 validates email addresses during customer registration and checkout processes to ensure the mailbox is accessible and potentially valid.

## Installation

### Via Composer

```bash
composer require frosh/mail-validation
```

```bash
bin/console plugin:refresh
bin/console plugin:install --activate FroshMailValidation
bin/console cache:clear
```

## Support

- **GitHub Issues**: [https://github.com/FriendsOfShopware/FroshMailValidation/issues](https://github.com/FriendsOfShopware/FroshMailValidation/issues)

## License

This plugin is licensed under the [MIT License](LICENSE).
