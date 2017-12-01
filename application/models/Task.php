<?php

/**
* Model for Tasks on the todo list
*/
class Task extends CI_Model
{
    /**
    * ctor
    */
    public function __construct()
    {
    }

    //provide this for now
    //creates a new task
    public function create($taskObj = null)
    {
        if ($taskObj === null)
            return new Task;

        $task = new Task;

        foreach($this->schema() as $field) {
            if (property_exists($taskObj, $field)) {
                $val = $taskObj->$field;

                if (is_numeric($val))
                    $val = intval($val);

                $task->$field = $val;
            }
        }
        return $task;
    }

    public function schema()
    {
        return array('id','task','priority','size','group','deadline','status','flag');
    }

    // If this class has a setProp method, use it, else modify the property directly
    public function __set($key, $value) {
        // if a set* method exists for this key,
        // use that method to insert this value.
        // For instance, setName(...) will be invoked by $object->name = ...
        // and setLastName(...) for $object->last_name =
        $method = 'set' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));
        if (method_exists($this, $method))
        {
                $this->$method($value);
                return $this;
        }

        //otherwise validate the property
        if (!$this->validate($key, $value)) {
            $isstr = is_string($value);
            throw new Exception('Property does not validate: ' . $key . ' => ' . $value
                . 'test: ' . intval('COMPSTUMP'));
        }
        $this->$key = $value;
        return $this;
    }

    private function validate($key, $value)
    {
        $rules = $this->rules();
        if (array_key_exists($key, $rules))
            return call_user_func($rules[$key], $value);

        return true;
    }

    // provide entity validation rules
    public function rules()
    {
        $config = array(
            'task' => function($value) { return is_string($value) && strlen($value) < 64; },
            'priority' => function($value) { return intval($value) != 0 && intval($value) < 4; },
            'size' => function($value) { return intval($value) != 0 && intval($value) < 4; },
            'group' => function($value) { return intval($value) != 0 && intval($value) < 5; },
        );
        return $config;
    }
}
