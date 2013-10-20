<?php

namespace Efficio\Tests\Dataset;

use Efficio\Dataset\Model;
use Efficio\Dataset\Storage\Model\FileStorage;
use Efficio\Dataset\Storage\Model\SessionStorage;
use Efficio\Dataset\Storage\Model\DatabaseStorage;

class BasicModel extends Model
{
    protected $first_name;
    protected $last_name;
}

class UserProps extends Model
{
    protected $first_name;
    protected $last_name;
    protected $age;
    protected $colors = [];
}

class UserFuncs extends Model
{
    public $set = false;
    public $get = false;
    protected $first_name;

    public function setFirstName($fn)
    {
        $this->set = true;
        $this->first_name = $fn;
    }

    public function getFirstName()
    {
        $this->get = true;
        return $this->first_name;
    }
}

class BasicSessionModel extends BasicModel
{
    use SessionStorage;
}

class BasicFileModel extends BasicModel
{
    use FileStorage;
}

class Post extends Model
{
    use DatabaseStorage;

    protected $label;
    protected $created_date;
}

class ImportantPost extends Post
{
}

/**
 * @table posts
 */
class AnnotatedPost extends Post
{
    /**
     * @virtual
     */
    protected $ignored;
}

class Comment extends Model
{
    use DatabaseStorage;

    protected $text;
    protected $user_id;
    protected $post_id;
}
