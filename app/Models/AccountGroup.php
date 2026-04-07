<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
    protected $fillable = ['name', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(AccountGroup::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(AccountGroup::class, 'parent_id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'account_group_id');
    }
}
