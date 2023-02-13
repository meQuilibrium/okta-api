<?php

namespace Meq\Tests\OktaApi\Integration;

use PHPUnit\Framework\TestCase;
use Meq\OktaApi\OktaApiClient;

abstract class IntegrationTestCase extends TestCase
{
    /** @var ?string */
    private $oktaBaseUrl;
    /** @var ?string */
    private $oktaApiToken;
    /** @var OktaApiClient */
    protected $apiClient;

    public function setUp(): void
    {
        parent::setUp();

        $this->oktaBaseUrl = getenv('OKTA_BASE_URL');
        $this->oktaApiToken = getenv('OKTA_API_TOKEN');

        $this->apiClient = new OktaApiClient($this->oktaBaseUrl, $this->oktaApiToken);
    }

    /**
     * Assert that requisite environment variables are set before performing integration tests.
     */
    public function assertPreConditions(): void
    {
        parent::assertPreConditions();

        $errorMessage = "You must set the %s environment variable for integration tests.\n" .
            "Try copying phpunit.xml.dist to phpunit.xml and setting it in the <php> section.";

        $this->assertNotEmpty($this->oktaBaseUrl, sprintf($errorMessage, 'OKTA_BASE_URL'));
        $this->assertNotEmpty($this->oktaApiToken, sprintf($errorMessage, 'OKTA_API_TOKEN'));
    }
}
