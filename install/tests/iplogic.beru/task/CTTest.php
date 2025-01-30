<?php

namespace Iplogic\Beru\Tests;

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
class CTTest extends BitrixTestCase
{

	/**
	 * @var Mockery\MockInterface
	 */
	protected $taskTableMock;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $resultMock;

	/**
	 * @var Mockery\SpyInterface
	 */
	protected $taskMock;

	/**
	 * @var int
	 */
	protected $taskID;


	public function setUp(): void
	{
		parent::setUp();

		$this->taskTableMock = \Mockery::mock('alias:' . \Iplogic\Beru\V2\ORM\TaskTable::class);
		$this->resultMock = \Mockery::mock(\Bitrix\Main\ORM\Query\Result::class);
		$this->taskMock = \Mockery::mock('alias:' . \Iplogic\Beru\V2\Task::class);

		$this->taskID = $this->faker->randomNumber;
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecute(): void
	{
		$this->taskTableMock->shouldReceive('getList')->times(2)->andReturn($this->resultMock);
		$this->resultMock->shouldReceive('fetch')->andReturn(
			["ID" => 1],
			["ID" => 2],
			false,
			["ID" => 3],
			["ID" => 4],
			false
		);
		$this->taskTableMock->shouldReceive('delete')->with(1)->once();
		$this->taskTableMock->shouldReceive('delete')->with(2)->once();
		$this->taskTableMock->shouldReceive('delete')->with(3)->once();
		$this->taskTableMock->shouldReceive('delete')->with(4)->once();
		$this->taskTableMock->shouldReceive('delete')->with($this->taskID)->once();
		$this->taskMock->shouldReceive('scheduleTask')->once();

		$obj = new \Iplogic\Beru\V2\Task\CT();
		$obj->execute(["ID" => $this->taskID]);
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteBadTask(): void
	{
		$this->taskTableMock->shouldReceive('getList')->times(4)->andReturn($this->resultMock);
		$this->resultMock->shouldReceive('fetch')->andReturn(
			["ID" => 1],
			["ID" => 2],
			false,
			["ID" => 3],
			["ID" => 4],
			false,
			["ID" => 1],
			["ID" => 2],
			false,
			["ID" => 3],
			["ID" => 4],
			false
		);
		$this->taskTableMock->shouldReceive('delete')->with(1)->times(2);
		$this->taskTableMock->shouldReceive('delete')->with(2)->times(2);
		$this->taskTableMock->shouldReceive('delete')->with(3)->times(2);
		$this->taskTableMock->shouldReceive('delete')->with(4)->times(2);
		$this->taskTableMock->shouldReceive('delete')->with($this->taskID)->never();
		$this->taskMock->shouldReceive('scheduleTask')->times(2);

		$obj = new \Iplogic\Beru\V2\Task\CT();
		$obj->execute(null);
		$obj->execute([]);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteNoOld(): void
	{
		$this->taskTableMock->shouldReceive('getList')->times(2)->andReturn($this->resultMock);
		$this->resultMock->shouldReceive('fetch')->andReturn(
			["ID" => 1],
			["ID" => 2],
			false,
			false
		);
		$this->taskTableMock->shouldReceive('delete')->with(1)->once();
		$this->taskTableMock->shouldReceive('delete')->with(2)->once();
		$this->taskTableMock->shouldReceive('delete')->with(3)->never();
		$this->taskTableMock->shouldReceive('delete')->with(4)->never();
		$this->taskTableMock->shouldReceive('delete')->with($this->taskID)->once();
		$this->taskMock->shouldReceive('scheduleTask')->once();

		$obj = new \Iplogic\Beru\V2\Task\CT();
		$obj->execute(["ID" => $this->taskID]);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteNoUnfinished(): void
	{
		$this->taskTableMock->shouldReceive('getList')->times(2)->andReturn($this->resultMock);
		$this->resultMock->shouldReceive('fetch')->andReturn(
			false,
			["ID" => 3],
			["ID" => 4],
			false
		);
		$this->taskTableMock->shouldReceive('delete')->with(1)->never();
		$this->taskTableMock->shouldReceive('delete')->with(2)->never();
		$this->taskTableMock->shouldReceive('delete')->with(3)->once();
		$this->taskTableMock->shouldReceive('delete')->with(4)->once();
		$this->taskTableMock->shouldReceive('delete')->with($this->taskID)->once();
		$this->taskMock->shouldReceive('scheduleTask')->once();

		$obj = new \Iplogic\Beru\V2\Task\CT();
		$obj->execute(["ID" => $this->taskID]);
	}


}