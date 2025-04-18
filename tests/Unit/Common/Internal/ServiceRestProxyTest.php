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
use AzureOSS\Storage\Common\Internal\ServiceRestProxy;
use AzureOSS\Storage\Tests\Framework\ReflectionTestBase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Unit tests for class ServiceRestProxy
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class ServiceRestProxyTest extends ReflectionTestBase
{
    public function testConstruct()
    {
        // Setup
        $primaryUri = 'http://www.microsoft.com';
        $secondaryUri = 'http://www.bing.com';
        $accountName = 'myaccount';
        $options['https'] = ['verify' => __DIR__ . '/TestFiles/cacert.pem'];

        // Test
        $proxy = new ServiceRestProxy(
            $primaryUri,
            $secondaryUri,
            $accountName,
            $options,
        );

        // Assert
        self::assertNotNull($proxy);
        self::assertEquals($accountName, $proxy->getAccountName());

        // Auto append an '/' at the end of uri.
        self::assertEquals($primaryUri . '/', (string) ($proxy->getPsrPrimaryUri()));
        self::assertEquals($secondaryUri . '/', (string) ($proxy->getPsrSecondaryUri()));

        return $proxy;
    }

    public function testSettingVerifyOptions()
    {
        // Setup
        $primaryUri = 'http://www.microsoft.com';
        $secondaryUri = 'http://www.bing.com';
        $accountName = 'myaccount';
        $options['http'] = ['verify' => __DIR__ . '/TestFiles/cacert.pem'];

        // Test
        $proxy = new ServiceRestProxy(
            $primaryUri,
            $secondaryUri,
            $accountName,
            $options,
        );

        $ref = new \ReflectionProperty(ServiceRestProxy::class, 'client');
        $ref->setAccessible(true);
        /** @var Client $client */
        $client = $ref->getValue($proxy);
        self::assertSame($options['http']['verify'], $client->getConfig('verify'));
    }

    /**
     * @depends testConstruct
     */
    public function testGroupQueryValues()
    {
        // Setup
        $values = ['A', 'B', 'C'];
        $expected = 'A,B,C';

        // Test
        $actual = ServiceRestProxy::groupQueryValues($values);

        // Assert
        self::assertEquals($expected, $actual);
    }

    /**
     * @depends testConstruct
     */
    public function testGroupQueryValuesWithUnorderedValues()
    {
        // Setup
        $values = ['B', 'C', 'A'];
        $expected = 'A,B,C';

        // Test
        $actual = ServiceRestProxy::groupQueryValues($values);

        // Assert
        self::assertEquals($expected, $actual);
    }

    public function testGroupQueryValuesWithNulls()
    {
        // Setup
        $values = [null, '', null];

        // Test
        $actual = ServiceRestProxy::groupQueryValues($values);

        // Assert
        self::assertEmpty($actual);
    }

    /**
     * @depends testConstruct
     */
    public function testGroupQueryValuesWithMix()
    {
        // Setup
        $values = [null, 'B', 'C', ''];
        $expected = 'B,C';

        // Test
        $actual = ServiceRestProxy::groupQueryValues($values);

        // Assert
        self::assertEquals($expected, $actual);
    }

    /**
     * @depends testConstruct
     */
    public function testPostParameter($restRestProxy)
    {
        // Setup
        $postParameters = [];
        $key = 'a';
        $expected = 'b';

        // Test
        $processedPostParameters = $restRestProxy->addPostParameter($postParameters, $key, $expected);
        $actual = $processedPostParameters[$key];

        // Assert
        self::assertEquals(
            $expected,
            $actual,
        );
    }

    /**
     * @depends testConstruct
     */
    public function testGenerateMetadataHeader($proxy)
    {
        // Setup
        $metadata = ['key1' => 'value1', 'MyName' => 'WindowsAzure', 'MyCompany' => 'Microsoft_'];
        $expected = [];
        foreach ($metadata as $key => $value) {
            $expected[Resources::X_MS_META_HEADER_PREFIX . $key] = $value;
        }

        // Test
        $actual = $proxy->generateMetadataHeaders($metadata);

        // Assert
        self::assertEquals($expected, $actual);
    }

    /**
     * @depends testConstruct
     */
    public function testGenerateMetadataHeaderInvalidNameFail($proxy)
    {
        // Setup
        $metadata = ['key1' => "value1\n", 'MyName' => "\rAzurr", 'MyCompany' => "Micr\r\nosoft_"];
        $this->expectException(get_class(new \InvalidArgumentException(Resources::INVALID_META_MSG)));

        // Test
        $proxy->generateMetadataHeaders($metadata);
    }

    /**
     * @depends testConstruct
     */
    public function testOnRejectedWithException($proxy)
    {
        // Setup
        $this->expectException(\Exception::class);
        $onRejected = self::getMethod('onRejected', $proxy);

        // Test
        $onRejected->invokeArgs($proxy, [new \Exception('test message'), 200]);
    }

    /**
     * @depends testConstruct
     */
    public function testOnRejectedWithString($proxy)
    {
        // Setup
        $message = 'test message';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage($message);
        $onRejected = self::getMethod('onRejected', $proxy);

        // Test
        $onRejected->invokeArgs($proxy, [$message, 200]);
    }

    /**
     * @depends testConstruct
     */
    public function testOnRejectedWithRequestExceptionNullResponse($proxy)
    {
        // Setup
        $this->expectException(RequestException::class);
        $onRejected = self::getMethod('onRejected', $proxy);

        $request = new Request('GET', 'http://www.bing.com');
        $reason = new RequestException('test message', $request);

        // Test
        $onRejected->invokeArgs($proxy, [$reason, 200]);
    }

    /**
     * @depends testConstruct
     */
    public function testOnRejectedWithRequestExceptionUnexpectedResponse($proxy)
    {
        // Setup
        $this->expectException(\AzureOSS\Storage\Common\Exceptions\ServiceException::class);
        $onRejected = self::getMethod('onRejected', $proxy);

        $request = new Request('GET', 'http://www.bing.com');
        $response = new Response(408, ['test_header' => 'test_header_value']);
        $reason = new RequestException('test message', $request, $response);

        // Test
        $onRejected->invokeArgs($proxy, [$reason, 200]);
    }

    /**
     * @depends testConstruct
     */
    public function testOnRejectedWithRequestExceptionExpectedResponse($proxy)
    {
        // Setup
        $onRejected = self::getMethod('onRejected', $proxy);

        $request = new Request('GET', 'http://www.bing.com');
        $response = new Response(200, ['test_header' => 'test_header_value']);
        $reason = new RequestException('test message', $request, $response);

        // Test
        $actual = $onRejected->invokeArgs($proxy, [$reason, 200]);

        // Assert
        self::assertSame($response, $actual);
    }
}
