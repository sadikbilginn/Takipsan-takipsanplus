<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'parent_id', 'title', 'icon', 'uri', 'sort',
    ];

    public function children()
    {
        return $this->hasMany('App\Menu', 'parent_id')->orderBy('sort')->with(['children', 'roles']);
    }

    public function roles(){
        return $this->belongsToMany('App\Role', 'menu_role','menu_id', 'role_id');
    }

    static $all_record_data = array();
    public static function all_record($parent_id=0, $level=0, $filter= array())
    {
        $arr = Menu::where('parent_id', $parent_id)->get();

        foreach ($arr as $value) {

            $new_data = new \stdClass;

            $new_data->id           = $value->id;
            $new_data->parent_id    = $value->parent_id;
            $new_data->title        = $value->title;
            $new_data->created_at   = $value->created_at;
            $new_data->updated_at   = $value->updated_at;
            $new_data->level        = $level;

            array_push(self::$all_record_data, $new_data);

            self::all_record($value->id, $level+1);
        }

        return (object) self::$all_record_data;
    }
}
