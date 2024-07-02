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

use AzureOSS\Storage\Blob\Internal\BlobResources;
use AzureOSS\Storage\Blob\Models\ContainerAccessPolicy;
use AzureOSS\Storage\Tests\Unit\Common\Models\AccessPolicyTest;

/**
 * Unit tests for class ContainerAccessPolicy
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class ContainerPolicyTest extends AccessPolicyTest
{
    protected function createAccessPolicy()
    {
        return new ContainerAccessPolicy();
    }

    protected function getResourceType()
    {
        return BlobResources::RESOURCE_TYPE_CONTAINER;
    }
}
