<?php

namespace Meq\Tests\OktaApi\Integration\Group;

use Meq\Tests\OktaApi\Integration\IntegrationTestCase;

class GroupApiTest extends IntegrationTestCase
{
    public function testListGroups(): void
    {
        $groups = $this->apiClient->listGroups();
        $this->assertIsArray($groups);
    }
}
