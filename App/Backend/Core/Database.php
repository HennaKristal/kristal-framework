<?php namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

use \PDO;
use \PDOException;


class Database
{
    private $connection;
    private $table_name;
    private $arguments = array();
    private $secured_inputs = array();


    // Create connection to the database
    public function __construct($params = array("database" => "primary"))
    {
        $databases = unserialize(DATABASES);

        // Make sure database information is set
        if (empty($databases[$params["database"]]))
        {
            throw new \Exception("Database configuration error! Database (" . $params["database"] . ") was empty, please double check your config file!");
        }

        try
        {
            // Create connection
            $this->connection = new PDO(
                "mysql:host=" . $databases[$params["database"]]->host . ";dbname=" . $databases[$params["database"]]->database_name . ";",
                $databases[$params["database"]]->username,
                $databases[$params["database"]]->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
            );
        }
        catch (PDOException $e)
        {
            throw new \Exception("Failed to Connect Database! Please double check your config file!");
        }

        $this->arguments["where"] = "";
        $this->arguments["select"] = "";
        $this->arguments["insert"] = "";
        $this->arguments["update"] = "";
        $this->arguments["orderBy"] = "";
        $this->arguments["limit"] = "";
        $this->arguments["offset"] = "";
    }


    // Check do we need to create a new table
    protected function confirmTable($engine = "InnoDB", $char_set = "utf8")
    {
        if (!$this->table) throw new \Exception("Entity " . get_class($this) . " does not have a table name!");
        if ($this->doesTableExist($this->table) === false)
        {
            $this->createTable($this->table, $this->primary_key, $this->columns, $engine, $char_set);
        }
    }


    // Check does table exist
    public function doesTableExist($table = null)
    {
        // Use entity table if table is not specified
        if ($table === null) { $table = $this->table; }
        if ($table === null) { return false; }

        // Iterate through the tables and check if the specified table exists
        foreach ($this->getTables() as $tableArray)
        {
            if (in_array($table, $tableArray))
            {
                return true;
            }
        }

        return false;
    }


    // Create table
    protected function createTable($table, $primary_key, array $columns, $engine = "InnoDB", $char_set = "utf8")
    {
        $query = "create table if not exists $table (";

        foreach ($columns as $key => $value)
        {
            $query .= "$key $value,";
        }

        $query .= "PRIMARY KEY ($primary_key)) ENGINE=$engine AUTO_INCREMENT=1 DEFAULT CHARSET=$char_set;";

        // Check for errors
        $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
        $statement = $this->connection->prepare($query);

        if (!$statement)
        {
            if (MAINTENANCE_MODE)
            {
                throw new \Exception("Invalid mysql structure at $table entity class!");
            }
            else
            {
                throw new \Exception("Fatal database error while creating entity! Please contact site admin about this error!");
            }
        } 

        $statement->execute();
    }


    public function dropTable($table = null)
    {
        if ($table === null) { $table = $this->table; }
        if ($table === null) { throw new \Exception("Fatal Error: you are trying to delete a table using dropTable() without specifying a table!"); }
        return $this->connection->prepare("drop table $table;")->execute();
    }


    public function dropTableCascade($table = null)
    {
        if ($table === null) { $table = $this->table; }
        if ($table === null) { throw new \Exception("Fatal Error: you are trying to delete a table using dropTable() without specifying a table!"); }
        return $this->connection->prepare("drop table $table CASCADE;")->execute();
    }


    public function getTables()
    {
        return $this->fetchAll("show tables;");
    }


    private function clearQuery()
    {
        $this->table_name = "";
        $this->secured_inputs = array();
        $this->arguments = array();
        $this->arguments["where"] = "";
        $this->arguments["select"] = "";
        $this->arguments["insert"] = "";
        $this->arguments["update"] = "";
        $this->arguments["orderBy"] = "";
        $this->arguments["limit"] = "";
        $this->arguments["offset"] = "";
    }


    protected function databaseBackup()
    {
        $backup = "-- Database Backup Created at " . date(DATE_FORMAT . " " . TIME_FORMAT);
        $tables = $this->getTables();

        foreach ($tables as $table)
        {
            $table_name = reset($table);

            // Delete table
            $backup .= "\n\n\nDROP TABLE " . $table_name . ";\n";

            // Create the table again
            $create = $this->fetch("show create table " . $table_name);
            $backup .= $create["Create Table"] . ";\n";

            // Insert statements
            $content = $this->fetchAll("select * from " . $table_name);

            foreach ($content as $row)
            {
                $fields = "";
                $values = "";

                foreach ($row as $key => $value)
                {
                    $fields .= $key . ", ";
                    $values .= (is_numeric($value)) ? "$value, " : "'$value', ";
                }

                $fields = rtrim($fields, ', ');
                $values = rtrim($values, ', ');

                $backup .= "\ninsert into " . $table_name . "($fields) values ($values);";
            }
        }

        return $backup;
    }


    public function close()
    {
        $this->connection = null;
    }





/*=====================================================================================================================================================================*/





    public function execute($query, array $secured_inputs = array())
    {
        return $this->connection->prepare($query)->execute($secured_inputs);
    }


    public function fetch($query, array $secured_inputs = array(), $type = "array")
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($secured_inputs);
        return ($type !== "array") ? (object) $statement->fetch(\PDO::FETCH_ASSOC) : $statement->fetch(\PDO::FETCH_ASSOC);
    }


    public function fetchAll($query, array $secured_inputs = null, $type = "array")
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($secured_inputs);
        return ($type !== "array") ? (object) $statement->fetchAll(\PDO::FETCH_ASSOC) : $statement->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function table($args = null)
    {
        $this->table_name = ($args !== null) ? $args : $this->table;
        return $this;
    }


    public function select($args)
    {
        if (is_array($args))
        {
            foreach ($args as $field)
            {
                $this->arguments["select"] .= "$field, ";
            }

            // Remove last ', ' from select string
            $this->arguments["select"] = substr($this->arguments["select"], 0, -2);
        }
        else
        {
            $this->arguments["select"] = $args;
        }

        return $this;
    }


    private function baseWhere($connector, $field, $operator, $value = null)
    {
        // Add connector (and | or) if where statement is not empty
        if (!empty($this->arguments["where"]))
        {
            $this->arguments["where"] .= " $connector ";
        }

        if ($value !== null)
        {
            // Add % to the beginning of the comparison value
            if (strtolower($operator) == "like" || strtolower($operator) == "not like" || strtolower($operator) == "starts with" || strtolower($operator) == "starts")
            {
                if (substr($value, 0, 1) !== "%")
                {
                    $value = "%" . $value;
                }
            }

            // Add % to the end of the comparison value
            if (strtolower($operator) == "like" || strtolower($operator) == "not like" || strtolower($operator) == "ends with" || strtolower($operator) == "ends to" || strtolower($operator) == "ends")
            {
                if (substr($value, -1) !== "%")
                {
                    $value .= "%";
                }
            }
        }
        else
        {
            // If $c is null we can default comparison to = 
            $value = $operator;
            $operator = "=";
        }

        // Create unique id for secure input parameter and expand where statement
        $unique_string = 'param_' . uniqid();
        $this->arguments["where"] .= "$field $operator :$unique_string";
        $this->secured_inputs[$unique_string] = $value;
    }


    public function where($field, $operator, $value = null)
    {
        $this->baseWhere("and", $field, $operator, $value);
        return $this;
    }


    public function orWhere($field, $operator, $value = null)
    {
        $this->baseWhere("or", $field, $operator, $value);
        return $this;
    }


    public function whereBetween($field, $min, $max)
    {
        $this->arguments["where"] .= " and ( 1 = 1";
        $this->where($field, ">=", $min);
        $this->where($field, "<=", $max);
        $this->arguments["where"] .= ")";
        return $this;
    }


    public function orWhereBetween($field, $min, $max)
    {
        $this->arguments["where"] .= " or ( 1 = 1";
        $this->where($field, ">=", $min);
        $this->where($field, "<=", $max);
        $this->arguments["where"] .= ")";
        return $this;
    }


    public function whereNotBetween($field, $min, $max)
    {
        $this->arguments["where"] .= " and ( 1 = 1";
        $this->where($field, "<=", $min);
        $this->where($field, ">=", $max);
        $this->arguments["where"] .= ")";
        return $this;
    }


    public function orWhereNotBetween($field, $min, $max)
    {
        $this->arguments["where"] .= " or ( 1 = 1";
        $this->where($field, "<=", $min);
        $this->where($field, ">=", $max);
        $this->arguments["where"] .= ")";
        return $this;
    }


    private function baseWhereIn($connector, $key, array $values_array)
    {
        // Add connector to where statement if it is not empty
        if (!empty($this->arguments["where"]))
        {
            $this->arguments["where"] .= " $connector ";
        }

        $this->arguments["where"] .= "$key in (";

        foreach ($values_array as $value)
        {
            $this->arguments["where"] .= "'$value',";
        }

        $this->arguments["where"] = substr($this->arguments["where"], 0, -1);
        $this->arguments["where"] .= ")";
    }


    public function whereIn($key, array $values_array)
    {
        $this->baseWhereIn("and", $key, $values_array);
        return $this;
    }


    public function orWhereIn($key, array $values_array)
    {
        $this->baseWhereIn("or", $key, $values_array);
        return $this;
    }


    public function whereInOr($array_in, $array_or)
    {
        $this->arguments["where"] .= " and ( 1 = 1";

        foreach ($array_in as $key => $values_array)
        {
            $this->baseWhereIn("and", $key, $values_array);
        }
        foreach ($array_or as $key => $values_array)
        {
            $this->baseWhereIn("or", $key, $values_array);
        }

        $this->arguments["where"] .= ")";
        return $this;
    }


    private function baseWhereNull($connector, $key, $is_null)
    {
        if (!empty($this->arguments["where"]))
        {
            $this->arguments["where"] .= " $connector ";
        }

        $this->arguments["where"] .= "$key is ". ($is_null ? "" : "not")  ." NULL";
    }


    public function whereNull($key)
    {
        $this->baseWhereNull("and", $key, true);
        return $this;
    }


    public function orWhereNull($key)
    {
        $this->baseWhereNull("or", $key, true);
        return $this;
    }


    public function whereNotNull($key)
    {
        $this->baseWhereNull("and", $key, false);
        return $this;
    }


    public function orWhereNotNull($key)
    {
        $this->baseWhereNull("or", $key, false);
        return $this;
    }


    public function orderBy($arg, $order = "asc")
    {
        if ($order !== "asc" && $order !== "desc")
        {
            $order = "asc";
        }

        $this->arguments["orderBy"] = "order by $arg $order";
        return $this;
    }


    public function limit($arg)
    {
        $this->arguments["limit"] = "limit $arg";
        return $this;
    }


    public function offset($arg)
    {
        $this->arguments["offset"] = "offset $arg";
        return $this;
    }


    private function baseGet()
    {
        $query = "select * from $this->table_name";
        if ($this->arguments["select"])
        {
            $query = "select {$this->arguments['select']} from $this->table_name";
        }
        if ($this->arguments["where"])
        {
            $query .= " where {$this->arguments['where']}";
        }
        if ($this->arguments["orderBy"])
        {
            $query .= " {$this->arguments["orderBy"]}";
        }
        if ($this->arguments["limit"])
        {
            $query .= " {$this->arguments["limit"]}";
        }
        if ($this->arguments["offset"])
        {
            $query .= " {$this->arguments["offset"]}";
        }
        $query .= ";";

        $statement = $this->connection->prepare($query);
        if (!$statement) return false;
        $statement->execute($this->secured_inputs);
        $this->clearQuery();
        return $statement;
    }

    public function get($type = "array")
    {
        $statement = $this->baseGet();
        if (!$statement) {
            return ($type === "array") ? [] : (object)[];
        }

        if ($type === "array") {
            return $statement->fetch(\PDO::FETCH_ASSOC) ?: [];
        }

        return $statement->fetch(\PDO::FETCH_OBJ) ?: (object)[];
    }
    
    public function getAll($type = "array")
    {
        $statement = $this->baseGet();
        if (!$statement) return false;

        if ($type !== "array")
        {
           return (object) $statement->fetchAll(\PDO::FETCH_OBJ);
        }
        else
        {
          return  $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
    }


    public function value($value)
    {
        $statement = $this->baseGet();
        $fetch = $statement->fetch(\PDO::FETCH_ASSOC);
        return $fetch[$value];
    }


    public function values($value, $type = "array")
    {
        $statement = $this->baseGet();
        $fetch = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $values = array();

        foreach ($fetch as $data)
        {
            array_push($values, $data[$value]);
        }

        return ($$type !== "array") ? (object) $values : $values;
    }


    public function count()
    {
        if (empty($this->arguments['where']))
        {
            $count = $this->fetch("select count(*) from $this->table_name");
        }
        else
        {
            $count = $this->fetch("select count(*) from $this->table_name where {$this->arguments['where']}", $this->secured_inputs);
        }
        
        $this->clearQuery();
        return (int)$count["count(*)"];
    }


    public function doesExist($field, $value = null)
    {
        if (is_array($field) && $value === null)
        {
            $query = $this->select("1");

            foreach ($field as $key => $value)
            {
                $query = $query->where($key, $value);
            }

            return $query->get();
        }
        else
        {
            return $this->select("1")->where($field, $value)->get();
        }
    }


    public function insert(array $values)
    {
        // Create string of all the insert values
        foreach ($values as $value)
        {
            $unique_string = 'param_' . uniqid();
            $this->arguments["insert"] .= ":$unique_string, ";
            $this->secured_inputs[$unique_string] = $value;
        }

        // Remove ', ' from the end of the string
        $this->arguments["insert"] = substr($this->arguments["insert"], 0, -2);

        // Create insert statement
        $query = "insert into $this->table_name values ({$this->arguments["insert"]});";

        // Execute statement
        $result = $this->connection->prepare($query)->execute($this->secured_inputs);
        $this->clearQuery();
        return $result;
    }


    public function update(array $args)
    {
        // Create string of all the update values
        foreach ($args as $key => $value)
        {
            $unique_string = 'param_' . uniqid();
            $this->arguments["update"] .= "$key = :$unique_string, ";
            $this->secured_inputs[$unique_string] = $value;
        }

        // Remove ', ' from the end of the string
        $this->arguments["update"] = substr($this->arguments["update"], 0, -2);

        // Create update statement
        $query = "update $this->table_name set {$this->arguments["update"]}";
        if ($this->arguments["where"])
        {
            $query .= " where {$this->arguments['where']}";
        }
        $query .= ";";

        // Execute statement
        $result = $this->connection->prepare($query)->execute($this->secured_inputs);
        $this->clearQuery();
        return $result;
    }


    public function delete()
    {
        $query = "delete from $this->table_name where ";

        // Add where condition
        $query .= ($this->arguments["where"]) ? "{$this->arguments['where']};" : "1 = 1;" ;

        // Execute statement
        $result = $this->connection->prepare($query)->execute($this->secured_inputs);
        $this->clearQuery();
        return $result;
    }
}
