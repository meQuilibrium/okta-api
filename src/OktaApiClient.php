<?php

namespace Meq\OktaApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

/**
 * @codeCoverageIgnore (unit tests should not make API calls)
 */
class OktaApiClient
{
    /** @var Client */
    private $httpClient;
    /** @var string */
    private $oktaApiToken;

    /**
     * @param string $oktaApiBaseUrl
     * @param string $oktaApiToken
     */
    public function __construct($oktaApiBaseUrl, $oktaApiToken)
    {
        $this->httpClient = new Client(['base_uri' => $oktaApiBaseUrl]);
        $this->oktaApiToken = $oktaApiToken;
    }

    /**
     * Get an array containing all the Okta groups
     *
     * @param string $search
     * @return object[]
     */
    public function listGroups($search = null): array
    {
        if (!empty($search)) {
            $search_query = http_build_query(['search' => $search]);
        }

        $groups = $this->request(
            'GET',
            sprintf('api/v1/groups?%s', $search_query ?? '')
        );

        return $groups;
    }

    /**
     * Get a group, or return null if none with the given ID exists.
     *
     * @param string $oktaGroupId
     * @return ?object
     */
    public function getGroup($oktaGroupId)
    {
        try {
            $group = $this->request('GET', sprintf('api/v1/groups/%s', $oktaGroupId));
        } catch (BadResponseException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                return null;
            }
            // If there was an error other than the group missing, re-throw
            throw $e;
        }

        return $group;
    }

    /**
     * Add a new group to Okta
     *
     * @param mixed[] $profile
     *   An array of key/value pairs of Okta Group profile data.
     *   @see https://developer.okta.com/docs/reference/api/groups/#add-group
     * @return object
     */
    public function addGroup(array $profile)
    {
        $group = $this->request(
            'POST',
            'api/v1/groups',
            [
                'json' => ['profile' => $profile],
                'headers' => ['Content-Type' => 'application/json'],
            ]
        );

        return $group;
    }

    /**
     * @param string $oktaGroupId
     * @param mixed[] $profile
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateGroup($oktaGroupId, array $profile)
    {
        $group = $this->request(
            'PUT',
            sprintf('api/v1/groups/%s', $oktaGroupId),
            [
                'json' => ['profile' => $profile],
                'headers' => ['Content-Type' => 'application/json'],
            ]
        );

        return $group;
    }

    /**
     * @param string $oktaGroupId
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteGroup($oktaGroupId)
    {
        $this->request(
            'DELETE',
            sprintf('api/v1/groups/%s', $oktaGroupId)
        );
    }

    /**
     * @param string $groupId
     * @return object[]
     */
    public function listGroupMembers($groupId): array
    {
        return $this->request(
            'GET',
            sprintf('api/v1/groups/%s/users', $groupId)
        );
    }

    /**
     * @param string $groupId
     * @param string $userId
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addMemberToGroup($groupId, $userId)
    {
        $this->request(
            'PUT',
            sprintf('api/v1/groups/%s/users/%s', $groupId, $userId)
        );
    }

    /**
     * @param string $groupId
     * @param string $userId
     * @return void
     */
    public function removeMemberFromGroup($groupId, $userId)
    {
        $this->request(
            'DELETE',
            sprintf('api/v1/groups/%s/users/%s', $groupId, $userId)
        );
    }

    /**
     * @return object[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listApps(): array
    {
        return $this->request(
            'GET',
            'api/v1/apps'
        );
    }

    /**
     * @param string $appId
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getApp($appId)
    {
        return $this->request(
            'GET',
            sprintf('api/v1/apps/%s', $appId)
        );
    }

    /**
     * @param string $appId
     * @return object[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAppGroups($appId)
    {
        return $this->request(
            'GET',
            sprintf('api/v1/apps/%s/groups?limit=1000000', $appId)
        );
    }

    /**
     * @param string $groupId
     * @param string $appId
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addGroupToApp($groupId, $appId)
    {
        return $this->request(
            'PUT',
            sprintf('api/v1/apps/%s/groups/%s', $appId, $groupId)
        );
    }

    /**
     * @param string $groupId
     * @param string $appId
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeGroupFromApp($groupId, $appId)
    {
        $this->request(
            'DELETE',
            sprintf('api/v1/apps/%s/groups/%s', $appId, $groupId)
        );
    }


    /**
     * @param string $method
     * @param string $uri
     * @param mixed[] $options
     * @return array|object|object[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request($method, $uri, array $options = [])
    {
        $options['headers'] = array_merge(
            $options['headers'] ?? [],
            [
                'Accept' => 'application/json',
                'Authorization' => sprintf('SSWS %s', $this->oktaApiToken)
            ]
        );

        $response = $this->httpClient->request(
            $method,
            $uri,
            $options
        );

        return json_decode($response->getBody()->getContents());
    }
}
