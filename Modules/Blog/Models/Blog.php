<?php

namespace Modules\Blog\Models;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends BaseModel
{
    // use HasFactory;
    use HasFactory;
    use SoftDeletes;
  

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title', 'description', 'total_views' , 'author_id' , 'status','is_featured', 'tags'
    ];    

    public function author(){
        return $this->belongsTo('App\Models\User','author_id','id')->withTrashed();
    }

    protected static function booted()
    {
        static::deleting(function (Blog $blog) {
            // Ensure status is disabled when blog is soft-deleted
            if ($blog->status !== 0) {
                $blog->status = 0;
                $blog->saveQuietly();
            }
        });
    }

}
