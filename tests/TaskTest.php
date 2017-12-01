<?php

use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private $CI;

    public function setUp()
    {
        $this->CI = &get_instance();
    }

    // test for valid propterty assignments
    public function testValidPropertyAssignments()
    {
        $task = $this->CI->task->create();
        $task->task = 'write unit tests';
        $task->priority = 1;
        $task->size = 2;
        $task->group = 3;

        $this->assertEquals($task->task, 'write unit tests');
        $this->assertEquals($task->priority, 1);
        $this->assertEquals($task->size, 2);
        $this->assertEquals($task->group, 3);
    }

    // assigning a non-string to $task->task should throw exception
    public function testTaskInvalidType()
    {
        $task = $this->CI->task->create();

        $this->expectException(Exception::class);
        $task->priority = 6969;
    }

    // assigning a string longer than 64 characters to $task->task should throw exception
    public function testTaskInvalidValue()
    {
        $task = $this->CI->task->create();

        $this->expectException(Exception::class);
        $task->priority = 'abc 123 this string is way too long to be a valid task name';
    }

    // assigning anything other than int to $task->priority should throw
    public function testPriorityInvalidType()
    {
        $task = $this->CI->task->create();

        $this->expectException(Exception::class);
        $task->priority = 'invalid';
    }

    // assigning a number > 4 to $task->priority should throw
    public function testPriorityInvalidValue()
    {
        $task = $this->CI->task->create();

        $this->expectException(Exception::class);
        $task->priority = 42;
    }

    // assigning anything other than int to $task->size should throw
    public function testSizeInvalidType()
    {
        $task = $this->CI->task->create();

        $this->expectException(Exception::class);
        $task->size = 'wrong';
    }
    
    // assigning a number >= 4 to $task->size should throw
    public function testSizeInvalidValue()
    {
        $task = $this->CI->task->create();

        $this->expectException(Exception::class);
        $task->size = 5;
    }

    // assigning anything but a number to to $task->group should throw
    public function testGroupInvalidType()
    {
        $task = $this->CI->task->create();

        $this->expectException(Exception::class);
        $task->group = 'nope';
    }

    // assigning a number > 4 to $task->group should throw
    public function testGroupInvalidValue()
    {
        $task = $this->CI->task->create();

        $this->expectException(Exception::class);
        $task->group = 5;
    }
}
