<?php

namespace App\Services;

use App\Repositories\CategoryRepository;   
use illuminate\Support\Facades\Storage;
class CategoryService {
    private $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAll(array $fields) {
        return $this->categoryRepository->getAll($fields);
    }

    public function getById(int $id, array $fields) {
        return $this->categoryRepository->getById($id, $fields ?? ['*']);
    }

    public function create(array $data) {
        return $this->categoryRepository->create($data);
    }

    public function update(int $id, array $data) {
        $fields = ['id'];
        $category = $this->categoryRepository->getById($id, $fields);
        return $this->categoryRepository->update($id, $data);
    }

    public function delete(int $id) {
        $fields = ['id'];
        $category = $this->categoryRepository->getById($id, $fields);
        return $this->categoryRepository->delete($id);
    }

}