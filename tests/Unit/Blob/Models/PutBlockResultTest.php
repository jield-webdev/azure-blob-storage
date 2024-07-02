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

use AzureOSS\Storage\Blob\Models\PutBlockResult;
use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class PutBlockResult
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class PutBlockResultTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        // Setup
        $sample = TestResources::listBlobsOneEntry();
        $expected = $sample['Blobs']['Blob']['Properties'];

        // Test
        $actual = PutBlockResult::create($expected);

        // Assert
        self::assertEquals($expected['Content-MD5'], $actual->getContentMD5());
        self::assertEquals(Utilities::toBoolean($expected['x-ms-request-server-encrypted']), $actual->getRequestServerEncrypted());
    }
}
