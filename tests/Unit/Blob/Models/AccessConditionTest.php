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

use AzureOSS\Storage\Blob\Models\AccessCondition;
use AzureOSS\Storage\Common\Internal\Resources;

/**
 * Unit tests for class AccessCondition
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class AccessConditionTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        // Setup
        $expectedHeaderType = Resources::IF_MATCH;
        $expectedValue = '0x8CAFB82EFF70C46';

        // Test
        $actual = AccessCondition::ifMatch($expectedValue);

        // Assert
        self::assertEquals($expectedHeaderType, $actual->getHeader());
        self::assertEquals($expectedValue, $actual->getValue());
    }

    public function testNone()
    {
        // Setup
        $expectedHeader = Resources::EMPTY_STRING;
        $expectedValue = null;

        // Test
        $actual = AccessCondition::none();

        // Assert
        self::assertEquals($expectedHeader, $actual->getHeader());
        self::assertEquals($expectedValue, $actual->getValue());
    }

    public function testIfModifiedSince()
    {
        // Setup
        $expectedHeader = Resources::IF_MODIFIED_SINCE;
        $expectedValue = new \DateTime('Sun, 25 Sep 2011 00:42:49 GMT');

        // Test
        $actual = AccessCondition::ifModifiedSince($expectedValue);

        // Assert
        self::assertEquals($expectedHeader, $actual->getHeader());
        self::assertEquals($expectedValue, $actual->getValue());
    }

    public function testIfMatch()
    {
        // Setup
        $expectedHeader = Resources::IF_MATCH;
        $expectedValue = '0x8CAFB82EFF70C46';

        // Test
        $actual = AccessCondition::ifMatch($expectedValue);

        // Assert
        self::assertEquals($expectedHeader, $actual->getHeader());
        self::assertEquals($expectedValue, $actual->getValue());
    }

    public function testIfNoneMatch()
    {
        // Setup
        $expectedHeader = Resources::IF_NONE_MATCH;
        $expectedValue = '0x8CAFB82EFF70C46';

        // Test
        $actual = AccessCondition::ifNoneMatch($expectedValue);

        // Assert
        self::assertEquals($expectedHeader, $actual->getHeader());
        self::assertEquals($expectedValue, $actual->getValue());
    }

    public function testIfNotModifiedSince()
    {
        // Setup
        $expectedHeader = Resources::IF_UNMODIFIED_SINCE;
        $expectedValue = new \DateTime('Sun, 25 Sep 2011 00:42:49 GMT');

        // Test
        $actual = AccessCondition::ifNotModifiedSince($expectedValue);

        // Assert
        self::assertEquals($expectedHeader, $actual->getHeader());
        self::assertEquals($expectedValue, $actual->getValue());
    }

    public function testIsValidWithValid()
    {
        // Test
        $actual = AccessCondition::isValid(Resources::IF_MATCH);

        // Assert
        self::assertTrue($actual);
    }

    public function testIsValidWithInvalid()
    {
        // Test
        $actual = AccessCondition::isValid('1234');

        // Assert
        self::assertFalse($actual);
    }
}
