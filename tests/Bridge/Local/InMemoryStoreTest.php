<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\AI\Chat\Tests\Bridge\Local;

use PHPUnit\Framework\TestCase;
use Symfony\AI\Chat\Bridge\Local\InMemoryStore;
use Symfony\AI\Platform\Message\Content\Text;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\AI\Platform\Message\UserMessage;

final class InMemoryStoreTest extends TestCase
{
    public function testSetupInitializesEmptyMessageBag()
    {
        $store = new InMemoryStore();
        $store->setup();

        $messages = $store->load();

        $this->assertInstanceOf(MessageBag::class, $messages);
        $this->assertCount(0, $messages);
    }

    public function testSaveStoresMessageBag()
    {
        $store = new InMemoryStore();

        $messageBag = new MessageBag();
        $messageBag->add(Message::ofUser('Hello'));
        $messageBag->add(Message::ofAssistant('Hi there'));

        $store->save($messageBag);

        $loadedMessages = $store->load();

        $this->assertSame($messageBag, $loadedMessages);
        $this->assertCount(2, $loadedMessages);
    }

    public function testLoadReturnsEmptyMessageBagWhenNotInitialized()
    {
        $store = new InMemoryStore();

        $messages = $store->load();

        $this->assertInstanceOf(MessageBag::class, $messages);
        $this->assertCount(0, $messages);
    }

    public function testLoadReturnsStoredMessages()
    {
        $store = new InMemoryStore();
        $store->setup();

        $messageBag = new MessageBag();
        $messageBag->add(Message::ofUser('Test message'));

        $store->save($messageBag);

        $loadedMessages = $store->load();

        $this->assertCount(1, $loadedMessages);
        $messages = $loadedMessages->getMessages();
        $this->assertInstanceOf(UserMessage::class, $messages[0]);
        $this->assertInstanceOf(Text::class, $messages[0]->content[0]);
        $this->assertSame('Test message', $messages[0]->content[0]->text);
    }

    public function testDropClearsMessages()
    {
        $store = new InMemoryStore();

        $messageBag = new MessageBag();
        $messageBag->add(Message::ofUser('Message 1'));
        $messageBag->add(Message::ofUser('Message 2'));

        $store->save($messageBag);
        $this->assertCount(2, $store->load());

        $store->drop();

        $messages = $store->load();
        $this->assertCount(0, $messages);
    }

    public function testSetupOptions()
    {
        $store = new InMemoryStore();

        $store->setup(['foo' => 'bar']);

        $messages = $store->load();
        $this->assertInstanceOf(MessageBag::class, $messages);
    }
}
