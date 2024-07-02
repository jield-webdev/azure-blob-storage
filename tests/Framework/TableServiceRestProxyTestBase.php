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

use AzureOSS\Storage\Common\Middlewares\RetryMiddlewareFactory;
use AzureOSS\Storage\Table\TableRestProxy;

/**
 * TestBase class for each unit test class.
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class TableServiceRestProxyTestBase extends ServiceRestProxyTestBase
{
    protected $_createdTables;

    public function setUp(): void
    {
        parent::setUp();
        $tableRestProxy = TableRestProxy::createTableService($this->connectionString);
        $tableRestProxy->pushMiddleware(RetryMiddlewareFactory::create());
        parent::setProxy($tableRestProxy);
        $this->_createdTables = [];
    }

    public function createTable($tableName, $options = null)
    {
        $this->restProxy->createTable($tableName, $options);
        $this->_createdTables[] = $tableName;
    }

    public function deleteTable($tableName)
    {
        if (($key = array_search($tableName, $this->_createdTables, true)) !== false) {
            unset($this->_createdTables[$key]);
        }
        $this->restProxy->deleteTable($tableName);
    }

    public function safeDeleteTable($tableName)
    {
        try {
            $this->deleteTable($tableName);
        } catch (\Exception $e) {
            // Ignore exception and continue, will assume that this table doesn't exist in the sotrage account
            error_log($e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach ($this->_createdTables as $value) {
            $this->safeDeleteTable($value);
        }
    }
}
