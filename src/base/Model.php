<?php


namespace App\base;


use App\models\TaskModel;

class Model
{
    private $db;
    private $query = '';
    private $isNewRecord = false;
    private $table = 'task';

    const FIND_ONE = 'one';
    const FIND_ALL = 'all';

    public $data = [];

    public function __construct()
    {
        $this->db = new \mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
        $this->isNewRecord = true;
    }

    public function getId()
    {
        return $this->data && isset($this->data['id'])
            ? $this->data['id']
            : null;
    }

    /**
     * @return $this
     */
    public function find()
    {
        $this->query = "select * from {$this->table}";
        return $this;
    }

    /**
     * @param string $where
     * @return TaskModel|array|null
     */
    public function findOne(string $where)
    {
        $this->query = "select * from {$this->table} where $where";
        return $this->execute(self::FIND_ONE);
    }

    /**
     * @param $body
     * @return $this
     */
    public function where($body)
    {
        $this->query .= " where $body";
        return $this;
    }

    /**
     * @param $body
     * @return $this
     */
    public function andWhere($body)
    {
        $this->query .= " and $body";
        return $this;
    }

    /**
     * @param $body
     * @return $this
     */
    public function limit($body)
    {
        $this->query .= " limit $body";
        return $this;
    }

    /**
     * @param $column
     * @param string $sort
     * @return $this
     */
    public function orderBy($column, $sort = 'ASC')
    {
        $this->query .= " order by $column $sort";
        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return TaskModel|array
     */
    public function execute($type = self::FIND_ALL)
    {
        $rows = [];
        $result = $this->db->query($this->query);
        if (!$result) {
            return null;
        }

        if ($type === self::FIND_ONE) {
            $row = $result->fetch_assoc();
            if (!$row) {
                return null;
            }

            $class = new TaskModel();
            $class->isNewRecord = false;
            if ($class->load($row)) {
                return $class;
            }
        }

        while($row = $result->fetch_assoc())
        {
            $class = new TaskModel();
            if ($class->load($row)) {
                $rows[] = $class;
            }
        }
        return $rows;
    }

    /**
     * @return array|int|null
     */
    public function count()
    {
        $result = $this->db->query($this->query);
        if (!$result) {
            return null;
        }

        return $result->num_rows;
    }

    public function load($data)
    {
        if (!empty($data)) {
            $this->data = $data;
            return true;
        }

        return false;
    }

    /**
     * @return bool|\mysqli_result
     */
    public function save()
    {
        if ($this->data) {
            $keys = join(', ', array_keys($this->data));
            $values = "\"" . join('", "', array_values($this->data)) . "\"";
            if (!$this->isNewRecord && $this->getId()) {
                $params = $this->getParamsForUpdate($this->data);
                var_dump("update {$this->table} set $params where {$this->table}.id = {$this->getId()}");
                return $this->db->query("update {$this->table} set $params where {$this->table}.id = {$this->getId()}");
            }
            var_dump("insert into {$this->table} ($keys) values ($values)");
            return $this->db->query("insert into {$this->table} ($keys) values ($values)");
        }
        return false;
    }

    public function delete()
    {
        if ($this->getId()) {
            return $this->db->query("delete from {$this->table} where id = {$this->getId()}");
        }
        return false;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getParamsForUpdate(array $data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = "$key=\"$value\"";
        }
        return join(', ', $result);
    }
}