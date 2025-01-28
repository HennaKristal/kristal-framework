<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

use Backend\Core\Database;


class Entity extends Database
{
    protected $identifier_key;
    protected $identifier_value;

    public function __construct(array $entity_information)
    {
        $this->identifier_key = $entity_information["identifier_key"];
        $this->identifier_value = $entity_information["identifier_value"];
        parent::__construct(["database" => $entity_information["database"]]);
        $this->confirmTable();

        // Get values from database
        if (isset($this->identifier_key) && isset($this->identifier_value))
        {
            $entity = $this->table()->where($this->identifier_key, $this->identifier_value)->get("array");

            if ($entity)
            {
                foreach ($this->columns as $key => $value)
                {
                    $this->{$key} = $entity[$key];
                }
            }
            else
            {
                $this->identifier_key = null;
                $this->identifier_value = null;
            }
        }
    }


    // Save data to database
    public function save()
    {
        if (isset($this->identifier_key) && isset($this->identifier_value))
        {
            // Create update data
            foreach ($this->columns as $key => $value)
            {
                $update[$key] = $this->{$key};
            }

            $this->table()->where($this->identifier_key, $this->identifier_value)->update($update);
        }
        else
        {
            // Create insert data
            foreach ($this->columns as $key => $value)
            {
                $insert[$key] = $this->{$key};
            }

            $this->table()->insert($insert);
        }
    }


    // Delete data from database
    public function delete()
    {
        if (isset($this->identifier_key) && isset($this->identifier_value))
        {
            $this->table()->where($this->identifier_key, $this->identifier_value)->delete();
        }
    }
}
