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

namespace AzureOSS\Storage\Tests\Unit\Common\Internal\Authentication;

use AzureOSS\Storage\Common\Internal\Resources;
use AzureOSS\Storage\Common\Internal\ServiceRestProxy;
use AzureOSS\Storage\Tests\Framework\TestResources;
use AzureOSS\Storage\Tests\Mock\Common\Internal\Authentication\SharedKeyAuthSchemeMock;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;

/**
 * Unit tests for SharedKeyAuthScheme class.
 *
 * @see       https://github.com/azure/azure-storage-php
 */
class SharedKeyAuthSchemeTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $expected = [];
        $expected[] = Resources::CONTENT_ENCODING;
        $expected[] = Resources::CONTENT_LANGUAGE;
        $expected[] = Resources::CONTENT_LENGTH;
        $expected[] = Resources::CONTENT_MD5;
        $expected[] = Resources::CONTENT_TYPE;
        $expected[] = Resources::DATE;
        $expected[] = Resources::IF_MODIFIED_SINCE;
        $expected[] = Resources::IF_MATCH;
        $expected[] = Resources::IF_NONE_MATCH;
        $expected[] = Resources::IF_UNMODIFIED_SINCE;
        $expected[] = Resources::RANGE;

        $mock = new SharedKeyAuthSchemeMock(TestResources::ACCOUNT_NAME, TestResources::KEY4);

        self::assertEquals(TestResources::ACCOUNT_NAME, $mock->getAccountName());
        self::assertEquals(TestResources::KEY4, $mock->getAccountKey());
        self::assertEquals($expected, $mock->getIncludedHeaders());
    }

    public function testComputeSignatureSimple()
    {
        $httpMethod = 'GET';
        $queryParams = [Resources::QP_COMP => 'list'];
        $url = TestResources::URI1;
        $date = TestResources::DATE1;
        $apiVersion = '2016-05-31';
        $accountName = TestResources::ACCOUNT_NAME;
        $headers = [Resources::X_MS_DATE => $date, Resources::X_MS_VERSION => $apiVersion];
        $expected = "GET\n\n\n\n\n\n\n\n\n\n\n\n" . Resources::X_MS_DATE . ":$date\n" . Resources::X_MS_VERSION .
                ":$apiVersion\n/$accountName" . parse_url($url, PHP_URL_PATH) . "\ncomp:list";
        $mock = new SharedKeyAuthSchemeMock($accountName, TestResources::KEY4);

        $actual = $mock->computeSignatureMock($headers, $url, $queryParams, $httpMethod);

        self::assertEquals($expected, $actual);
    }

    public function testGetAuthorizationHeaderSimple()
    {
        $accountName = TestResources::ACCOUNT_NAME;
        $apiVersion = '2016-05-31';
        $accountKey = TestResources::KEY4;
        $url = TestResources::URI2;
        $date1 = TestResources::DATE2;
        $headers = [Resources::X_MS_VERSION => $apiVersion, Resources::X_MS_DATE => $date1];
        $queryParams = [Resources::QP_COMP => 'list'];
        $httpMethod = 'GET';
        $expected = 'SharedKey ' . $accountName;

        $mock = new SharedKeyAuthSchemeMock($accountName, $accountKey);

        $actual = $mock->getAuthorizationHeader($headers, $url, $queryParams, $httpMethod);

        self::assertEquals($expected, substr($actual, 0, \strlen($expected)));
    }

    public function testComputeCanonicalizedHeadersMock()
    {
        $date = TestResources::DATE1;
        $headers = [];
        $headers[Resources::X_MS_DATE] = $date;
        $headers[Resources::X_MS_VERSION] = '2016-05-31';
        $expected = [];
        $expected[] = Resources::X_MS_DATE . ':' . $date;
        $expected[] = Resources::X_MS_VERSION . ':' . $headers[Resources::X_MS_VERSION];
        $mock = new SharedKeyAuthSchemeMock(TestResources::ACCOUNT_NAME, TestResources::KEY4);

        $actual = $mock->computeCanonicalizedHeadersMock($headers);

        self::assertEquals($expected, $actual);
    }

    public function testComputeCanonicalizedResourceMockSimple()
    {
        $queryVariables = [];
        $queryVariables['COMP'] = 'list';
        $accountName = TestResources::ACCOUNT_NAME;
        $url = TestResources::URI1;
        $expected = '/' . $accountName . parse_url($url, PHP_URL_PATH) . "\n" . 'comp:list';
        $mock = new SharedKeyAuthSchemeMock($accountName, TestResources::KEY4);

        $actual = $mock->computeCanonicalizedResourceMock($url, $queryVariables);

        self::assertEquals($expected, $actual);
    }

    public function testComputeCanonicalizedResourceMockMultipleValues()
    {
        $queryVariables = [];
        $queryVariables['COMP'] = 'list';
        $queryVariables[Resources::QP_INCLUDE] = ServiceRestProxy::groupQueryValues(
            [
                'snapshots',
                'metadata',
                'uncommittedblobs',
            ],
        );
        $expectedQueryPart = "comp:list\ninclude:metadata,snapshots,uncommittedblobs";
        $accountName = TestResources::ACCOUNT_NAME;
        $url = TestResources::URI1;
        $expected = '/' . $accountName . parse_url($url, PHP_URL_PATH) . "\n" . $expectedQueryPart;
        $mock = new SharedKeyAuthSchemeMock($accountName, TestResources::KEY4);

        $actual = $mock->computeCanonicalizedResourceMock($url, $queryVariables);

        self::assertEquals($expected, $actual);
    }

    public function testComputeCanonicalizedResourceForTableMock()
    {
        $queryVariables = [];
        $queryVariables['COMP'] = 'list';
        $accountName = TestResources::ACCOUNT_NAME;
        $url = TestResources::URI1;
        $expected = '/' . $accountName . parse_url($url, PHP_URL_PATH) . '?comp=list';
        $mock = new SharedKeyAuthSchemeMock($accountName, TestResources::KEY4);

        $actual = $mock->computeCanonicalizedResourceForTableMock($url, $queryVariables);

        self::assertEquals($expected, $actual);
    }

    public function testSignRequest()
    {
        // Setup
        $mock = new SharedKeyAuthSchemeMock(TestResources::ACCOUNT_NAME, TestResources::KEY4);
        $uri = new Uri(TestResources::URI2);
        $request = new Request('Get', $uri, [], null);

        // Test
        $actual = $mock->signRequest($request);

        // Assert
        self::assertArrayHasKey(strtolower(Resources::AUTHENTICATION), $actual->getHeaders());
    }
}
