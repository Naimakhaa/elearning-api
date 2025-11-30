<?php

namespace Interfaces;

/**
 * Interface untuk object yang bisa di-enroll seperti Course.
 */
interface EnrollAble
{
    /**
     * Cek apakah entity masih boleh menerima enrollment baru.
     */
    public function canEnroll(int $currentEnrolledCount): bool;

    /**
     * Dipanggil ketika ada enrollment baru.
     */
    public function onEnroll(): void;

    /**
     * Dipanggil ketika enrollment dibatalkan.
     */
    public function onCancelEnrollment(): void;
}
