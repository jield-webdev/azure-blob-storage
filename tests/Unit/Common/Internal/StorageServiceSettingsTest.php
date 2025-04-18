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

use AzureOSS\Storage\Common\Internal\Resources;
use AzureOSS\Storage\Common\Internal\StorageServiceSettings;
use AzureOSS\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class StorageServiceSettings
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class StorageServiceSettingsTest extends \PHPUnit\Framework\TestCase
{
    private $_accountName = 'mytestaccount';

    protected function setUp(): void
    {
        $property = new \ReflectionProperty('AzureOSS\Storage\Common\Internal\StorageServiceSettings', 'isInitialized');
        $property->setAccessible(true);
        $property->setValue(false);
    }

    public function testCreateFromConnectionStringWithUseDevStore()
    {
        // Setup
        $connectionString = 'UseDevelopmentStorage=true';
        $expectedName = Resources::DEV_STORE_NAME;
        $expectedKey = Resources::DEV_STORE_KEY;
        $expectedBlobEndpoint = Resources::DEV_STORE_URI . ':10000/devstoreaccount1/';
        $expectedQueueEndpoint = Resources::DEV_STORE_URI . ':10001/devstoreaccount1/';
        $expectedTableEndpoint = Resources::DEV_STORE_URI . ':10002/devstoreaccount1/';

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
    }

    public function testCreateFromConnectionStringWithUseDevStoreUri()
    {
        // Setup
        $myProxyUri = 'http://222.3.5.6';
        $connectionString = "DevelopmentStorageProxyUri=$myProxyUri;UseDevelopmentStorage=true";
        $expectedName = Resources::DEV_STORE_NAME;
        $expectedKey = Resources::DEV_STORE_KEY;
        $expectedBlobEndpoint = $myProxyUri . ':10000/devstoreaccount1/';
        $expectedQueueEndpoint = $myProxyUri . ':10001/devstoreaccount1/';
        $expectedTableEndpoint = $myProxyUri . ':10002/devstoreaccount1/';

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
    }

    public function testCreateFromConnectionStringWithInvalidUseDevStoreFail()
    {
        // Setup
        $invalidValue = 'invalid_value';
        $connectionString = "UseDevelopmentStorage=$invalidValue";
        $expectedMsg = sprintf(
            Resources::INVALID_CONFIG_VALUE,
            $invalidValue,
            implode("\n", ['true']),
        );
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage($expectedMsg);

        // Test
        StorageServiceSettings::createFromConnectionString($connectionString);
    }

    public function testCreateFromConnectionStringWithEmptyConnectionStringFail()
    {
        // Setup
        $connectionString = '';
        $this->expectException('\InvalidArgumentException');

        // Test
        StorageServiceSettings::createFromConnectionString($connectionString);
    }

    public function testGetName()
    {
        // Setup
        $expected = 'myname';
        $setting = new StorageServiceSettings($expected, null, null, null, null, null, null);

        // Test
        $actual = $setting->getName();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testGetKey()
    {
        // Setup
        $expected = 'mykey';
        $setting = new StorageServiceSettings(null, $expected, null, null, null, null);

        // Test
        $actual = $setting->getKey();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testGetBlobEndpointUri()
    {
        // Setup
        $expected = 'myblobEndpointUri';
        $setting = new StorageServiceSettings(null, null, $expected, null, null, null);

        // Test
        $actual = $setting->getBlobEndpointUri();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testGetQueueEndpointUri()
    {
        // Setup
        $expected = 'myqueueEndpointUri';
        $setting = new StorageServiceSettings(null, null, null, $expected, null, null);

        // Test
        $actual = $setting->getQueueEndpointUri();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testGetTableEndpointUri()
    {
        // Setup
        $expected = 'mytableEndpointUri';
        $setting = new StorageServiceSettings(null, null, null, null, $expected, null);

        // Test
        $actual = $setting->getTableEndpointUri();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testGetFileEndpointUri()
    {
        // Setup
        $expected = 'myfileEndpointUri';
        $setting = new StorageServiceSettings(null, null, null, null, null, $expected);

        // Test
        $actual = $setting->getFileEndpointUri();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testGetSasToken()
    {
        // Setup
        $expected = 'mysas=bla&mysas2=bla%2F';
        $setting = new StorageServiceSettings(null, null, null, null, null, null, null, null, null, null, $expected);

        // Test
        $actual = $setting->getSasToken();

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testCreateFromConnectionStringWithAutomatic()
    {
        // Setup
        $protocol = 'https';
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $connectionString = "DefaultEndpointsProtocol=$protocol;AccountName=$expectedName;AccountKey=$expectedKey";
        $expectedBlobEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::BLOB_BASE_DNS_NAME);
        $expectedQueueEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::QUEUE_BASE_DNS_NAME);
        $expectedTableEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::TABLE_BASE_DNS_NAME);

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
    }

    public function testCreateFromConnectionStringWithTableEndpointSpecified()
    {
        // Setup
        $protocol = 'https';
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $expectedTableEndpoint = 'http://myprivatedns.com';
        $expectedBlobEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::BLOB_BASE_DNS_NAME);
        $expectedQueueEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::QUEUE_BASE_DNS_NAME);
        $expectedFileEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::FILE_BASE_DNS_NAME);
        $connectionString = "DefaultEndpointsProtocol=$protocol;AccountName=$expectedName;AccountKey=$expectedKey;TableEndpoint=$expectedTableEndpoint";

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
        self::assertEquals($expectedFileEndpoint, $actual->getFileEndpointUri());
    }

    public function testCreateFromConnectionStringWithBlobEndpointSpecified()
    {
        // Setup
        $protocol = 'https';
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $expectedTableEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::TABLE_BASE_DNS_NAME);
        $expectedBlobEndpoint = 'http://myprivatedns.com';
        $expectedQueueEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::QUEUE_BASE_DNS_NAME);
        $connectionString = "DefaultEndpointsProtocol=$protocol;BlobEndpoint=$expectedBlobEndpoint;AccountName=$expectedName;AccountKey=$expectedKey";
        $expectedFileEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::FILE_BASE_DNS_NAME);

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
        self::assertEquals($expectedFileEndpoint, $actual->getFileEndpointUri());
    }

    public function testCreateFromConnectionStringWithQueueEndpointSpecified()
    {
        // Setup
        $protocol = 'https';
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $expectedTableEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::TABLE_BASE_DNS_NAME);
        $expectedBlobEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::BLOB_BASE_DNS_NAME);
        $expectedFileEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::FILE_BASE_DNS_NAME);
        $expectedQueueEndpoint = 'http://myprivatedns.com';
        $connectionString = "QueueEndpoint=$expectedQueueEndpoint;DefaultEndpointsProtocol=$protocol;AccountName=$expectedName;AccountKey=$expectedKey";

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
        self::assertEquals($expectedFileEndpoint, $actual->getFileEndpointUri());
    }

    public function testCreateFromConnectionStringWithFileEndpointSpecified()
    {
        // Setup
        $protocol = 'https';
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $expectedTableEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::TABLE_BASE_DNS_NAME);
        $expectedBlobEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::BLOB_BASE_DNS_NAME);
        $expectedQueueEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::QUEUE_BASE_DNS_NAME);
        $expectedFileEndpoint = 'http://myprivatedns.com';
        $connectionString = "FileEndpoint=$expectedFileEndpoint;DefaultEndpointsProtocol=$protocol;AccountName=$expectedName;AccountKey=$expectedKey";

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
        self::assertEquals($expectedFileEndpoint, $actual->getFileEndpointUri());
    }

    public function testCreateFromConnectionStringWithQueueAndBlobEndpointSpecified()
    {
        // Setup
        $protocol = 'https';
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $expectedTableEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::TABLE_BASE_DNS_NAME);
        $expectedFileEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::FILE_BASE_DNS_NAME);
        $expectedBlobEndpoint = 'http://myprivateblobdns.com';
        $expectedQueueEndpoint = 'http://myprivatequeuedns.com';
        $connectionString = "QueueEndpoint=$expectedQueueEndpoint;DefaultEndpointsProtocol=$protocol;AccountName=$expectedName;AccountKey=$expectedKey;BlobEndpoint=$expectedBlobEndpoint";

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
        self::assertEquals($expectedFileEndpoint, $actual->getFileEndpointUri());
    }

    public function testCreateFromConnectionStringWithAutomaticMissingProtocolFail()
    {
        // Setup
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $connectionString = "AccountName=$expectedName;AccountKey=$expectedKey";
        $expectedMsg = sprintf(Resources::MISSING_CONNECTION_STRING_SETTINGS, $connectionString);
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage($expectedMsg);

        // Test
        StorageServiceSettings::createFromConnectionString($connectionString);
    }

    public function testCreateFromConnectionStringWithAutomaticMissingAccountNameFail()
    {
        // Setup
        $expectedKey = TestResources::KEY4;
        $connectionString = "DefaultEndpointsProtocol=http;AccountKey=$expectedKey";
        $expectedMsg = sprintf(Resources::MISSING_CONNECTION_STRING_SETTINGS, $connectionString);
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage($expectedMsg);

        // Test
        StorageServiceSettings::createFromConnectionString($connectionString);
    }

    public function testCreateFromConnectionStringWithAutomaticCorruptedAccountKeyFail()
    {
        // Setup
        $expectedName = $this->_accountName;
        $invalidKey = '__A&*INVALID-@Key';
        $connectionString = "DefaultEndpointsProtocol=http;AccountName=$expectedName;AccountKey=$invalidKey";
        $expectedMsg = sprintf(Resources::INVALID_ACCOUNT_KEY_FORMAT, $invalidKey);
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage($expectedMsg);

        // Test
        StorageServiceSettings::createFromConnectionString($connectionString);
    }

    public function testCreateFromConnectionStringWithQueueEndpointSpecfied()
    {
        // Setup
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $expectedTableEndpoint = null;
        $expectedBlobEndpoint = null;
        $expectedQueueEndpoint = 'http://myprivatequeuedns.com';
        $connectionString = "QueueEndpoint=$expectedQueueEndpoint;AccountName=$expectedName;AccountKey=$expectedKey";

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
    }

    public function testCreateFromConnectionStringWithQueueAndBlobEndpointSpecfied()
    {
        // Setup
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $expectedTableEndpoint = null;
        $expectedBlobEndpoint = 'http://myprivateblobdns.com';

        $expectedQueueEndpoint = 'http://myprivatequeuedns.com';
        $connectionString = "QueueEndpoint=$expectedQueueEndpoint;BlobEndpoint=$expectedBlobEndpoint;AccountName=$expectedName;AccountKey=$expectedKey";

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
    }

    public function testCreateFromConnectionStringWithQueueAndBlobAndTableEndpointSpecfied()
    {
        // Setup
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $expectedTableEndpoint = 'http://myprivatetabledns.com';
        $expectedBlobEndpoint = 'http://myprivateblobdns.com';

        $expectedQueueEndpoint = 'http://myprivatequeuedns.com';
        $connectionString = "TableEndpoint=$expectedTableEndpoint;QueueEndpoint=$expectedQueueEndpoint;BlobEndpoint=$expectedBlobEndpoint;AccountName=$expectedName;AccountKey=$expectedKey";

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
    }

    public function testCreateFromConnectionStringWithEndpointSuffixSpecfied()
    {
        // Setup
        $protocol = 'https';
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $expectedBlobEndpoint = "$protocol://$expectedName.blob.core.chinacloudapi.cn";
        $expectedFileSecondaryEndpoint = "$protocol://$expectedName-secondary.file.core.chinacloudapi.cn";
        $connectionString = "DefaultEndpointsProtocol=$protocol;AccountName=$expectedName;AccountKey=$expectedKey;EndpointSuffix=core.chinacloudapi.cn";

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedFileSecondaryEndpoint, $actual->getFileSecondaryEndpointUri());
    }

    public function testCreateFromConnectionStringMissingServicesEndpointsFail()
    {
        // Setup
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $connectionString = "AccountName=$expectedName;AccountKey=$expectedKey";
        $expectedMsg = sprintf(Resources::MISSING_CONNECTION_STRING_SETTINGS, $connectionString);
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage($expectedMsg);

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertNull($actual);
    }

    public function testCreateFromConnectionStringWithInvalidBlobEndpointUriFail()
    {
        // Setup
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $invalidUri = 'https://www.invalid_domain';
        $connectionString = "BlobEndpoint=$invalidUri;DefaultEndpointsProtocol=http;AccountName=$expectedName;AccountKey=$expectedKey";
        $expectedMsg = sprintf(Resources::INVALID_CONFIG_URI, $invalidUri);
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage($expectedMsg);

        // Test
        StorageServiceSettings::createFromConnectionString($connectionString);
    }

    public function testCreateFromConnectionStringWithInvalidSettingKeyFail()
    {
        // Setup
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $validKeys = [];
        $validKeys[] = Resources::USE_DEVELOPMENT_STORAGE_NAME;
        $validKeys[] = Resources::DEVELOPMENT_STORAGE_PROXY_URI_NAME;
        $validKeys[] = Resources::DEFAULT_ENDPOINTS_PROTOCOL_NAME;
        $validKeys[] = Resources::ACCOUNT_NAME_NAME;
        $validKeys[] = Resources::ACCOUNT_KEY_NAME;
        $validKeys[] = Resources::SAS_TOKEN_NAME;
        $validKeys[] = Resources::BLOB_ENDPOINT_NAME;
        $validKeys[] = Resources::QUEUE_ENDPOINT_NAME;
        $validKeys[] = Resources::TABLE_ENDPOINT_NAME;
        $validKeys[] = Resources::FILE_ENDPOINT_NAME;
        $invalidKey = 'InvalidKey';
        $connectionString = "DefaultEndpointsProtocol=http;$invalidKey=MyValue;AccountName=$expectedName;AccountKey=$expectedKey";
        $expectedMsg = sprintf(
            Resources::INVALID_CONNECTION_STRING_SETTING_KEY,
            $invalidKey,
            implode("\n", $validKeys),
        );
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage($expectedMsg);

        // Test
        StorageServiceSettings::createFromConnectionString($connectionString);
    }

    public function testCreateFromConnectionStringWithCaseInsensitive()
    {
        // Setup
        $protocol = 'https';
        $expectedName = $this->_accountName;
        $expectedKey = TestResources::KEY4;
        $connectionString = "defaultendpointsprotocol=$protocol;accountname=$expectedName;accountkey=$expectedKey";
        $expectedBlobEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::BLOB_BASE_DNS_NAME);
        $expectedQueueEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::QUEUE_BASE_DNS_NAME);
        $expectedTableEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::TABLE_BASE_DNS_NAME);
        $expectedFileEndpoint = sprintf(Resources::SERVICE_URI_FORMAT, $protocol, $expectedName, Resources::FILE_BASE_DNS_NAME);

        // Test
        $actual = StorageServiceSettings::createFromConnectionString($connectionString);

        // Assert
        self::assertEquals($expectedName, $actual->getName());
        self::assertEquals($expectedKey, $actual->getKey());
        self::assertEquals($expectedBlobEndpoint, $actual->getBlobEndpointUri());
        self::assertEquals($expectedQueueEndpoint, $actual->getQueueEndpointUri());
        self::assertEquals($expectedTableEndpoint, $actual->getTableEndpointUri());
        self::assertEquals($expectedFileEndpoint, $actual->getFileEndpointUri());
    }
}
