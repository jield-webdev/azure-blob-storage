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

namespace AzureOSS\Storage\Tests\Unit\Common\Internal;

use AzureOSS\Storage\Common\Internal\ConnectionStringSource;

/**
 * Unit tests for class ConnectionStringSource
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class ConnectionStringSourceTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $property = new \ReflectionProperty('AzureOSS\Storage\Common\Internal\ConnectionStringSource', '_isInitialized');
        $property->setAccessible(true);
        $property->setValue(null);
    }

    public function testEnvironmentSource()
    {
        // Setup
        $key = 'key';
        $value = 'value';
        putenv("$key=$value");

        // Test
        $actual = ConnectionStringSource::environmentSource($key);

        // Assert
        self::assertEquals($value, $actual);

        // Clean
        putenv($key);
    }

    public function testGetDefaultSources()
    {
        // Setup
        $expectedKeys = [ConnectionStringSource::ENVIRONMENT_SOURCE];

        // Test
        $actual = ConnectionStringSource::getDefaultSources();

        // Assert
        $keys = array_keys($actual);
        self::assertEquals(count($expectedKeys), count($keys));
        for ($index = 0; $index < count($expectedKeys); ++$index) {
            self::assertEquals($expectedKeys[$index], $keys[$index]);
        }
    }
}
