<?php

/**
* Model for Tasks on the todo list
*/
class Tasks extends XML_Model
{
    /**
    * ctor
    */
    public function __construct()
    {
        parent::__construct(APPPATH . '../data/tasks.xml', 'id', 'Task');
    }

    function getCategorizedTasks()
    {
        // extract the undone tasks
        foreach ($this->all() as $task)
        {
            if ($task->status != 2) {
                $task->group = $this->app->group($task->group);
                $undone[] = $task;
            }
        }

        // order them by category
        usort($undone, "orderByCategory");

        return $undone;
    }

    // provide form validation rules
    public function rules()
    {
        $config = array(
            ['field' => 'task', 'label' => 'TODO task', 'rules' => 'alpha_numeric_spaces|max_length[64]'],
            ['field' => 'priority', 'label' => 'Priority', 'rules' => 'integer|less_than[4]'],
            ['field' => 'size', 'label' => 'Task size', 'rules' => 'integer|less_than[4]'],
            ['field' => 'group', 'label' => 'Task group', 'rules' => 'integer|less_than[5]'],
        );
        return $config;
    }

    public function getAllTasks()
    {
        return $this->all();
    }

    public function all()
    {
        $ret = array();
        foreach (parent::all() as $t) {
            $ret[$t->id] = $this->task->create($t);
        }
        return $ret;
    }

    public function get($id, $key2 = null)
    {
        return $this->task->create(parent::get($id, $key2));
    }

}

function orderByCategory($a, $b)
{
    if ($a->group < $b->group)
        return -1;
    elseif ($a->group > $b->group)
        return 1;
    else
        return 0;
}
