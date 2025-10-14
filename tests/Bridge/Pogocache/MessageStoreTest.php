<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\AI\Chat\Tests\Bridge\Pogocache;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\AI\Chat\Bridge\Pogocache\MessageStore;
use Symfony\AI\Chat\Tests\MessageStoreTestCase;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

final class MessageStoreTest extends MessageStoreTestCase
{
    public function testStoreCannotSetupOnInvalidResponse()
    {
        $httpClient = new MockHttpClient([
            new JsonMockResponse([], [
                'http_code' => 400,
            ]),
        ], 'http://127.0.0.1:9401');

        $store = new MessageStore($httpClient, 'http://127.0.0.1:9401', 'test', 'test');

        self::expectException(ClientException::class);
        self::expectExceptionMessage('HTTP 400 returned for "http://127.0.0.1:9401/test?auth=test".');
        self::expectExceptionCode(400);
        $store->setup();
    }

    public function testStoreCanSetup()
    {
        $httpClient = new MockHttpClient([
            new JsonMockResponse('Stored', [
                'http_code' => 200,
            ]),
        ], 'http://127.0.0.1:9401');

        $store = new MessageStore($httpClient, 'http://127.0.0.1:9401', 'test', 'test');

        $store->setup();

        $this->assertSame(1, $httpClient->getRequestsCount());
    }

    public function testStoreCannotDropOnInvalidResponse()
    {
        $httpClient = new MockHttpClient([
            new JsonMockResponse('Not Found', [
                'http_code' => 404,
            ]),
        ], 'http://127.0.0.1:9401');

        $store = new MessageStore($httpClient, 'http://127.0.0.1:9401', 'test', 'test');

        self::expectException(ClientException::class);
        self::expectExceptionMessage('HTTP 404 returned for "http://127.0.0.1:9401/test?auth=test".');
        self::expectExceptionCode(404);
        $store->drop();
    }

    public function testStoreCanDrop()
    {
        $httpClient = new MockHttpClient([
            new JsonMockResponse('Deleted', [
                'http_code' => 200,
            ]),
        ], 'http://127.0.0.1:9401');

        $store = new MessageStore($httpClient, 'http://127.0.0.1:9401', 'test', 'test');

        $store->drop();

        $this->assertSame(1, $httpClient->getRequestsCount());
    }

    public function testStoreCannotSaveOnInvalidResponse()
    {
        $httpClient = new MockHttpClient([
            new JsonMockResponse('', [
                'http_code' => 400,
            ]),
        ], 'http://127.0.0.1:9401');

        $store = new MessageStore($httpClient, 'http://127.0.0.1:9401', 'test', 'test');

        self::expectException(ClientException::class);
        self::expectExceptionMessage('HTTP 400 returned for "http://127.0.0.1:9401/test?auth=test".');
        self::expectExceptionCode(400);
        $store->save(new MessageBag(Message::ofUser('Hello there')));
    }

    public function testStoreCanSave()
    {
        $httpClient = new MockHttpClient([
            new JsonMockResponse('Stored', [
                'http_code' => 200,
            ]),
        ], 'http://127.0.0.1:9401');

        $store = new MessageStore($httpClient, 'http://127.0.0.1:9401', 'test', 'test');

        $store->save(new MessageBag(Message::ofUser('Hello there')));

        $this->assertSame(1, $httpClient->getRequestsCount());
    }

    public function testStoreCannotLoadMessagesOnInvalidResponse()
    {
        $httpClient = new MockHttpClient([
            new JsonMockResponse('', [
                'http_code' => 400,
            ]),
        ], 'http://127.0.0.1:9401');

        $store = new MessageStore($httpClient, 'http://127.0.0.1:9401', 'test', 'test');

        self::expectException(ClientException::class);
        self::expectExceptionMessage('HTTP 400 returned for "http://127.0.0.1:9401/test?auth=test".');
        self::expectExceptionCode(400);
        $store->load();
    }

    /**
     * @param array<mixed, mixed> $payload
     */
    #[DataProvider('provideMessages')]
    public function testStoreCanLoadMessages(array $payload)
    {
        $httpClient = new MockHttpClient([
            new JsonMockResponse([$payload], [
                'http_code' => 200,
            ]),
        ], 'http://127.0.0.1:9401');

        $store = new MessageStore($httpClient, 'http://127.0.0.1:9401', 'test', 'test');

        $messageBag = $store->load();

        $this->assertCount(1, $messageBag);
        $this->assertSame(1, $httpClient->getRequestsCount());
    }
}
