<?php

namespace TCG\Voyager\Tests\Unit\Actions;

use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\User;
use TCG\Voyager\Tests\TestCase;

class AbstractActionTest extends TestCase
{
    /**
     * The users DataType instance.
     *
     * @var \TCG\Voyager\Models\DataType
     */
    protected $userDataType;

    /**
     * A dummy user instance.
     *
     * @var \TCG\Voyager\Models\User
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/../../database/factories');

        $this->userDataType = Voyager::model('DataType')->where('name', 'users')->first();
        $this->user = factory(User::class)->create();
    }

    /**
     * This test checks that `getRoute` method calls the `getDefaultRoute`
     * method if the given key is empty.
     */
    public function testGetRouteWithEmptyKey()
    {
        $stub = $this->getMockBuilder(AbstractAction::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultRoute'])
            ->getMockForAbstractClass();

        // The `getDefaultRoute` method is called as default inside the
        // `getRoute` method to retrieve the route.
        $stub->expects($this->any())
             ->method('getDefaultRoute')
             ->will($this->returnValue(true));

        $this->assertTrue($stub->getRoute($this->userDataType->name));
    }

    /**
     * This test checks that `getRoute` method calls the expected method when a
     * key is given.
     */
    public function testGetRouteWithCustomKey()
    {
        $stub = $this->getMockBuilder(AbstractAction::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomRoute'])
            ->getMockForAbstractClass();

        // The key that's passed to the `getRoute` method will be capitalized
        // and putted between 'get' and 'Route'. Calling `getRoute('custom')`
        // will call the `getCustomRoute` method if it's defined.
        $stub->expects($this->any())
             ->method('getCustomRoute')
             ->will($this->returnValue(true));

        $this->assertTrue($stub->getRoute('custom'));
    }

    /**
     * This test checks that `getAttributes` method will give us the expected
     * output.
     */
    public function testConvertAttributesToHtml()
    {
        $stub = $this->getMockBuilder(AbstractAction::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttributes'])
            ->getMockForAbstractClass();

        $stub->expects($this->any())
             ->method('getAttributes')
             ->will($this->returnValue([
                 'class'   => 'class1 class2',
                 'data-id' => 5,
                 'id'      => 'delete-5',
             ]));

        $this->assertEquals('class="class1 class2"data-id="5"id="delete-5"', $stub->convertAttributesToHtml());
    }

    /**
     * This test checks that `shouldActionDisplayOnDataType` method returns true
     * if the action should be displayed for every data type.
     */
    public function testShouldActionDisplayOnDataTypeWithDefaultDataType()
    {
        $stub = $this->getMockBuilder(AbstractAction::class)
            ->setConstructorArgs([$this->userDataType, $this->user])
            ->getMockForAbstractClass();

        $this->assertTrue($stub->shouldActionDisplayOnDataType());
    }

    /**
     * This test checks that `shouldActionDisplayOnDataType` method returns true
     * if the action should only be displayed for a specific data type.
     */
    public function testTrueIsReturnedIfDataTypeMatchesTheOneWhereTheActionWasCreatedFor()
    {
        $stub = $this->getMockBuilder(AbstractAction::class)
            ->setConstructorArgs([$this->userDataType, $this->user])
            ->setMethods(['getDataType'])
            ->getMockForAbstractClass();

        $stub->expects($this->any())
             ->method('getDataType')
             ->will($this->returnValue($this->userDataType->name));

        $this->assertTrue($stub->shouldActionDisplayOnDataType());
    }

    /**
     * This test checks that `shouldActionDisplayOnDataType` method returns false
     * if the action should only be displayed for a specific data type.
     */
    public function testFalseIsReturnedIfDataTypeDoesNotMatchesTheOneWhereTheActionWasCreatedFor()
    {
        $stub = $this->getMockBuilder(AbstractAction::class)
            ->setConstructorArgs([$this->userDataType, $this->user])
            ->setMethods(['getDataType'])
            ->getMockForAbstractClass();

        $stub->expects($this->any())
             ->method('getDataType')
             ->will($this->returnValue('not users')); // different data type

        $this->assertFalse($stub->shouldActionDisplayOnDataType());
    }
}
