<?php

namespace Core;

use PDO;

abstract class Model
{
    protected ?int $id = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    abstract protected static function tableName(): string;

    /**
     * Mapping data array ke property model
     * (override di child kalau perlu)
     */
    public function fill(array $data): void
    {
        $this->id         = isset($data['id']) ? (int)$data['id'] : $this->id;
        $this->created_at = $data['created_at'] ?? $this->created_at;
        $this->updated_at = $data['updated_at'] ?? $this->updated_at;
    }

    /**
     * Insert / update tergantung ada id atau tidak
     */
    public function save(): bool
    {
        return $this->id === null ? $this->insert() : $this->update();
    }

    // Child harus mengimplementasi insert & update
    abstract protected function insert(): bool;
    abstract protected function update(): bool;

    /**
     * Helper static find by id
     */
    public static function find(int $id): ?static
    {
        $db = Database::getConnection();
        $table = static::tableName();

        $stmt = $db->prepare("SELECT * FROM {$table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        /** @var static $model */
        $model = new static();
        $model->fill($row);
        return $model;
    }

    /**
     * Helper ambil semua record
     */
    public static function all(): array
    {
        $db = Database::getConnection();
        $table = static::tableName();

        $stmt = $db->query("SELECT * FROM {$table}");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new static();
            $model->fill($row);
            return $model;
        }, $rows);
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
