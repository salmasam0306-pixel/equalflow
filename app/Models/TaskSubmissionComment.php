<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskSubmissionComment extends Model
{
    protected $fillable = ['submission_id', 'user_id', 'comment'];

    public function submission()
    {
        // Specify the foreign key column name explicitly
        return $this->belongsTo(TaskSubmission::class, 'submission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}