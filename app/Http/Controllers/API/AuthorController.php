<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Note;
use App\User;
use App\Author;
use Validator;

class AuthorController extends BaseController {

    public function getNoteAuthors(Note $note) {
        $preparedAuthors = [];
        $authors = $note->authors()->orderBy('is_creator','desc')->get();

        foreach ($authors as $author) {
            $user = $author->user();
            $preparedAuthors[] = [
                'id' => $author->id,
                'user_id' => $author->user_id,
                'note_id' => $author->note_id,
                'name' => $user->name,
                'email' => $user->email,
                'is_creator' => $author->is_creator
            ];
        }

        return $this->sendResponse($preparedAuthors);
    }

    public function setAuthorByEmail(Request $request, Note $note) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::where('email', $input['email'])->first();
        if (!$user) {
            return $this->sendError('Validation Error.', ['email' => 'User with this email does not exists.']);
        }

        $isAuthor = Author::where('user_id', $user->id)->where('note_id', $note->id)->count() != 0;
        if ($isAuthor) {
            return $this->sendError('Validation Error.', ['note_id' => 'This user is already author of this note.']);
        }

        $author = new Author();
        $author->note_id = $note->id;
        $author->user_id = $user->id;
        $author->is_creator = false;
        $author->save();
        $user = $author->user();

        $preparedAuthor = [
            'id' => $author->id,
            'user_id' => $author->user_id,
            'note_id' => $author->note_id,
            'name' => $user->name,
            'email' => $user->email,
            'is_creator' => $author->is_creator
        ];

        return $this->sendResponse($preparedAuthor);
    }

    public function destroy(Request $request, Author $author) {
        $creator = Author::where('note_id', $author->note_id)->where('is_creator', true)->first();

        if ($creator->user_id != $request->user()->id) {
            return $this->sendError('Validation Error.', ['note_id' => 'Note with this author doesn`t belong you.']);
        }

        if ($author->is_creator) {
            return $this->sendError('Validation Error.', ['creator' => 'You can`t delete yourself from authors.']);
        }

        $author->delete();

        return $this->sendResponse($author->toArray(), 'Author deleted successfully.');
    }
}
