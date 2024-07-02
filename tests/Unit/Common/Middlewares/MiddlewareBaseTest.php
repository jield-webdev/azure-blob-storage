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

namespace AzureOSS\Storage\Tests\Unit\Common\Middlewares;

use AzureOSS\Storage\Common\Middlewares\MiddlewareBase;
use AzureOSS\Storage\Tests\Framework\ReflectionTestBase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Unit tests for class MiddlewareBase
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class MiddlewareBaseTest extends ReflectionTestBase
{
    public function testInvoke()
    {
        $middlewareBase = new MiddlewareBase();
        $client = new Client();
        $handler = static function ($request, $options) use ($client) {
            return $client->sendAsync($request, $options);
        };
        $callable = $middlewareBase($handler);
        $message = 'Not a callable returned by __invoke';
        self::assertIsCallable($callable, $message);
    }

    /**
     * @depends testInvoke
     */
    public function testOnRequest()
    {
        $middlewareBase = new MiddlewareBase();
        $onRequest = self::getMethod('onRequest', $middlewareBase);
        $request = new Request('GET', 'http://www.bing.com');
        $newRequest = $onRequest->invokeArgs($middlewareBase, [$request]);
        self::assertTrue($request === $newRequest, 'Not equal to original request');
    }

    /**
     * @depends testInvoke
     */
    public function testOnFulfilled()
    {
        $middlewareBase = new MiddlewareBase();
        $onFulfilled = self::getMethod('onFulfilled', $middlewareBase);
        $request = new Request('GET', 'http://www.bing.com');
        $callable = $onFulfilled->invokeArgs($middlewareBase, [$request, []]);
        $response = new Response();
        $newResponse = $callable($response);
        self::assertTrue($response === $newResponse, 'Not equal to original response');
    }

    /**
     * @depends testInvoke
     */
    public function testOnRejected()
    {
        $middlewareBase = new MiddlewareBase();
        $onFulfilled = self::getMethod('onRejected', $middlewareBase);
        $request = new Request('GET', 'http://www.bing.com');
        $callable = $onFulfilled->invokeArgs($middlewareBase, [$request, []]);
        $reason = new RequestException('test message', $request);
        $promise = $callable($reason);
        $newReason = null;
        try {
            $promise->wait();
        } catch (RequestException $e) {
            $newReason = $e;
        }
        self::assertTrue($reason === $newReason, 'Not equal to original response');
    }
}
