<?php
namespace Models;

abstract class User
{
    protected ?int $id;
    protected string $email;
    protected string $password; // hashed
    protected string $name;
    protected ?string $phone;
    protected ?string $created_at;
    protected ?string $updated_at;

    public function __construct(array $data = [])
    {
        $this->id         = $data['id'] ?? null;
        $this->email      = $data['email'] ?? '';
        $this->password   = $data['password'] ?? '';
        $this->name       = $data['name'] ?? '';
        $this->phone      = $data['phone'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    // Basic validation used by child classes
    public function validateBasic(): array
    {
        $errors = [];

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid.';
        }

        if ($this->id === null && strlen($this->password) < 6) {
            // ketika create: password wajib minimal 6
            $errors[] = 'Password minimal 6 karakter.';
        }

        if (empty($this->name)) {
            $errors[] = 'Nama wajib diisi.';
        }

        return $errors;
    }

    public static function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $plain): bool
    {
        return password_verify($plain, $this->password);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'name' => $this->name,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    // Each child must implement table name and save/find logic if desired
    abstract public function validate(): array;
}