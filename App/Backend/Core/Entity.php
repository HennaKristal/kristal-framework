<?php declare(strict_types=1);
namespace Backend\Core;
defined("ACCESS") or exit("Access Denied");

use Backend\Core\Database;

abstract class Entity extends Database
{
    protected string $table;
    protected string $primary_key = 'id';
    protected array $columns = [];
    protected mixed $identifier_value = null;
    protected bool $persisted = false;

    public function __construct(mixed $identifier = null, string $database = 'primary')
    {
        parent::__construct(['database' => $database]);
        $this->confirmTable();

        if ($identifier !== null)
        {
            $this->load($identifier);
        }
    }

    /* ===================================================== */
    /*                       Loading                         */
    /* ===================================================== */
    protected function load(mixed $identifier): void
    {
        $this->assertSafeIdentifier($this->primary_key);

        $row = $this->table()->where($this->primary_key, $identifier)->getFirst();

        if (empty((array) $row))
        {
            return;
        }

        foreach ($this->columns as $column => $_)
        {
            $this->{$column} = $row->{$column} ?? null;
        }

        $this->identifier_value = $identifier;
        $this->persisted = true;
    }

    /* ===================================================== */
    /*                       Saving                          */
    /* ===================================================== */
    public function save(): bool
    {
        $data = [];

        foreach ($this->columns as $column => $_)
        {
            if ($column === $this->primary_key)
                continue;

            $data[$column] = $this->{$column} ?? null;
        }

        if ($this->persisted)
        {
            return $this->table()->where($this->primary_key, $this->identifier_value)->update($data);
        }

        $result = $this->table()->insert($data);

        if ($result)
        {
            $this->identifier_value = $this->connection->lastInsertId();
            $this->persisted = true;
        }

        return $result;
    }

    /* ===================================================== */
    /*                       Delete                          */
    /* ===================================================== */
    public function delete(): bool
    {
        if (!$this->persisted)
            return false;

        $result = $this->table()->where($this->primary_key, $this->identifier_value)->delete();

        if ($result)
        {
            $this->persisted = false;
            $this->identifier_value = null;
        }

        return $result;
    }

    /* ===================================================== */
    /*                   State helpers                      */
    /* ===================================================== */
    public function exists(): bool
    {
        return $this->persisted;
    }

    public function getId(): mixed
    {
        return $this->identifier_value;
    }
}


// class Entity extends Database
// {
//     protected $identifier_key;
//     protected $identifier_value;

//     public function __construct(array $entity_information)
//     {
//         $this->identifier_key = $entity_information["identifier_key"];
//         $this->identifier_value = $entity_information["identifier_value"];
//         parent::__construct(["database" => $entity_information["database"]]);
//         $this->confirmTable();

//         // Get values from database
//         if (isset($this->identifier_key) && isset($this->identifier_value))
//         {
//             $entity = $this->table()->where($this->identifier_key, $this->identifier_value)->getFirst("array");

//             if ($entity)
//             {
//                 foreach ($this->columns as $key => $value)
//                 {
//                     $this->{$key} = $entity[$key];
//                 }
//             }
//             else
//             {
//                 $this->identifier_key = null;
//                 $this->identifier_value = null;
//             }
//         }
//     }


//     // Save data to database
//     public function save()
//     {
//         if (isset($this->identifier_key) && isset($this->identifier_value))
//         {
//             // Create update data
//             foreach ($this->columns as $key => $value)
//             {
//                 $update[$key] = $this->{$key};
//             }

//             $this->table()->where($this->identifier_key, $this->identifier_value)->update($update);
//         }
//         else
//         {
//             // Create insert data
//             foreach ($this->columns as $key => $value)
//             {
//                 $insert[$key] = $this->{$key};
//             }

//             $this->table()->insert($insert);
//         }
//     }


//     // Delete data from database
//     public function delete(): bool
//     {
//         if (isset($this->identifier_key) && isset($this->identifier_value))
//         {
//             $this->table()->where($this->identifier_key, $this->identifier_value)->delete();
//         }
//     }
// }
