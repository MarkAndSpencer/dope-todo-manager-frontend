<?php

use PHPUnit\Framework\TestCase;

class TaskListTest extends TestCase
{

    private $CI;

    public function setUp()
    {
        $this->CI = &get_instance();
    }
    public function testTaskList()
    {
        $completedCount = 0;
        $this->CI->load->model('tasks');
        $taskList = $this->CI->tasks->getAllTasks();
        foreach($taskList as $task) {
            if ($task->status == 2) {
                $completedCount += 1;
            } else {
                $completedCount -= 1;
            }
        }
        $this->assertTrue($completedCount < 0);
    }
}
