<?php

namespace App\Repositories\Account;

use App\Repositories\BaseRepository;
use App\Repositories\Roles\RoleRepositoryInterface;
use MongoDB\BSON\ObjectId;


class AccountRepository extends BaseRepository implements AccountRepositoryInterface
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        parent::__construct('Account');
    }
   

    public function findByEmail(string $email)
    {
        return $this->model->findOne(['email' => $email]);
    }

    public function findByRole(string $role)
    {
        return $this->model->findOne(['role' => $role]);
    }

    public function create(array $data)
    {
        return $this->model->insertOne($data);
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function findById($id)
    {
        $objectId = new ObjectId($id);

        return $this->model->findOne(['_id' => $objectId]);
    }

    public function update($id, array $data)
    {
        $objectId = new ObjectId($id);

        return $this->model->updateOne(['_id' => $objectId], ['$set' => $data]);
    }

    public function delete($id)
    {
        $objectId = new ObjectId($id);

        return $this->model->deleteOne(['_id' => $objectId]);
    }

    public function findByToken(string $token)
    {
        return $this->model->findOne(['token' => $token]);
    }


    public function findByIds(array $ids)
    {
        $objectIds = array_map(function ($id) {
            return new ObjectId($id);
        }, $ids);

        return $this->model->find(['_id' => ['$in' => $objectIds]])->toArray();
    }

    /*
	* cần truyền thông số _id Roles vào để lấy do trong account đang lưu quyền dưới dạng _id , lưu ý _id là dạng object lấy trực tiếp từ mongodb ra
	*/
    public function findAdmin($_idRoles)
    {
        // Tìm tài khoản có role là admin
        return $this->model->findOne(['role' => $_idRoles]);
    }
}
