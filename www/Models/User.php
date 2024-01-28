<?php

namespace App\Models;


use App\DB\Database;
use PDO;

class User
{
    private ?int $id = null;
    protected string $firstname;
    protected string $lastname;
    protected string $email;
    protected string $pwd;
    protected string $role;
    protected int $isDeleted;
    private Database $database;


    public function __construct()
    {
        $this->database = new Database();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $firstname = ucwords(strtolower(trim($firstname)));
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $lastname = strtoupper(trim($lastname));
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $email = strtolower(trim($email));
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPwd(): string
    {
        return $this->pwd;
    }

    /**
     * @param $email
     * @param $newPassword
     * @param null $oldPassword
     * @return bool
     */
    public function setPwd($email, $newPassword, $oldPassword = null): bool
    {
        $pdo = $this->database->getDatabaseConnection();

        // Si l'ancien mot de passe es présent, on le valide d'abord
        if ($oldPassword !== null) {
            $stmt = $pdo->prepare("SELECT pwd FROM users WHERE email = ?");
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();
            // Vérification
            if ($user && !password_verify($oldPassword, $user['pwd'])) {
                return false; // Mot de passe incorrect
            }
        }

        // Hash le nouveau mdp
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // MaJ du mdp dans la bdd
        $updateStmt = $pdo->prepare("UPDATE users SET pwd = ? WHERE email = ?");
        $updateStmt->bindParam(1, $hashedNewPassword, PDO::PARAM_STR);
        $updateStmt->bindParam(2, $email, PDO::PARAM_STR);

        return $updateStmt->execute(); // True si la maJ est effectué
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return bool
     */
    public function isDeleted(): int
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(int $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }




    public function authenticateUser(String $email, String $password): bool {

        $pdo = $this->database->getDatabaseConnection();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            return password_verify($password, $user['pwd']);
        }

        return false;
    }

    public function createUser(String $firstname, String $lastname, String $email, String $password, String $role): bool
    {
        $pdo = $this->database->getDatabaseConnection();

        $stmt = $pdo->prepare("INSERT INTO users (Firstname, Lastname, email, pwd, Role, is_validated, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([$firstname, $lastname, $email, $password, $role, false]);
    }

    public function EmailExists(String $email): bool {
        $pdo = $this->database->getDatabaseConnection();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->bindParam(1, $email, PDO::PARAM_STR); // Use 1 for the first parameter, and PDO::PARAM_STR for a string
        $stmt->execute();

        // Get the results
        $result = $stmt->fetch();

        // Check if the email exists
        return $result[0] > 0;
    }


}