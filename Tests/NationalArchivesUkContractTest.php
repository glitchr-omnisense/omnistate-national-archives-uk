<?php

namespace Omnistate\NationalArchivesUk\Tests;

use Omnistate\Model\CivilQuery;
use Omnistate\NationalArchivesUk\NationalArchivesUk;
use Omnistate\Testing\RegistryContractTests;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/** Omnistate's conformance suite, on the register's recorded answers (Tests/Fixtures). */
final class NationalArchivesUkContractTest extends TestCase
{
    use RegistryContractTests;

    protected function registry(HttpClientInterface $http): object
    {
        return new NationalArchivesUk($http);
    }

    protected function recordedAnswers(): array
    {
        return ['*' => static fn (string $method, string $url) => new MockResponse((string) file_get_contents(__DIR__.'/Fixtures/'.match (true) {
            str_contains($url, '/details/') => 'details.json',
            default => 'search.json',
        }), ['response_headers' => ['content-type' => 'application/json']])];
    }

    protected function civilQuery(): ?CivilQuery
    {
        return new CivilQuery(familyName: 'Smith', givenName: 'John');
    }

    protected function civilIdentifier(): ?string
    {
        return '28d83883-ca69-45e3-bbdc-c25fb2da0f1b';
    }
}
