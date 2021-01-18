<?php
namespace App\Transformers;

use App\GroupUsers;
use League\Fractal\TransformerAbstract;

class GroupUsersTransformer extends TransformerAbstract
{
    public function transform(GroupUsers $groupUsers)
    {
        return [
            'id' => $groupUsers->id,
            'group_id' => $groupUsers->group_id,
            'user_id' => $groupUsers->user_id,
            'created_at' => $groupUsers->created_at,
            'updated_at' => $groupUsers->updated_at
         ];
    }
}