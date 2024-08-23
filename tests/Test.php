<?php
namespace TJM\Project\Tests;
use TJM\Component\BufferManager\BufferManager;
use PHPUnit\Framework\TestCase;

class Test extends TestCase{
	public function test(){
		$bufferManager = new BufferManager();
		$this->assertFalse($bufferManager->has('a'));
		$bufferManager->start('a');
		echo 'Apple';
		$bufferManager->end();
		$bufferManager->start('b');
		echo 'Banana';
		$bufferManager->end();
		$this->assertTrue($bufferManager->has('a'));
		$this->assertEquals('Apple',  $bufferManager->get('a'));
		$this->assertEquals('Banana',  $bufferManager->get('b'));
	}
}