<?php

namespace Iplogic\Beru\Tests;

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
class USTest extends BitrixTestCase
{
	/**
	 * @var Mockery\MockInterface
	 */
	protected $taskTableMock;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $taskMock;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $productTableMock;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $apiRequestMock;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $taskResultMock;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $prodResultMock;


	public function setUp(): void
	{
		parent::setUp();

		$this->taskMock = \Mockery::mock('alias:' . \Iplogic\Beru\V2\Task::class);
		$this->apiRequestMock = \Mockery::mock("overload:" . \Iplogic\Beru\V2\ApiRequest\setShown::class);
		$this->taskTableMock = \Mockery::mock('alias:' . \Iplogic\Beru\V2\ORM\TaskTable::class);
		$this->productTableMock = \Mockery::mock('alias:' . \Iplogic\Beru\V2\ORM\ProductTable::class);

		$this->prodResultMock = \Mockery::mock(\Bitrix\Main\ORM\Query\Result::class);
		$this->taskResultMock = \Mockery::mock(\Bitrix\Main\ORM\Query\Result::class);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecute(): void
	{
		$this->taskTableMock->shouldReceive('getList')->once()->andReturn($this->taskResultMock);
		$this->taskResultMock->shouldReceive('fetch')->andReturn(
			["ID" => 1, "ENTITY_ID" => 3],
			["ID" => 2, "ENTITY_ID" => 4],
			false
		);
		$this->productTableMock->shouldReceive('getList')->once()->andReturn($this->prodResultMock);
		$this->prodResultMock->shouldReceive('fetch')->andReturn(
			["ID" => 3, "MARKET_SKU" => 777],
			["ID" => 4, "MARKET_SKU" => 888],
			false
		);
		$this->taskTableMock->shouldReceive('delete')->with(1)->once();
		$this->taskTableMock->shouldReceive('delete')->with(2)->once();
		$this->taskTableMock->shouldReceive('delete')->with(111)->once();
		$this->productTableMock->shouldReceive('update')->times(2);
		$this->apiRequestMock->shouldReceive('send')->with(
			[
				"hiddenOffers" => [
					[
						"marketSku"  => 777,
					],
					[
						"marketSku"  => 888,
					],
				],
			]
		)->once()->andReturn(["status" => 200]);
		$this->taskMock->shouldReceive('scheduleTask')->once();

		$obj = new \Iplogic\Beru\V2\Task\US();
		$obj->execute(["ID" => 111, "PROFILE_ID" => 5555555]);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteBadTask(): void
	{
		$this->taskTableMock->shouldReceive('getList')->never();
		$this->taskResultMock->shouldReceive('fetch')->never();
		$this->productTableMock->shouldReceive('getList')->never();
		$this->prodResultMock->shouldReceive('fetch')->never();
		$this->taskTableMock->shouldReceive('delete')->never();
		$this->productTableMock->shouldReceive('update')->never();
		$this->apiRequestMock->shouldReceive('send')->never();
		$this->taskMock->shouldReceive('scheduleTask')->times(2);

		$obj = new \Iplogic\Beru\V2\Task\US();
		$obj->execute(null);
		$obj->execute([]);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteNoTasks(): void
	{
		$this->taskTableMock->shouldReceive('getList')->once()->andReturn($this->taskResultMock);
		$this->taskResultMock->shouldReceive('fetch')->andReturn(false);
		$this->productTableMock->shouldReceive('getList')->never();
		$this->taskTableMock->shouldReceive('delete')->with(1)->never();
		$this->taskTableMock->shouldReceive('delete')->with(2)->never();
		$this->taskTableMock->shouldReceive('delete')->with(111)->once();
		$this->productTableMock->shouldReceive('update')->never();
		$this->apiRequestMock->shouldReceive('send')->never();
		$this->taskMock->shouldReceive('scheduleTask')->once();

		$obj = new \Iplogic\Beru\V2\Task\US();
		$obj->execute(["ID" => 111, "PROFILE_ID" => 5555555]);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteNotAllProducts(): void
	{
		$this->taskTableMock->shouldReceive('getList')->once()->andReturn($this->taskResultMock);
		$this->taskResultMock->shouldReceive('fetch')->andReturn(
			["ID" => 1, "ENTITY_ID" => 3],
			["ID" => 2, "ENTITY_ID" => 4],
			false
		);
		$this->productTableMock->shouldReceive('getList')->once()->andReturn($this->prodResultMock);
		$this->prodResultMock->shouldReceive('fetch')->andReturn(
			["ID" => 3, "MARKET_SKU" => 777],
			false
		);
		$this->taskTableMock->shouldReceive('delete')->with(1)->once();
		$this->taskTableMock->shouldReceive('delete')->with(2)->once();
		$this->taskTableMock->shouldReceive('delete')->with(111)->once();
		$this->productTableMock->shouldReceive('update')->once();
		$this->apiRequestMock->shouldReceive('send')->with(
			[
				"hiddenOffers" => [
					[
						"marketSku"  => 777,
					],
				],
			]
		)->once()->andReturn(["status" => 200]);
		$this->taskMock->shouldReceive('scheduleTask')->once();

		$obj = new \Iplogic\Beru\V2\Task\US();
		$obj->execute(["ID" => 111, "PROFILE_ID" => 5555555]);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testExecuteNoProducts(): void
	{
		$this->taskTableMock->shouldReceive('getList')->once()->andReturn($this->taskResultMock);
		$this->taskResultMock->shouldReceive('fetch')->andReturn(
			["ID" => 1, "ENTITY_ID" => 3],
			["ID" => 2, "ENTITY_ID" => 4],
			false
		);
		$this->productTableMock->shouldReceive('getList')->once()->andReturn($this->prodResultMock);
		$this->prodResultMock->shouldReceive('fetch')->andReturn(false);
		$this->taskTableMock->shouldReceive('delete')->with(1)->once();
		$this->taskTableMock->shouldReceive('delete')->with(2)->once();
		$this->taskTableMock->shouldReceive('delete')->with(111)->once();
		$this->productTableMock->shouldReceive('update')->never();
		$this->apiRequestMock->shouldReceive('send')->never();
		$this->taskMock->shouldReceive('scheduleTask')->once();

		$obj = new \Iplogic\Beru\V2\Task\US();
		$obj->execute(["ID" => 111, "PROFILE_ID" => 5555555]);
	}


}
