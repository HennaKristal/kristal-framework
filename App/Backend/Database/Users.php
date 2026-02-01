<?php namespace Backend\Database;
defined("ACCESS") or exit("Access Denied");

use Backend\Core\Database;
use Backend\Core\Entity;

class Users extends Database
{
    private static $instance = null;
    protected $database = "primary";
    protected $table;
    protected $columns;
    protected $primary_key;

    public function __construct()
    {
        $this->table = "users";
        $this->primary_key = "id";

        $this->columns = array(
            "id" => "int(11) unsigned AUTO_INCREMENT",
            "username" => "varchar(100)",
            "email" => "varchar(200) NOT NULL UNIQUE",
            "password_hash" => "varchar(255)",
        );

        parent::__construct(["database" => $this->database]);
        $this->confirmTable();
    }

    // Singleton Pattern
    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new Users();
        }
        return self::$instance;
    }

    /* ====================================================================== */
    /*                           DATABASE API CALLS                           */
    /* ====================================================================== */

    public function createUser(string $username, string $email, string $password_hash, string $salt): bool
    {
        return $this->table()->insert([
            null,
            $username,
            $email,
            $password_hash,
        ]);
    }

    public function getUser(int $id): object
    {
        return $this->table()
            ->where("id", $id)
            ->select(["id", "username", "email", "password_hash"])
            ->get("object");
    }

    public function updateUserEmail(int $id, string $email): bool
    {
        return $this->table()
            ->where("id", $id)
            ->update([
                "email" => $email
            ]);
    }

    public function deleteUser(int $id, string $email): bool
    {
        return $this->table()
            ->where("id", $id)->where("email", $email)
            ->delete();
    }
}

/* ===================================================================== */
/*                            DATABASE Entity                            */
/* ===================================================================== */
class User extends Entity
{
    protected string $database = 'primary';
    protected string $table = 'users';
    protected string $primary_key = 'id';

    protected array $columns = [
        'id' => 'int(11) unsigned AUTO_INCREMENT',
        'username' => 'varchar(' . USERNAME_MAX_LENGTH . ')',
        'email' => 'varchar(' . EMAIL_MAX_LENGTH . ')',
        'password_hash' => 'varchar(255)',
    ];

    public ?int $id = null;
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password_hash = null;

    public function __construct(?int $id = null)
    {
        parent::__construct($id, $this->database);
    }
}