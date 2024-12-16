<?php
namespace Iplogic\Beru\Tests;

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
class RQTest extends BitrixTestCase
{

	/**
	 * @var Mockery\MockInterface
	 */
	protected $apiLogTableMock;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $RQMock;

	/**
	 * @var Mockery\SpyInterface
	 */
	protected $taskTableSpy;


	public function setUp(): void
	{
		parent::setUp();

		$this->apiLogTableMock = \Mockery::mock('alias:' . \Iplogic\Beru\V2\ORM\ApiLogTable::class);
		$this->RQMock = \Mockery::mock(\Iplogic\Beru\V2\Task\RQ::class)
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$this->taskTableSpy = \Mockery::spy('alias:' . \Iplogic\Beru\V2\ORM\TaskTable::class);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecute(): void
	{
		$this->apiLogTableMock
			->shouldReceive('getRowById')
			->once()
			->andReturn(["REQUEST_TYPE" => "", "URL" => "", "REQUEST" => ""]);
		$this->apiLogTableMock->shouldReceive('update')->never();
		$this->RQMock->shouldReceive('query')->once()->andReturn(["status" => 200]);

		$this->RQMock->execute(["ID" => 0]);

		$this->taskTableSpy->shouldHaveReceived('delete')->once();
		$this->taskTableSpy->shouldNotHaveReceived('update');
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteNoTask(): void
	{
		$this->apiLogTableMock->shouldReceive('getRowById')->never();
		$this->apiLogTableMock->shouldReceive('update')->never();
		$this->RQMock->shouldReceive('query')->never();

		$this->RQMock->execute(false);

		$this->taskTableSpy->shouldNotHaveReceived('delete');
		$this->taskTableSpy->shouldNotHaveReceived('update');
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteNoLogRecord(): void
	{
		$this->apiLogTableMock
			->shouldReceive('getRowById')
			->once()
			->andReturn(false);
		$this->apiLogTableMock->shouldReceive('update')->never();
		$this->RQMock->shouldReceive('query')->never();

		$this->RQMock->execute(["ID" => 0]);

		$this->taskTableSpy->shouldHaveReceived('delete')->once();
		$this->taskTableSpy->shouldNotHaveReceived('update');
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteStopRepeating(): void
	{
		$this->apiLogTableMock
			->shouldReceive('getRowById')
			->once()
			->andReturn(["REQUEST_TYPE" => "", "URL" => "", "REQUEST" => ""]);
		$this->apiLogTableMock->shouldReceive('update')->once();
		$this->RQMock->shouldReceive('query')->once()->andReturn(["status" => 400, "stop_repeating" => true]);

		$this->RQMock->execute(["ID" => 0]);

		$this->taskTableSpy->shouldHaveReceived('delete')->once();
		$this->taskTableSpy->shouldNotHaveReceived('update');
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteRejected(): void
	{
		$this->apiLogTableMock
			->shouldReceive('getRowById')
			->once()
			->andReturn(["REQUEST_TYPE" => "", "URL" => "", "REQUEST" => ""]);
		$this->apiLogTableMock->shouldReceive('update')->never();
		$this->RQMock->shouldReceive('query')->once()->andReturn(["status" => 500]);

		$this->RQMock->execute(["ID" => 0]);

		$this->taskTableSpy->shouldNotHaveReceived('delete');
		$this->taskTableSpy->shouldHaveReceived('update')->once();
	}

}