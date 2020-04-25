<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Note;

class Author extends Model {
    protected $fillable = [
        'user_id', 'note_id', 'is_creator'
    ];

    public function user() {
        return User::find($this->user_id);
    }

    public function note() {
        return Note::find($this->note_id);
    }
}
