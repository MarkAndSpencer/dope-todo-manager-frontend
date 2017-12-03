<?php

/**
 * Modified to use REST client to get port data from our server.
 */
define('REST_SERVER', 'http://backend.local');  // the REST server host
define('REST_PORT', $_SERVER['SERVER_PORT']);   // the port you are running the server

/**
* Model for Tasks on the todo list
*/
class Tasks extends Memory_Model
{
    /**
    * ctor
    */
    public function __construct()
    {
        parent::__construct();
        $this->_fields = array('id','task','priority','size','group','deadline','status','flag');
    }

    public function load() {
        // load our data from the REST backend
        $this->rest->initialize(array('server' => REST_SERVER));
        $this->rest->option(CURLOPT_PORT, REST_PORT);
        $this->_data =  $this->rest->get('job');

        $one = array_values((array) $this->_data);
        $this->_fields = array_keys((array)$one[0]);
        $this->reindex();
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
        $this->load();
        $ret = array();
        foreach (parent::all() as $t) {
            $ret[$t->id] = $this->task->create($t);
        }
        return $ret;
    }

    public function get($key, $key2 = null)
    {
        $this->rest->initialize(array('server' => REST_SERVER));
        $this->rest->option(CURLOPT_PORT, REST_PORT);
        return $this->rest->get('job/' . $key);
    }

    function delete($key, $key2 = null)
    {
        $this->rest->initialize(array('server' => REST_SERVER));
        $this->rest->option(CURLOPT_PORT, REST_PORT);
        $this->rest->delete('job/' . $key);
        $this->load();
    }

    function update($record)
    {
        $this->rest->initialize(array('server' => REST_SERVER));
        $this->rest->option(CURLOPT_PORT, REST_PORT);
        $key = $record->{$this->_keyfield};
        $retrieved = $this->rest->put('job/' . $key, $record);
        $this->load();
    }

    function add($record)
    {
        $this->rest->initialize(array('server' => REST_SERVER));
        $this->rest->option(CURLOPT_PORT, REST_PORT);
        $key = $record->{$this->_keyfield};
        $retrieved = $this->rest->post('job/' . $key, $record);
        $this->load();
    }

    protected function store()
    {
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
