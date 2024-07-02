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

use AzureOSS\Storage\Blob\Models\ListBlobsOptions;

/**
 * Unit tests for class ListBlobsOptions
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class ListBlobsOptionsTest extends \PHPUnit\Framework\TestCase
{
    public function testSetPrefix()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = 'myprefix';

        // Test
        $options->setPrefix($expected);

        // Assert
        self::assertEquals($expected, $options->getPrefix());
    }

    public function testGetPrefix()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = 'myprefix';
        $options->setPrefix($expected);

        // Test
        $actual = $options->getPrefix();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testSetDelimiter()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = 'mydelimiter';

        // Test
        $options->setDelimiter($expected);

        // Assert
        self::assertEquals($expected, $options->getDelimiter());
    }

    public function testGetDelimiter()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = 'mydelimiter';
        $options->setDelimiter($expected);

        // Test
        $actual = $options->getDelimiter();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testSetMarker()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = 'mymarker';

        // Test
        $options->setMarker($expected);

        // Assert
        self::assertEquals($expected, $options->getNextMarker());
    }

    public function testSetMaxResults()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = 3;

        // Test
        $options->setMaxResults($expected);

        // Assert
        self::assertEquals($expected, $options->getMaxResults());
    }

    public function testGetMaxResults()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = 3;
        $options->setMaxResults($expected);

        // Test
        $actual = $options->getMaxResults();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testSetIncludeMetadata()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = true;

        // Test
        $options->setIncludeMetadata($expected);

        // Assert
        self::assertEquals($expected, $options->getIncludeMetadata());
    }

    public function testGetIncludeMetadata()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = true;
        $options->setIncludeMetadata($expected);

        // Test
        $actual = $options->getIncludeMetadata();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testSetIncludeSnapshots()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = true;

        // Test
        $options->setIncludeSnapshots($expected);

        // Assert
        self::assertEquals($expected, $options->getIncludeSnapshots());
    }

    public function testGetIncludeSnapshots()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = true;
        $options->setIncludeSnapshots($expected);

        // Test
        $actual = $options->getIncludeSnapshots();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testSetIncludeUncommittedBlobs()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = true;

        // Test
        $options->setIncludeUncommittedBlobs($expected);

        // Assert
        self::assertEquals($expected, $options->getIncludeUncommittedBlobs());
    }

    public function testGetIncludeUncommittedBlobs()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = true;
        $options->setIncludeUncommittedBlobs($expected);

        // Test
        $actual = $options->getIncludeUncommittedBlobs();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testSetIncludeDeleted()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = true;

        // Test
        $options->setIncludeDeleted($expected);

        // Assert
        self::assertEquals($expected, $options->getIncludeDeleted());
    }

    public function testGetIncludeDeleted()
    {
        // Setup
        $options = new ListBlobsOptions();
        $expected = true;
        $options->setIncludeDeleted($expected);

        // Test
        $actual = $options->getIncludeDeleted();

        // Assert
        self::assertEquals($expected, $actual);
    }
}
