<?php
namespace App\Transformers;

use App\Groups;
use League\Fractal\TransformerAbstract;

class GroupTransformer extends TransformerAbstract
{
    public function transform(Groups $group)
    {
        return [
            'id' => $group->id,
            'group_name' => $group->group_name,
            'group_desc' => $group->group_desc,
            'group_owner_id' => $group->group_owner_id,
            'is_public_group' => $group->is_public_group,
            'created_at' => $group->created_at,
            'updated_at' => $group->updated_at
            
        ];
    }
}