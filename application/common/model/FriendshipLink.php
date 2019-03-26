<?php
namespace app\common\model;

use traits\model\SoftDelete;
class FriendshipLink extends Model
{
	use SoftDelete;
    protected $name = 'friendship_link';
    protected $autoWriteTimestamp = true;

}
