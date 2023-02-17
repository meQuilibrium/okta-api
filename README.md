# okta-api

PHP SDK for the Okta API (at least, the parts we use).

Container name: `okta-api`

## Installation

In composer.json, add a repository:
```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:meQuilibrium/okta-api.git"
    }
  ]
}
```

Then install with composer:

```shell
composer require meq/okta-api
```

## Usage

```php
use Meq\OktaApi\OktaApiClient;

$oktaApiClient = new OktaApiClient('https://your-organization-id.okta.com', $apiToken);

// List groups
$groups = $oktaApiClient->listGroups();
```

## Integration testing

This repo is compatible with PHP versions from 7.1 through 8.2.
As a result, there is no version of PHPUnit capable of running in all test matrix permutations.
To test locally, run `composer req --dev phpunit/phpunit` to install PHPUnit, then `composer test` to run test cases.
