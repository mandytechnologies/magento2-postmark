Magento 2 Postmark Integration
==================
A Magento module to use Postmark instead of Core e-mail module.

Forked from a module by [Ripen](https://www.ripen.com/) that has since been taken offline.

## How it works
This module overwrites the Magento e-mail model so that outgoing mail is passed through Postmark.


## Getting Started

### Configuration options (Backend)
Accessible via *Stores > Configuration > Services > Postmark Integration*

#### Enable Postmark Integration
Enable it to start using Postmark instead of core e-mail model

#### API Key
Your own Postmark API key


## Automated Testing

There is a test suite created for the initial module, but it is not currently maintained and will almost certainly not pass.

It is left in place with the goal of updating it to work correctly again in the future.
