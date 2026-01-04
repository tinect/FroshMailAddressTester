# FroshMailAddressTester

This plugin for Shopware 6 tests email address during customer registration and checkout processes to ensure the mailbox is accessible and potentially valid.

## Installation

### Via Composer

```bash
composer require frosh/mail-address-tester
```

```bash
bin/console plugin:refresh
bin/console plugin:install --activate FroshMailAddressTester
bin/console cache:clear
```

## Support

- **GitHub Issues**: [https://github.com/FriendsOfShopware/FroshMailAddressTester/issues](https://github.com/FriendsOfShopware/FroshMailAddressTester/issues)

## License

This plugin is licensed under the [MIT License](LICENSE).
