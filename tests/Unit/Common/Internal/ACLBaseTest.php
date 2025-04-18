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

use AzureOSS\Storage\Common\Internal\ACLBase;
use AzureOSS\Storage\Common\Internal\Resources;
use AzureOSS\Storage\Common\Internal\Serialization\XmlSerializer;
use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Queue\Models\QueueACL;
use AzureOSS\Storage\Tests\Framework\TestResources;

/**
 * Unit tests for class ACLBase
 *
 * @see      https://github.com/azure/azure-storage-php
 */
class ACLBaseTest extends \PHPUnit\Framework\TestCase
{
    public function testSetGetSignedIdentifiers()
    {
        // Setup
        $sample = TestResources::getQueueACLMultipleEntriesSample();
        $child = new QueueACL();

        $child->fromXmlArray($sample[Resources::XTAG_SIGNED_IDENTIFIERS]);
        $expected = $child->getSignedIdentifiers();
        $expected[0]->setId('newXid');

        // Test
        $child->setSignedIdentifiers($expected);

        // Assert
        self::assertEquals($expected, $child->getSignedIdentifiers());
    }

    public function testToXml()
    {
        // Setup
        $sample = TestResources::getQueueACLMultipleEntriesSample();
        $expected = new QueueACL();
        $expected->fromXmlArray($sample['SignedIdentifiers']);

        $xmlSerializer = new XmlSerializer();

        // Test
        $xml = $expected->toXml($xmlSerializer);

        // Assert
        $array = Utilities::unserialize($xml);
        $acl = QueueACL::create($array);
        self::assertEquals(
            $expected->getSignedIdentifiers(),
            $acl->getSignedIdentifiers(),
        );
    }

    public function testToArray()
    {
        // Setup
        $sample = TestResources::getQueueACLMultipleUnencodedEntriesSample();
        $expected = $sample['SignedIdentifiers'];
        $acl = new QueueACL();
        $acl->fromXmlArray($expected);

        // Test
        $actual = $acl->toArray();

        // Assert
        self::assertEquals(
            $expected['SignedIdentifier'][0],
            $actual[0]['SignedIdentifier'],
        );
        self::assertEquals(
            $expected['SignedIdentifier'][1],
            $actual[1]['SignedIdentifier'],
        );
    }

    public function testAddRemoveSignedIdentifier()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('There can be at most 5 signed identifiers');

        $sample = TestResources::getQueueACLMultipleArraySample();
        $acl = new QueueACL();
        for ($i = 0; $i < 5; ++$i) {
            $acl->addSignedIdentifier(
                $sample[$i]['Id'],
                $sample[$i]['AccessPolicy']['Start'],
                $sample[$i]['AccessPolicy']['Expiry'],
                $sample[$i]['AccessPolicy']['Permission'],
            );
        }

        self::assertCount(5, $acl->getSignedIdentifiers());

        //remove a non-exist signed identifier.
        $acl->removeSignedIdentifier('random_signed_identifier');
        self::assertCount(5, $acl->getSignedIdentifiers());
        //remove an exist signed identifier.
        $acl->removeSignedIdentifier('a');
        self::assertCount(4, $acl->getSignedIdentifiers());
        //add this signed identifier back.
        $acl->addSignedIdentifier(
            $sample[0]['Id'],
            $sample[0]['AccessPolicy']['Start'],
            $sample[0]['AccessPolicy']['Expiry'],
            $sample[0]['AccessPolicy']['Permission'],
        );
        self::assertCount(5, $acl->getSignedIdentifiers());
        //add a signed identifier with existing ID.
        $acl->addSignedIdentifier(
            $sample[0]['Id'],
            $sample[0]['AccessPolicy']['Start'],
            $sample[0]['AccessPolicy']['Expiry'],
            $sample[0]['AccessPolicy']['Permission'],
        );
        self::assertCount(5, $acl->getSignedIdentifiers());
        //add 6th signed identifier, expect error.
        $acl->addSignedIdentifier(
            $sample[5]['Id'],
            $sample[5]['AccessPolicy']['Start'],
            $sample[5]['AccessPolicy']['Expiry'],
            $sample[5]['AccessPolicy']['Permission'],
        );
    }
}
