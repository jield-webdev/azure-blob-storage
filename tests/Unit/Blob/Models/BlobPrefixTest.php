<?php

/**
 * LICENSE: The MIT License (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * https://github.com/azure/azure-storage-php/LICENSE
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @see      https://github.com/azure/azure-storage-php
 */

namespace AzureOSS\Storage\Tests\Unit\Blob\Models;

use AzureOSS\Storage\Blob\Models\BlobPrefix;
use AzureOSS\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class BlobPrefix
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class BlobPrefixTest extends \PHPUnit\Framework\TestCase
{
    public function testSetName()
    {
        // Setup
        $blob = new BlobPrefix();
        $expected = TestResources::QUEUE1_NAME;

        // Test
        $blob->setName($expected);

        // Assert
        self::assertEquals($expected, $blob->getName());
    }

    public function testGetName()
    {
        // Setup
        $blob = new BlobPrefix();
        $expected = TestResources::QUEUE1_NAME;
        $blob->setName($expected);

        // Test
        $actual = $blob->getName();

        // Assert
        self::assertEquals($expected, $actual);
    }
}
