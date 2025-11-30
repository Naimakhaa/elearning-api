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
        $this->id         = isset($data['id']) ? (int)$data['id'] : null;
        $this->email      = $data['email'] ?? '';
        $this->password   = $data['password'] ?? '';
        $this->name       = $data['name'] ?? '';
        $this->phone      = $data['phone'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    /*
    |------------------------------------------------------------
    | VALIDATION
    |------------------------------------------------------------
    */
    public function validateBasic(): array
    {
        $errors = [];

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid.';
        }

        // Saat create (id null), password minimal 6 karakter
        if ($this->id === null && strlen($this->password) < 6) {
            $errors[] = 'Password minimal 6 karakter.';
        }

        if (empty($this->name)) {
            $errors[] = 'Nama wajib diisi.';
        }

        return $errors;
    }

    abstract public function validate(): array;


    /*
    |------------------------------------------------------------
    | PASSWORD HANDLING
    |------------------------------------------------------------
    */
    public static function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $plain): bool
    {
        return password_verify($plain, $this->password);
    }


    /*
    |------------------------------------------------------------
    | SERIALIZATION
    |------------------------------------------------------------
    */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'password'   => $this->password,
            'name'       => $this->name,
            'phone'      => $this->phone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }


    /*
    |------------------------------------------------------------
    | GETTERS
    |------------------------------------------------------------
    */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }


    /*
    |------------------------------------------------------------
    | SETTERS
    |------------------------------------------------------------
    */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $plainPassword): void
    {
        $this->password = self::hashPassword($plainPassword);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }
}
