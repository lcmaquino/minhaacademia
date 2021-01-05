<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    /**
     * Get the users with this role.
     *
     * @return Model
     */
    public function users(){
        return $this->hasMany('App\User', 'role', 'id');
    }

    /**
     * Get the role id with $name.
     *
     * @param string $name
     * @return integer
     */
    static public function roleId($name){
        $role = Role::where(['name' => strtolower($name)])->first();
        return $role->count() > 0 ? $role->id : null;
    }

    /**
     * Get the role name with $id.
     *
     * @param integer $name
     * @return string
     */
    static public function roleName($id){
        $role = Role::find($id);
        return $role != null ? $role->name : null;
    }
}