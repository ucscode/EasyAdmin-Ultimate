# EasyAdmin Ultimate

EasyAdmin Ultimate is a comprehensive enhancement package built on top of the Symfony framework and integrates the EasyAdmin bundle, providing a rich set of tools, components, utilities, and additional features. It’s designed to simplify development workflows and accelerate project delivery.

## Features

- **Enhanced Symfony Setup**: Optimized setup that comes with necessary packages and configurations right out of the box.

- **EasyAdmin Integration**: Incorporated with the EasyAdmin bundle to offers a strong administration interface that requires little setup to manage entities.

- **Extended Controller and Services**: Benefit from pre-configured controllers, services, and utilities designed to handle common tasks and accelerate development.

- **Simplified Authentication and Login Pages**: Includes pre-built authentication controllers and templates available for safe and secure authentication.

- **Ready-to-Use Entities**: Access a library of built-in entities able to fit into any project, complete with database mappings and CRUD operations, allowing you to focus on business logic rather than repetitive boilerplate code.

## Installation

You can install EasyAdmin Ultimate via Composer. Run the following command:

```bash
composer create-project ucscode/easyadmin-ultimate
```

Once installed, run the following command to get your project ready

```bash
php bin/console eau:initialize
```

## Configuration

If you are using Apache on shared hosting or within a subdirectory and do not have access to the Apache configuration file, please follow these steps to ensure your project works correctly:

- Rename the `.htaccess.bk` file in the project root directory to `.htaccess.`
- Update the **RewriteBase** directive in the .htaccess file to point to your project's subdirectory.

This ensures that the necessary configurations are in place for your project to function as expected.

## Documentation

For detailed usage instructions and documentation, please refer to the following resources:

- [Symfony Documentation](https://symfony.com/doc/current/index.html): Official documentation for Symfony framework, providing comprehensive guides, tutorials, and references for Symfony development.

- [EasyAdmin Documentation](https://symfony.com/doc/current/bundles/EasyAdminBundle/index.html): Official documentation for EasyAdmin bundle, offering detailed documentation on installation, configuration, and usage of EasyAdmin for Symfony applications.

- [EasyAdmin SymfonyCast](https://symfonycasts.com/screencast/easyadminbundle/install): Official video screening page, providing step by step video footages on how to use EasyAdmin.

You can also explore the features and components included in EasyAdmin Ultimate and start leveraging them in your Symfony projects to facilitate development workflows.

## License

EasyAdmin Ultimate is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contribution

If you would like to contribute to the project, you can do so in several ways:

- **Report Issues**: If you encounter any bugs, issues, or have feature requests, please [open an issue](https://github.com/ucscode/easyadmin-ultimate/issues) on GitHub. Providing detailed information about the problem or suggestion will help us address it more effectively.

- **Submit Pull Requests**: If you would like to contribute code enhancements, bug fixes, or new features, please fork the repository, make your changes, and submit a pull request. Make sure to follow the coding standards and include relevant tests and documentation for your changes.

- **Spread the Word**: If you find EasyAdmin Ultimate useful, consider sharing it with others, giving it a star on GitHub, or tweeting about it. Your support helps us grow the community and improve the project.

Before contributing, please review the [contribution guidelines](CONTRIBUTING.md) for detailed information on how to contribute effectively and respectfully.

Thank you for considering contributing to EasyAdmin Ultimate!

## Disclaimer

EasyAdmin Ultimate is a personal project created by [Uchenna Ajah](http://ucscode.com) and is not affiliated with, endorsed by, or sponsored by the Symfony team. Symfony and EasyAdmin are trademarks of their respective owners. This project is not an official product or package from the Symfony team and should not be misconstrued as such.

Please note that while EasyAdmin Ultimate leverages Symfony and EasyAdmin components, it is a standalone project with its own goals and objectives. Any opinions expressed within this project are solely those of the project creator and contributors and do not necessarily reflect the views or opinions of the Symfony team or its affiliates.

For official information and resources related to Symfony framework and EasyAdmin bundle, please refer to the [Symfony Documentation](https://symfony.com/doc/current/index.html) and [EasyAdmin Documentation](https://symfony.com/doc/current/bundles/EasyAdminBundle/index.html) respectively.

