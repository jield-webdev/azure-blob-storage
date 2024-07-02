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

use AzureOSS\Storage\Blob\Models\ContainerProperties;

/**
 * Unit tests for class ContainerProperties
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class ContainerPropertiesTest extends \PHPUnit\Framework\TestCase
{
    public function testGetETag()
    {
        // Setup
        $properties = new ContainerProperties();
        $expected = '0x8CACB9BD7C6B1B2';
        $properties->setETag($expected);

        // Test
        $actual = $properties->getETag();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testSetETag()
    {
        // Setup
        $properties = new ContainerProperties();
        $expected = '0x8CACB9BD7C6B1B2';

        // Test
        $properties->setETag($expected);

        // Assert
        $actual = $properties->getETag();
        self::assertEquals($expected, $actual);
    }

    public function testGetLastModified()
    {
        // Setup
        $properties = new ContainerProperties();
        $expected = new \DateTime('Fri, 09 Oct 2009 21:04:30 GMT');
        $properties->setLastModified($expected);

        // Test
        $actual = $properties->getLastModified();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testSetLastModified()
    {
        // Setup
        $properties = new ContainerProperties();
        $expected = new \DateTime('Fri, 09 Oct 2009 21:04:30 GMT');

        // Test
        $properties->setLastModified($expected);

        // Assert
        $actual = $properties->getLastModified();
        self::assertEquals($expected, $actual);
    }
}
