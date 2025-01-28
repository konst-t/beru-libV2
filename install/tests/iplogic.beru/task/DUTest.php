<?php

namespace Iplogic\Beru\Tests;

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
class DUTest extends BitrixTestCase
{
	/**
	 * @var Mockery\MockInterface
	 */
	protected $taskTableMock;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $executorMock;


	public function setUp(): void
	{
		parent::setUp();

		$this->executorMock = \Mockery::mock('alias:' . \Iplogic\Beru\V2\Executor::class);
		$this->taskTableMock = \Mockery::mock('alias:' . \Iplogic\Beru\V2\ORM\TaskTable::class);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecute(): void
	{
		$this->executorMock->shouldReceive('go')->once();
		$this->taskTableMock->shouldReceive('delete')->once();

		$obj = new \Iplogic\Beru\V2\Task\DU();
		$obj->execute(["ID" => $this->faker->randomNumber]);
	}


}