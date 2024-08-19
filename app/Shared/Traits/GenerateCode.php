<?php

namespace App\Shared\Traits;

trait GenerateCode
{
    private function getCodigo(string $prefix): string
    {
        if (!isset($this->model)) return $prefix . '/' . date('Y');
        $last = $this->model->latest('id')->first()?->id;
        $key = sprintf('%02d', $last + 1);
        return $prefix . '-' . $key . '/' . date('Y');
    }
}
