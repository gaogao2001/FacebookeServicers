<?php

namespace App\Modules\Facebook\Repositories\Account;

use App\Repositories\BaseRepositoryInterface;

interface AccountRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll();
	
	public function countAll();

    public function create(array $data);

    public function findById($id);

    public function searchAccounts(array $filters = []);

    public function update($id, array $data);

    public function delete($id);

    public function deleteByUids(array $uids);

    public function deleteAll();

    public function findByUid($uid);

    public function updateStatus(string $uid, string $status): bool;

    public function countAccounts(array $filters = []): int;

    public function updateConfigAuto(array $data);

    public function updateInteractLimit(array $interactLimitData);

    public function updateConfigAutoById($id, array $data);

    public function findByUids(array $uids, array $options = []);

    public function getMultipleFriendsData(array $uids);
    
    public function getMultipleGroupsData(array $uids);

    public function getMultiplePostsData(array $uids);

    // public function getHistoryData(array $uids);
    //đổi nhóm
    public function getUniqueGroupAccounts();

    public function updateManyByUids(array $uids, array $data);

    public function updateByUid(string $uid, array $data);

    public function findByGroup(string $group): array;

    public function getAllUids(): array;

    public function getUniqueStatus();
    
    public function getAccounts(bool $hasProxy): array;

    public function fixBirthday ();

    public function filterFriendsByRange(array $range);
    
    public function filterGroupsByRange(array $range);

    public function parseProxy(string $proxy): ?array;
	
	public function UpdateAll(array $data, ?array $condition = null);

    public function updateNetworkUse($uid, array $proxyData);

    public function paginate(array $filters = [], int $perPage = 1000, int $page = 1);

}
