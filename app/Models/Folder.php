<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'parent_id' ,'path'];

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
    public function descendants()
    {
        return $this->children()->with('descendants');
    }
    public function subfolders()
    {
        return $this->hasMany(Folder::class, 'parent_id', 'id');
    }

    public function getPath()
    {
        $path = [$this->name];

        $ancestor = $this->parent;

        while ($ancestor) {
            array_unshift($path, $ancestor->name);
            $ancestor = $ancestor->parent;
        }

        return implode('/', $path);
    }
}
