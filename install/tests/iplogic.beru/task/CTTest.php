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
	protected $taskSpy;


	public function setUp(): void
	{
		parent::setUp();

		$this->taskTableMock = \Mockery::mock('alias:' . \Iplogic\Beru\V2\ORM\TaskTable::class);
		$this->resultMock = \Mockery::mock(\Bitrix\Main\ORM\Query\Result::class);
		$this->taskSpy = \Mockery::spy('alias:' . \Iplogic\Beru\V2\Task::class);

		$this->resultMock->shouldReceive( 'fetch' )->andReturn(["ID" => 0], ["ID" => 0], false);
		$this->taskTableMock->shouldReceive('getList')->once()->andReturn($this->resultMock);
		$this->taskTableMock->shouldReceive('delete')->atLeast()->times(2)->andReturn(null);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecute(): void
	{
		$obj = new \Iplogic\Beru\V2\Task\CT();
		$obj->execute(["ID" => $this->faker->randomNumber]);

		$this->taskSpy->shouldHaveReceived('scheduleTask')->once();
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteNull(): void
	{
		$obj = new \Iplogic\Beru\V2\Task\CT();
		$obj->execute(null);

		$this->taskSpy->shouldHaveReceived('scheduleTask')->once();
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteEmpty(): void
	{
		$obj = new \Iplogic\Beru\V2\Task\CT();
		$obj->execute([]);

		$this->taskSpy->shouldHaveReceived('scheduleTask')->once();
	}
}