<?php

namespace Interfaces;

/**
 * Interface untuk entity yang bisa dipublish / unpublish.
 */
interface Publishable
{
    /**
     * Publikasikan entity (ubah status → published).
     */
    public function publish(): void;

    /**
     * Batalkan publish (status → draft).
     */
    public function unpublish(): void;

    /**
     * Ambil status saat ini (draft/published/archived).
     */
    public function getStatus(): string;

    /**
     * Cek apakah status = published.
     */
    public function isPublished(): bool;
}
