<?php

namespace App\Services;

use App\Models\Set;

class CategoryService extends BaseService
{
    public function setModel()
    {
        return new Set();
    }

    public function getAllCategories()
    {
        return Set::with('categories')->get();
    }

    public function getCategoryById($id){
        return $this->model->find($id);
    }
    
    public function store($data){

        return $this->model->create($data);
    }
    
    public function update($id, $data){
        $category = $this->model->find($id);
        if($category){
            $category->update($data);
            return $category;
        }
        return null;
    }
    
    public function delete($id){
        $category = $this->model->find($id);
        if($category){
            $category->delete();
            return true;
        }
        return false;
    }

    public function addSetCategory($data)
    {
    $set = $this->model->find($data['id']); 
    if (!$set) {
        return false; 
    }

    $newCategory = $set->categories()->create([
        'name' => $data['name'], 
    ]);

    return $newCategory; 
    }
}