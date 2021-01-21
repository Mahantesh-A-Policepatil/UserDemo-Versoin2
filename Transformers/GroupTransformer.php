<?php
namespace App\Transformers;

use App\Group;
use League\Fractal\TransformerAbstract;

class GroupTransformer extends TransformerAbstract
{
    public function transform(Group $group)
    {
        return [
            'id' => $group->id,
            'group_name' => $group->group_name,
            'group_desc' => $group->group_desc,
            'group_owner_id' => $group->group_owner_id,
            'is_public_group' => $group->is_public_group,
            'created_at' => $group->created_at->format('Y-m-d g:iA'),
            'updated_at' => $group->updated_at->format('Y-m-d g:iA')

        ];
    }
}