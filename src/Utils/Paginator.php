<?php
// src/Utils/Paginator.php

namespace App\Utils;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class Paginator
{
    private $items;
    private $currentPage;
    private $itemsPerPage;
    private $totalItems;
    private $totalPages;

    public function __construct(DoctrinePaginator $items, int $currentPage = 1, int $itemsPerPage = 10)
    {
        $this->items = $items;
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalItems = count($items);
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
    }

    public function getItems(): iterable
    {
        return $this->items;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    public function getNextPage(): int
    {
        return min($this->totalPages, $this->currentPage + 1);
    }

    public function getPageRange(int $maxPages = 5): array
    {
        $middle = ceil($maxPages / 2);
        
        if ($this->totalPages <= $maxPages) {
            return range(1, $this->totalPages);
        }
        
        if ($this->currentPage <= $middle) {
            return range(1, $maxPages);
        }
        
        if ($this->currentPage > ($this->totalPages - $middle)) {
            return range($this->totalPages - $maxPages + 1, $this->totalPages);
        }
        
        return range($this->currentPage - $middle + 1, $this->currentPage + $middle - 1);
    }
}