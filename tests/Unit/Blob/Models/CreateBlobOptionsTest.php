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
use AzureOSS\Storage\Blob\Models\CreateBlobOptions;

/**
 * Unit tests for class CreateBlobOptions
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class CreateBlobOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testSetContentType()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setContentType($expected);

        // Test
        $options->setContentType($expected);

        // Assert
        self::assertEquals($expected, $options->getContentType());
    }

    public function testSetContentEncoding()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setContentEncoding($expected);

        // Test
        $options->setContentEncoding($expected);

        // Assert
        self::assertEquals($expected, $options->getContentEncoding());
    }

    public function testSetContentLanguage()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setContentLanguage($expected);

        // Test
        $options->setContentLanguage($expected);

        // Assert
        self::assertEquals($expected, $options->getContentLanguage());
    }

    public function testSetContentMD5()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setContentMD5($expected);

        // Test
        $options->setContentMD5($expected);

        // Assert
        self::assertEquals($expected, $options->getContentMD5());
    }

    public function testSetCacheControl()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setCacheControl($expected);

        // Test
        $options->setCacheControl($expected);

        // Assert
        self::assertEquals($expected, $options->getCacheControl());
    }

    public function testSetContentDisposition()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setContentDisposition($expected);

        // Test
        $options->setContentDisposition($expected);

        // Assert
        self::assertEquals($expected, $options->getContentDisposition());
    }

    public function testSetLeaseId()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $options = new CreateBlobOptions();
        $options->setLeaseId($expected);

        // Test
        $options->setLeaseId($expected);

        // Assert
        self::assertEquals($expected, $options->getLeaseId());
    }

    public function testSetSequenceNumber()
    {
        // Setup
        $expected = 123;
        $options = new CreateBlobOptions();
        $options->setSequenceNumber($expected);

        // Test
        $options->setSequenceNumber($expected);

        // Assert
        self::assertEquals($expected, $options->getSequenceNumber());
    }

    public function testSetMetadata()
    {
        // Setup
        $container = new CreateBlobOptions();
        $expected = ['key1' => 'value1', 'key2' => 'value2'];

        // Test
        $container->setMetadata($expected);

        // Assert
        self::assertEquals($expected, $container->getMetadata());
    }

    public function testGetMetadata()
    {
        // Setup
        $container = new CreateBlobOptions();
        $expected = ['key1' => 'value1', 'key2' => 'value2'];
        $container->setMetadata($expected);

        // Test
        $actual = $container->getMetadata();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testGetAccessConditions()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new CreateBlobOptions();
        $result->setAccessConditions($expected);

        // Test
        $actual = $result->getAccessConditions();

        // Assert
        self::assertEquals($expected, $actual[0]);
    }

    public function testSetAccessConditions()
    {
        // Setup
        $expected = AccessCondition::none();
        $result = new CreateBlobOptions();

        // Test
        $result->setAccessConditions($expected);

        // Assert
        self::assertEquals($expected, $result->getAccessConditions()[0]);
    }
}
