<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\AI\Chat\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\AI\Platform\Message\AssistantMessage;
use Symfony\AI\Platform\Message\Content\Text;
use Symfony\AI\Platform\Message\SystemMessage;
use Symfony\AI\Platform\Message\ToolCallMessage;
use Symfony\AI\Platform\Message\UserMessage;
use Symfony\Component\Uid\Uuid;

class MessageStoreTestCase extends TestCase
{
    public static function provideMessages(): \Generator
    {
        yield UserMessage::class => [
            [
                'id' => Uuid::v7()->toRfc4122(),
                'type' => UserMessage::class,
                'content' => '',
                'contentAsBase64' => [
                    [
                        'type' => Text::class,
                        'content' => 'What is the Symfony framework?',
                    ],
                ],
                'toolsCalls' => [],
                'metadata' => [],
            ],
        ];
        yield SystemMessage::class => [
            [
                'id' => Uuid::v7()->toRfc4122(),
                'type' => SystemMessage::class,
                'content' => 'Hello there',
                'contentAsBase64' => [],
                'toolsCalls' => [],
                'metadata' => [],
            ],
        ];
        yield AssistantMessage::class => [
            [
                'id' => Uuid::v7()->toRfc4122(),
                'type' => AssistantMessage::class,
                'content' => 'Hello there',
                'contentAsBase64' => [],
                'toolsCalls' => [
                    [
                        'id' => '1',
                        'name' => 'foo',
                        'function' => [
                            'name' => 'foo',
                            'arguments' => '{}',
                        ],
                    ],
                ],
                'metadata' => [],
            ],
        ];
        yield ToolCallMessage::class => [
            [
                'id' => Uuid::v7()->toRfc4122(),
                'type' => ToolCallMessage::class,
                'content' => 'Hello there',
                'contentAsBase64' => [],
                'toolsCalls' => [
                    'id' => '1',
                    'name' => 'foo',
                    'function' => [
                        'name' => 'foo',
                        'arguments' => '{}',
                    ],
                ],
                'metadata' => [],
            ],
        ];
    }
}
