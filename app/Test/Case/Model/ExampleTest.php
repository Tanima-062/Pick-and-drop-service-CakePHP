<?php

App::uses('Station', 'Model');

class ExampleTest extends CakeTestCase {
	public function setUp() {
		parent::setUp();
		$this->Station = ClassRegistry::init('Station');
	}

	public function testGetStationById() {
		$actual = $this->Station->getStationById(1);
		$this->assertSame('函館', $actual['Station']['name']);
		$this->assertEquals(1, $actual['Station']['prefecture_id']);
		$this->assertSame('hakodate_station', $actual['Station']['url']);
		$this->assertEquals(41.773709, $actual['Station']['latitude']);
		$this->assertEquals(140.726413, $actual['Station']['longitude']);
		$this->assertEquals(0, $actual['Station']['type']);
	}

	// TODO: add more tests
}
