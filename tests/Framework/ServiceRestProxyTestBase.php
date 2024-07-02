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

namespace AzureOSS\Storage\Tests\Framework;

use AzureOSS\Storage\Common\Internal\Resources;
use AzureOSS\Storage\Common\Models\ServiceProperties;

/**
 * TestBase class for Storage Services test classes.
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class ServiceRestProxyTestBase extends RestProxyTestBase
{
    protected $propertiesChanged;
    protected $defaultProperties;
    protected $connectionString;

    public const NOT_SUPPORTED = 'The storage emulator doesn\'t support this API';
    public const TAKE_TOO_LONG = 'This test takes long time, skip.';
    public const SKIPPED_AFTER_SEVERAL_ATTEMPTS = 'Test skipped after several fails.';

    protected function skipIfEmulated()
    {
        if ($this->isEmulated()) {
            $this->markTestSkipped(self::NOT_SUPPORTED);
        }
    }

    protected function isEmulated()
    {
        return strpos($this->connectionString, Resources::USE_DEVELOPMENT_STORAGE_NAME) !== false;
    }

    public function __construct()
    {
        parent::__construct();
        $this->connectionString = TestResources::getWindowsAzureStorageServicesConnectionString();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->_createDefaultProperties();
    }

    private function _createDefaultProperties()
    {
        $this->propertiesChanged = false;
        $propertiesArray = [];
        $propertiesArray['HourMetrics']['Version'] = '1.0';
        $propertiesArray['HourMetrics']['Enabled'] = 'false';
        $propertiesArray['HourMetrics']['IncludeAPIs'] = 'false';
        $propertiesArray['HourMetrics']['RetentionPolicy']['Enabled'] = 'false';
        $this->defaultProperties = ServiceProperties::create($propertiesArray);
    }

    public function setServiceProperties($properties, $options = null)
    {
        $this->restProxy->setServiceProperties($properties, $options);
        $this->propertiesChanged = true;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->propertiesChanged) {
            $this->restProxy->setServiceProperties($this->defaultProperties);
        }
    }
}
