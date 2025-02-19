<?php
namespace App\Repositories\Account;

use App\Repositories\BaseRepositoryInterface;


interface AccountRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email);
    
    public function findByRole(string $role);
    
    public function create(array $data);
    
    public function findAll();
    
    public function findById($id);
    
    public function update($id, array $data);
    
    public function delete($id);

    public function findByToken(string $token);

    public function findByIds(array $ids);

    public function findAdmin($_idRoles);
    
}