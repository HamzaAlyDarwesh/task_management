<?php

namespace App\Interfaces;

interface CommentRepositoryInterface
{
    public function list(array $data);

    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);

    public function show(int $id);
}
