<?php
/**
 * Copyright 2014, 2015 Brandon Black <blblack@gmail.com>
 *
 * This file is part of IPSet.
 *
 * Foobar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Foobar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with IPSet.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace IPSet\Test;

use IPSet\IPSet;

/**
 * @group IPSet
 */
class IPSetTest extends \PHPUnit_Framework_TestCase {

	protected function setUp() {
		parent::setUp();

		// The IPSet::recOptimize method curses over 100 times.
		// This setting defaults to 100.
		ini_set( 'xdebug.max_nesting_level', 1000 );
	}

	/**
	 * Provides test cases for IPSetTest::testIPSet
	 *
	 * Returns an array of test cases. Each case is an array of (description,
	 * config, tests).  Description is just text output for failure messages,
	 * config is an array constructor argument for IPSet, and the tests are
	 * an array of IP => expected (boolean) result against the config dataset.
	 */
	public static function provideIPSets() {
		return array(
			array(
				'old_list_subset',
				array(
					'208.80.152.162',
					'10.64.0.123',
					'10.64.0.124',
					'10.64.0.125',
					'10.64.0.126',
					'10.64.0.127',
					'10.64.0.128',
					'10.64.0.129',
					'10.64.32.104',
					'10.64.32.105',
					'10.64.32.106',
					'10.64.32.107',
					'91.198.174.45',
					'91.198.174.46',
					'91.198.174.47',
					'91.198.174.57',
					'2620:0:862:1:A6BA:DBFF:FE30:CFB3',
					'91.198.174.58',
					'2620:0:862:1:A6BA:DBFF:FE38:FFDA',
					'208.80.152.16',
					'208.80.152.17',
					'208.80.152.18',
					'208.80.152.19',
					'91.198.174.102',
					'91.198.174.103',
					'91.198.174.104',
					'91.198.174.105',
					'91.198.174.106',
					'91.198.174.107',
					'91.198.174.81',
					'2620:0:862:1:26B6:FDFF:FEF5:B2D4',
					'91.198.174.82',
					'2620:0:862:1:26B6:FDFF:FEF5:ABB4',
					'10.20.0.113',
					'2620:0:862:102:26B6:FDFF:FEF5:AD9C',
					'10.20.0.114',
					'2620:0:862:102:26B6:FDFF:FEF5:7C38',
				),
				array(
					'0.0.0.0' => false,
					'255.255.255.255' => false,
					'10.64.0.122' => false,
					'10.64.0.123' => true,
					'10.64.0.124' => true,
					'10.64.0.129' => true,
					'10.64.0.130' => false,
					'91.198.174.81' => true,
					'91.198.174.80' => false,
					'0::0' => false,
					'ffff:ffff:ffff:ffff:FFFF:FFFF:FFFF:FFFF' => false,
					'2001:db8::1234' => false,
					'2620:0:862:1:26b6:fdff:fef5:abb3' => false,
					'2620:0:862:1:26b6:fdff:fef5:abb4' => true,
					'2620:0:862:1:26b6:fdff:fef5:abb5' => false,
				),
			),
			array(
				'new_cidr_set',
				array(
					'208.80.154.0/26',
					'2620:0:861:1::/64',
					'208.80.154.128/26',
					'2620:0:861:2::/64',
					'208.80.154.64/26',
					'2620:0:861:3::/64',
					'208.80.155.96/27',
					'2620:0:861:4::/64',
					'10.64.0.0/22',
					'2620:0:861:101::/64',
					'10.64.16.0/22',
					'2620:0:861:102::/64',
					'10.64.32.0/22',
					'2620:0:861:103::/64',
					'10.64.48.0/22',
					'2620:0:861:107::/64',
					'91.198.174.0/25',
					'2620:0:862:1::/64',
					'10.20.0.0/24',
					'2620:0:862:102::/64',
					'10.128.0.0/24',
					'2620:0:863:101::/64',
					'10.2.4.26',
				),
				array(
					'0.0.0.0' => false,
					'255.255.255.255' => false,
					'10.2.4.25' => false,
					'10.2.4.26' => true,
					'10.2.4.27' => false,
					'10.20.0.255' => true,
					'10.128.0.0' => true,
					'10.64.17.55' => true,
					'10.64.20.0' => false,
					'10.64.27.207' => false,
					'10.64.31.255' => false,
					'0::0' => false,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' => false,
					'2001:DB8::1' => false,
					'2620:0:861:106::45' => false,
					'2620:0:862:103::' => false,
					'2620:0:862:102:10:20:0:113' => true,
				),
			),
			array(
				'empty_set',
				array(),
				array(
					'0.0.0.0' => false,
					'255.255.255.255' => false,
					'10.2.4.25' => false,
					'10.2.4.26' => false,
					'10.2.4.27' => false,
					'10.20.0.255' => false,
					'10.128.0.0' => false,
					'10.64.17.55' => false,
					'10.64.20.0' => false,
					'10.64.27.207' => false,
					'10.64.31.255' => false,
					'0::0' => false,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' => false,
					'2001:DB8::1' => false,
					'2620:0:861:106::45' => false,
					'2620:0:862:103::' => false,
					'2620:0:862:102:10:20:0:113' => false,
				),
			),
			array(
				'edge_cases',
				array(
					'0.0.0.0',
					'255.255.255.255',
					'::',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff',
					'10.10.10.10/25', // host bits intentional
				),
				array(
					'0.0.0.0' => true,
					'255.255.255.255' => true,
					'10.2.4.25' => false,
					'10.2.4.26' => false,
					'10.2.4.27' => false,
					'10.20.0.255' => false,
					'10.128.0.0' => false,
					'10.64.17.55' => false,
					'10.64.20.0' => false,
					'10.64.27.207' => false,
					'10.64.31.255' => false,
					'0::0' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' => true,
					'2001:DB8::1' => false,
					'2620:0:861:106::45' => false,
					'2620:0:862:103::' => false,
					'2620:0:862:102:10:20:0:113' => false,
					'10.10.9.255' => false,
					'10.10.10.0' => true,
					'10.10.10.1' => true,
					'10.10.10.10' => true,
					'10.10.10.126' => true,
					'10.10.10.127' => true,
					'10.10.10.128' => false,
					'10.10.10.177' => false,
					'10.10.10.255' => false,
					'10.10.11.0' => false,
				),
			),
			array(
				'exercise_optimizer',
				array(
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fffe:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fffd:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fffc:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fffb:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fffa:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff9:8000/113',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff9:0/113',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff8:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff7:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff6:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff5:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff4:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff3:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff2:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff1:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:fff0:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffef:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffee:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffec:0/111',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffeb:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffea:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe9:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe8:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe7:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe6:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe5:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe4:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe3:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe2:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe1:0/112',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffe0:0/110',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffc0:0/107',
					'ffff:ffff:ffff:ffff:ffff:ffff:ffa0:0/107',
				),
				array(
					'0.0.0.0' => false,
					'255.255.255.255' => false,
					'::' => false,
					'ffff:ffff:ffff:ffff:ffff:ffff:ff9f:ffff' => false,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffa0:0' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffc0:1234' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffed:ffff' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:fff4:4444' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:fff9:8080' => true,
					'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' => true,
				),
			),
		);
	}

	/**
	 * Validates IPSet loading and matching code
	 *
	 * @dataProvider provideIPSets
	 */
	public function testIPSet( $desc, array $cfg, array $tests ) {
		$ipset = new IPSet( $cfg );
		foreach ( $tests as $ip => $expected ) {
			$result = $ipset->match( $ip );
			$this->assertEquals( $expected, $result, "Incorrect match() result for $ip in dataset $desc" );
		}
	}
}
