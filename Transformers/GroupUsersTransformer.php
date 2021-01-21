<?php
namespace App\Transformers;

use App\GroupUser;
use League\Fractal\TransformerAbstract;

class GroupUsersTransformer extends TransformerAbstract
{
    public function transform(GroupUser $groupUsers)
    {
        return [
            'id' => $groupUsers->id,
            'group_id' => $groupUsers->group_id,
            'user_id' => $groupUsers->user_id,
            'created_at' => $groupUsers->created_at->format('Y-m-d g:iA'),
            'updated_at' => $groupUsers->updated_at->format('Y-m-d g:iA')
         ];
    }
}