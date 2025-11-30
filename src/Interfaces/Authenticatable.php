<?php

namespace Interfaces;

/**
 * Interface untuk entity yang bisa diautentikasi (login).
 */
interface Authenticatable
{
    public function getId(): ?int;

    public function getEmail(): string;

    public function getPassword(): string;

    /**
     * Verifikasi password plain dengan hashed password.
     */
    public function verifyPassword(string $plain): bool;
}
