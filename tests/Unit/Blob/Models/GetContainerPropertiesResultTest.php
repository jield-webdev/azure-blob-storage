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

use AzureOSS\Storage\Blob\Models\GetContainerPropertiesResult;
use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class GetContainerPropertiesResult
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class GetContainerPropertiesResultTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        // Setup
        $sample = TestResources::listBlobsOneEntry();
        $expectedProperties = $sample['Blobs']['Blob']['Properties'];
        $expectedDate = Utilities::rfc1123ToDateTime($expectedProperties['Last-Modified']);
        $expectedProperties['x-ms-meta-'] = $sample['Blobs']['Blob']['Metadata'];

        // Test
        $result = GetContainerPropertiesResult::create($expectedProperties);

        // Assert
        self::assertEquals(['' => $expectedProperties['x-ms-meta-']], $result->getMetadata());
        self::assertEquals($expectedDate, $result->getLastModified());
        self::assertEquals($expectedProperties['Etag'], $result->getETag());
    }
}
