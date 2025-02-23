<?php
namespace App\Modules\Network\Repositories\ProxyV6;

use App\Repositories\BaseRepository;
use App\Modules\Network\Repositories\ProxyV6\ProxyV6RepositoryInterface;
use MongoDB\BSON\ObjectId;


class ProxyV6Repository extends BaseRepository implements ProxyV6RepositoryInterface
{
   
    public function __construct()
    {
        parent::__construct('ProxyV6' , 'NetworkControler');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function DeleteAllProxyExpired($_Ipv6List = [])
    {
        if (count($_Ipv6List) > 0) {
            $conditions = [];
            foreach ($_Ipv6List as $block) {
                $conditions[] = ['interface' => new \MongoDB\BSON\Regex(preg_quote($block), 'i')];
            }
            $this->model->deleteMany(['$nor' => $conditions]);
        }
    }

    public function findById($id)
    {
        return $this->model->findOne(['_id' => new ObjectId($id)]);
    }
	
	public function findOne($filters = [])
    {
        return $this->model->findOne($filters);
    }

    public function deleteAllProxies()
    {
        $documents = $this->model->find([]);
		$ports = [];
		foreach ($documents as $document) {
			$ports[] = $document->port;
		}

        $this->model->drop();


        return $ports;
    }

    public function deleteOne($id)
    {
        return $this->model->deleteOne(['_id' => new ObjectId($id)]);
    }

    public function list($filters = [], $options = [])
    {
        return $this->model->find($filters, $options);
    }

    public function insertOne($data)
    {
        return $this->model->insertOne($data);
    }

    public function updateOne($id, $data)
    {
        return $this->model->updateOne(['_id' => new ObjectId($id)], ['$set' => $data]);
    }
    
    public function searchProxies(array $filters = [], int $perPage = 500, int $page = 1)
    {
        $options = [
            'limit' => $perPage,
            'skip' => ($page - 1) * $perPage,
            'sort' => ['_id' => -1]
        ];

        $cursor = $this->model->find($filters, $options);
        $data = $cursor->toArray();

        $total = $this->model->countDocuments($filters);
        $lastPage = ceil($total / $perPage);

        return [
            'data' => $data,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'per_page' => $perPage,
            'total' => $total
        ];
    }

    public function countProxies($conditions = [])
    {
        return $this->model->countDocuments($conditions);
    }

    public function getProxiesV6ByIds($ids, $select = ["*"])
    {
        $query = ['config_name' => ['$in' => $ids]];

        $options = [];
        if ($select !== ["*"]) {
            $projection = array_fill_keys($select, 1);
            $options['projection'] = $projection;
        }

        $cursor = $this->model->find($query, $options);

        $results = iterator_to_array($cursor);

        return $results;
    }

}