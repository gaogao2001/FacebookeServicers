<?php

namespace App\Modules\Facebook\Repositories\Account;

use App\Modules\Facebook\Repositories\Account\AccountRepositoryInterface as AccountRepositoryInterface;
use App\Repositories\BaseRepository;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use HoangquyIT\Helper\Common;

class AccountRepository extends BaseRepository implements AccountRepositoryInterface
{
    public function __construct()
    {
        parent::__construct('Account', 'FacebookData');
    }

    public function findAll()
    {
        return $this->model->find()->toArray();
    }

    public function countAll()
    {
        return $this->model->countDocuments();
    }


    public function findById($id)
    {
        $objectId = new ObjectId($id);

        return $this->model->findOne(['_id' => $objectId]);
    }

    // public function searchAccounts(array $filters = [], int $perPage = 1000, int $page = 1)
    // {
    //     $options = [
    //         'limit' => $perPage,
    //         'skip' => ($page - 1) * $perPage,
    //         'sort' => ['_id' => 1]
    //     ];

    //     $pipeline = [];

    //     // Thêm pipeline birthday từ controller (không lồng thêm key "birthday_pipeline")
    //     if (!empty($filters['birthday_pipeline'])) {
    //         $pipeline = array_merge($pipeline, $filters['birthday_pipeline']);
    //     }

    //     if (!empty($uids)) {
    //         $pipeline[] = ['$match' => ['uid' => ['$in' => $uids]]];
    //     } else {
    //         $pipeline[] = ['$match' => ['uid' => ['$ne' => null]]];
    //     }

    //     // Thêm pipeline last_seeding nếu có
    //     if (!empty($filters['last_seeding_pipeline'])) {
    //         $pipeline = array_merge($pipeline, $filters['last_seeding_pipeline']);
    //     }

    //     // Các bộ lọc bổ sung
    //     $additionalFilters = $filters;
    //     unset($additionalFilters['last_seeding_pipeline'], $additionalFilters['parsed_birthday'], $additionalFilters['birthday_pipeline']);

    //     if (!empty($additionalFilters)) {
    //         $pipeline[] = ['$match' => $additionalFilters];
    //     }

    //     // Thêm limit và skip
    //     $pipeline[] = ['$limit' => $perPage];
    //     $pipeline[] = ['$skip' => ($page - 1) * $perPage];


    //     // Thực hiện query với pipeline
    //     if (!empty($pipeline)) {
    //         $data = $this->model->aggregate(array_values($pipeline))->toArray();
    //     } else {
    //         // Nếu không có pipeline thì query thông thường
    //         $data = $this->model->find($filters, $options)->toArray();
    //     }

    //     // Tổng hợp dữ liệu để trả về
    //     $totalFilters = $filters;
    //     unset($totalFilters['last_seeding_pipeline'], $totalFilters['parsed_birthday'], $totalFilters['birthday_pipeline']);
    //     $total = $this->model->countDocuments($totalFilters);
    //     $lastPage = ceil($total / $perPage);


    //     return [
    //         'data' => $data,
    //         'currentPage' => $page,
    //         'lastPage' => $lastPage,
    //         'per_page' => $perPage,
    //         'total' => $total
    //     ];
    // }
    public function searchAccounts(array $filters = [])
    {
        $pipeline = [];



        // Thêm pipeline birthday từ controller (không lồng thêm key "birthday_pipeline")
        if (!empty($filters['birthday_pipeline'])) {
            $pipeline = array_merge($pipeline, $filters['birthday_pipeline']);
        }

        if (!empty($filters['uids'])) {
            $pipeline[] = ['$match' => ['uid' => ['$in' => $filters['uids']]]];
        } else {
            $pipeline[] = ['$match' => ['uid' => ['$ne' => null]]];
        }

        // Thêm pipeline last_seeding nếu có
        if (!empty($filters['last_seeding_pipeline'])) {
            $pipeline = array_merge($pipeline, $filters['last_seeding_pipeline']);
        }

        // Các bộ lọc bổ sung
        $additionalFilters = $filters;
        unset($additionalFilters['last_seeding_pipeline'], $additionalFilters['parsed_birthday'], $additionalFilters['birthday_pipeline']);

        if (!empty($additionalFilters)) {
            $pipeline[] = ['$match' => $additionalFilters];
        }

        // Thực hiện query với pipeline mà không phân trang
        if (!empty($pipeline)) {
            $data = $this->model->aggregate(array_values($pipeline))->toArray();
        } else {
            $data = $this->model->find()->toArray();
        }

        // Ghép dữ liệu nếu cần

        return $data;
    }

    public function paginate(array $filters = [], int $perPage = 100, int $page = 1)
    {
        $pipeline = [];

        // Áp dụng bộ lọc
        if (!empty($filters)) {
            $pipeline[] = ['$match' => $filters];
        }

        // Sắp xếp dữ liệu
        $pipeline[] = ['$sort' => ['_id' => 1]];



        // Thêm skip và limit cho phân trang
        $pipeline[] = ['$skip' => ($page - 1) * $perPage];
        $pipeline[] = ['$limit' => $perPage];

        try {
            $data = $this->model->aggregate($pipeline)->toArray();
        } catch (\Exception $e) {
            \Log::error('Aggregate error in paginate: ' . $e->getMessage());
            $data = [];
        }


        // Tổng số bản ghi
        $total = $this->model->countDocuments($filters);
        $lastPage = ceil($total / $perPage);


        return [
            'data' => $data,
            'total' => $total,
            'lastPage' => $lastPage,
            'currentPage' => $page,
        ];
    }

    public function update($id, array $data)
    {
        $objectId = new ObjectId($id);
        return $this->model->updateOne(
            ['_id' => $objectId],
            ['$set' => $data]
        );
    }

    public function deleteAll()
    {
        return $this->model->deleteMany([]);
    }
    
    public function delete($id)
    {
        $objectId = new ObjectId($id);
        return $this->model->deleteOne(['_id' => $objectId]);
    }

    public function deleteByUids(array $uids)
    {
        return $this->model->deleteMany(['uid' => ['$in' => $uids]]);
    }

    public function findByUid($uid)
    {
        $account = $this->model->findOne(['uid' => $uid]);

        if (!$account) {
            $account = $this->model->findOne(['MultiAccount.profile.id' => $uid]);
        }

        return $account;
    }

    public function updateStatus(string $uid, string $status): bool
    {
        $updateResult = $this->model->updateOne(
            ['uid' => $uid],
            ['$set' => ['status' => $status]]
        );

        return $updateResult->getModifiedCount() > 0;
    }

    public function countAccounts(array $filters = []): int
    {
        return $this->model->countDocuments($filters);
    }

    public function updateConfigAuto(array $configAutoData)
    {
        return $this->model->updateMany(
            [], // Điều kiện: cập nhật tất cả bản ghi
            ['$set' => ['config_auto' => $configAutoData]] // Chỉ cập nhật trường `config_auto`
        );
    }

    public function updateInteractLimit(array $limitData)
    {
        // Nếu dữ liệu truyền vào có key "interact_limit" thì lấy giá trị bên trong (tránh lồng 2 lớp)
        if (isset($limitData['interact_limit'])) {
            $limitData = $limitData['interact_limit'];
        }
    
        return $this->model->updateMany(
            [],
            ['$set' => ['interact_limit' => $limitData]]
        );
    }

    public function updateConfigAutoById($id, array $configAutoData)
    {
        $objectId = new ObjectId($id);
        return $this->model->updateOne(
            ['_id' => $objectId],
            ['$set' => ['config_auto' => $configAutoData]]
        );
    }

    public function findByUids(array $uids, array $options = [])
    {
        return $this->model->find(
            ['uid' => ['$in' => $uids]],
            $options
        )->toArray();
    }

    public function getMultipleFriendsData(array $uids)
    {
        $collection = app('mongo')->FacebookData->Friends;
        $friendData = $collection->find(['friends_of' => ['$in' => $uids]]);

        $result = [];
        foreach (iterator_to_array($friendData) as $friend) {
            $uid = $friend['friends_of'];
            if (!isset($result[$uid])) {
                $result[$uid] = [];
            }
            $result[$uid][] = $friend;
        }

        return $result;
    }

    public function getMultiplePostsData(array $uids)
    {
        $collection = app('mongo')->FacebookData->Post;
        $postData   = $collection->find(['post_of' => ['$in' => $uids]]);

        $result = [];
        foreach (iterator_to_array($postData) as $post) {
            $uid = $post['post_of'];
            if (!isset($result[$uid])) {
                $result[$uid] = [];
            }
            $result[$uid][] = $post;
        }

        // Nếu chỉ truyền 1 UID => trả về mảng 1 chiều
        if (count($uids) === 1) {
            $singleUid = $uids[0];
            // có hay không có key $singleUid trong $result, trả về mảng trống nếu không có
            return $result[$singleUid] ?? [];
        }

        // Nhiều UID => trả về mảng 2 chiều như cũ
        return $result;
    }

    public function getMultipleGroupsData(array $uids)
    {

        $collection = app('mongo')->FacebookData->Groups;
        $groupData = $collection->find(['groups_of' => ['$in' => $uids]]);

        $result = [];
        foreach (iterator_to_array($groupData) as $group) {
            $uid = $group['groups_of'];
            if (!isset($result[$uid])) {
                $result[$uid] = [];
            }
            $result[$uid][] = $group;
        }



        return $result;
    }

    // public function getHistoryData(array $uids)
    // {
    //     $collection = app('mongo')->History->FacebookHistory;
    //     $historyData = $collection->find(['uid' => ['$in' => $uids]]);

    //     $result = [];
    //     foreach ($historyData as $history) {
    //         $historyObject = json_decode(json_encode($history));

    //         // Kiểm tra và sửa thuộc tính ' status' thành 'status'
    //         if (isset($historyObject->{' status'})) {
    //             $historyObject->status = $historyObject->{' status'};
    //             unset($historyObject->{' status'});
    //         }

    //         $result[$historyObject->uid][] = $historyObject;
    //     }
    //     return $result;
    // }

    public function getAllUids(): array
    {
        // Sử dụng MongoDB cursor để duyệt qua tất cả dữ liệu
        $cursor = $this->model->find([], ['projection' => ['uid' => 1, '_id' => 0]]);
        $uids = [];

        // Duyệt qua từng tài liệu trong cursor
        foreach ($cursor as $document) {
            $uids[] = $document['uid'];
        }
        return $uids;
    }
    public function updateManyByUids(array $uids, array $data)
    {
        try {
            $collection = app('mongo')->FacebookData->Account;
            Log::info('Updating networkuse for UIDs: ' . implode(',', $uids));
            Log::info('Update data: ' . json_encode($data));

            $result = $collection->updateMany(
                ['uid' => ['$in' => $uids]],
                ['$set' => $data]
            );
            Log::info("Matched: " . $result->getMatchedCount() . ", Modified: " . $result->getModifiedCount());

            // Trả về true nếu có tài liệu được matched, bất kể có chỉnh sửa hay không
            return $result->getMatchedCount() > 0;
        } catch (\Exception $e) {
            Log::error('Error in updateManyByUids: ' . $e->getMessage());
            return false;
        }
    }

    public function updateByUid(string $uid, array $data)
    {
        $collection = app('mongo')->FacebookData->Account;
        $result = $collection->updateMany(
            ['uid' => $uid],
            ['$set' => $data]
        );

        return $result->getModifiedCount() > 0;
    }

    public function UpdateAll(array $data, ?array $condition = null)
    {
        $resultInfo = array('status' => false, 'message' => null);
        $collection = app('mongo')->FacebookData->Account;

        // Điều kiện lọc (nếu không có thì mặc định là [])
        $filter = $condition ?? [];

        try {
            $result = $collection->updateMany(
                $filter,
                ['$set' => $data]
            );

            $resultInfo['status'] = true;
            $resultInfo['message'] = 'Cập nhật thành công với ' . $result->getModifiedCount() . ' dữ liệu cập nhật';
        } catch (\Exception $e) {
            // Ghi thông báo lỗi vào message
            $resultInfo['message'] = 'Lỗi xảy ra trong quá trình cập nhật: ' . $e->getMessage();
        }
        return json_decode(json_encode($resultInfo));
    }

    public function findByGroup(string $group): array
    {
        $accounts = $this->model->find(['groups_account' => $group])->toArray();
        return array_map(function ($account) {
            return $account['uid'];
        }, $accounts);
    }

    public function getUniqueGroupAccounts()
    {
        $collection = app('mongo')->FacebookData->Account;
        $pipeline = [
            ['$group' => ['_id' => '$groups_account']],
            ['$project' => ['group' => '$_id', '_id' => 0]]
        ];
        $results = $collection->aggregate($pipeline)->toArray();

        // Lấy danh sách các group
        $groups = array_map(function ($item) {
            return $item['group'];
        }, $results);

        return $groups;
    }

    public function create(array $data)
    {
        $this->model->insertOne($data);
    }

    public function getUniqueStatus()
    {
        $collection = app('mongo')->FacebookData->Account;
        $pipeline = [
            ['$group' => ['_id' => '$status']],
            ['$project' => ['status' => '$_id', '_id' => 0]]
        ];
        $results = $collection->aggregate($pipeline)->toArray();

        // Lấy danh sách các status
        $statuses = array_map(function ($item) {
            return $item['status'];
        }, $results);

        return $statuses;
    }

    public function getAccounts(bool $hasProxy): array
    {
        // Nếu $hasProxy = true, kiểm tra các tài khoản có cổng proxy
        // Nếu $hasProxy = false, kiểm tra các tài khoản không có cổng proxy
        $condition = $hasProxy ? ['networkuse.port' => ['$ne' => null]] : ['networkuse.port' => null];
        return $this->model->find($condition)->toArray();
    }

    public function filterFriendsByRange(array $range)
    {
        $collection = app('mongo')->FacebookData->Friends;

        // Pipeline để nhóm và đếm số lượng bạn bè theo `friends_of`
        $pipeline = [
            [
                '$group' => [
                    '_id' => '$friends_of', // Nhóm theo `friends_of`
                    'friend_count' => ['$sum' => 1], // Đếm số lượng bạn bè
                ]
            ]
        ];

        // Thêm điều kiện lọc theo khoảng số lượng bạn bè
        $matchConditions = [];
        if (!empty($range['from'])) {
            $matchConditions['friend_count']['$gte'] = $range['from'];
        }
        if (!empty($range['to'])) {
            $matchConditions['friend_count']['$lte'] = $range['to'];
        }

        if (!empty($matchConditions)) {
            $pipeline[] = ['$match' => $matchConditions];
        }

        // Thêm projection để chỉ trả về UID
        $pipeline[] = [
            '$project' => [
                'uid' => '$_id',
                '_id' => 0
            ]
        ];


        // Chạy query
        $result = $collection->aggregate($pipeline);

        $uids = [];
        foreach ($result as $item) {
            $uids[] = $item['uid'];
        }

        return $uids;
    }

    public function filterGroupsByRange(array $range)
    {
        $collection = app('mongo')->FacebookData->Groups;

        // Pipeline để nhóm và đếm số lượng nhóm theo `groups_of`
        $pipeline = [
            [
                '$group' => [
                    '_id' => '$groups_of', // Nhóm theo `groups_of`
                    'group_count' => ['$sum' => 1], // Đếm số lượng nhóm
                ]
            ]
        ];

        // Thêm điều kiện lọc theo khoảng số lượng nhóm
        $matchConditions = [];
        if (!empty($range['from'])) {
            $matchConditions['group_count']['$gte'] = $range['from'];
        }
        if (!empty($range['to'])) {
            $matchConditions['group_count']['$lte'] = $range['to'];
        }

        if (!empty($matchConditions)) {
            $pipeline[] = ['$match' => $matchConditions];
        }

        // Thêm projection để chỉ trả về UID
        $pipeline[] = [
            '$project' => [
                'uid' => '$_id',
                '_id' => 0
            ]
        ];

        // Chạy query
        $result = $collection->aggregate($pipeline);
        $uids = [];
        foreach ($result as $item) {
            $uids[] = $item['uid'];
        }

        return $uids;
    }

    public function fixBirthday(): array
    {
        $_commonHelper = new Common();
        try {
            $updatedCount = 0;

            // Lấy tất cả tài khoản có trường 'birthday' là chuỗi không rỗng
            $cursor = $this->model->find([
                'birthday' => [
                    '$exists' => true,
                    '$nin' => [null, ''],
                    '$type' => 'string'
                ]
            ]);

            foreach ($cursor as $account) {
                $_id = $account['_id'];
                $birthday = $_commonHelper->convertBirthday($account['birthday']); // Sửa lỗi thừa dấu `)` trong phương thức này.
                if (!empty($birthday)) {
                    // Cập nhật ngày sinh đã chuyển đổi
                    $this->model->updateOne(
                        ['_id' => $_id],
                        ['$set' => ['birthday' => $birthday]] // Đóng dấu ngoặc vuông và ngoặc nhọn đúng cách
                    );
                    $updatedCount++; // Tăng biến đếm số lượng cập nhật
                }
            }

            return [
                'status' => true,
                'updatedCount' => $updatedCount // Trả về số lượng đã cập nhật
            ];
        } catch (\Exception $e) {
            Log::error('fixBirthday thất bại: ' . $e->getMessage());

            return [
                'status' => false,
                'error' => $e->getMessage() // Trả về thông báo lỗi nếu xảy ra ngoại lệ
            ];
        }
    }


    public function parseProxy(string $proxy): ?array
    {
        $parts = explode(':', $proxy);

        if (count($parts) === 2) {
            return [
                'type' => 'proxy',
                'ip' => $parts[0],
                'port' => (int)$parts[1],
                'username' => null,
                'password' => null
            ];
        } elseif (count($parts) === 4) {
            return [
                'type' => 'proxy',
                'ip' => $parts[0],
                'port' => (int)$parts[1],
                'username' => $parts[2],
                'password' => $parts[3]
            ];
        }

        // Định dạng proxy không hợp lệ
        return null;
    }

    public function updateNetworkUse($uid, array $proxyData)
    {
        return $this->model->updateOne(
            ['uid' => $uid],
            ['$set' => [
                'networkuse.type' => $proxyData['type'],
                'networkuse.ip' => $proxyData['ip'],
                'networkuse.port' => $proxyData['port'],
                'networkuse.username' => $proxyData['username'],
                'networkuse.password' => $proxyData['password'],
            ]]
        );
    }
}
