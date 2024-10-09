
# Magento 2 Postmark Integration

![Magento 2](https://img.shields.io/badge/Magento-2.4-brightgreen.svg)
![License](https://img.shields.io/badge/license-MIT-blue.svg)

This module integrates Postmark, a reliable email delivery service, with Magento 2, allowing you to send all transactional emails through Postmark. It improves the reliability of your email delivery, reduces the chance of your emails landing in spam, and provides access to Postmark’s powerful email tracking and analytics features.

## Features

- Seamless integration with Postmark for sending transactional emails in Magento 2.
- Enhanced email delivery and tracking.
- Provides access to Postmark's analytics, including open rates, click rates, and more.
- Improved email deliverability and reduced spam issues.

## Fork Information

This Magento 2 Postmark Integration is a fork from [ripenecommerce/magento2-postmark](https://github.com/ripenecommerce/magento2-postmark).

## Requirements

- Magento 2.4.x or later
- PHP 7.4 or later
- A Postmark account with API credentials

## Installation

### Using Composer

1. **Require the module via Composer:**
   ```bash
   composer require mandytechnologies/magento2-postmark
   ```

2. **Enable the module:**
   ```bash
   bin/magento module:enable MandyTechnologies_Postmark
   ```

3. **Run the Magento setup upgrade command:**
   ```bash
   bin/magento setup:upgrade
   ```

4. **Deploy static content:**
   ```bash
   bin/magento setup:static-content:deploy
   ```

5. **Flush the Magento cache:**
   ```bash
   bin/magento cache:flush
   ```

## Configuration

1. **Sign up for a Postmark Account:**
   - If you don’t already have a Postmark account, [sign up here](https://postmarkapp.com/sign_up) and generate an API key for sending transactional emails.
   
2. **Configure the module in Magento Admin:**
   - Navigate to `Stores` > `Configuration` > `Postmark` to enter your Postmark API key.
   - Set other email options such as from name, email address, and sender signature.

## Usage

Once configured, all your Magento 2 transactional emails, such as order confirmations, shipping updates, and password reset emails, will be sent via Postmark. You can view detailed logs and analytics in your Postmark dashboard.

## Troubleshooting

If you encounter any issues, consider the following steps:

- Ensure that your Postmark API key is correct.
- Verify that your domain is verified and approved for sending through Postmark.
- Clear the Magento cache using `bin/magento cache:flush`.

## Contributing

We welcome contributions! Please fork the repository and submit a pull request.

## Support

For support, please open an issue on GitHub or reach out to Postmark’s [support team](https://postmarkapp.com/support).
