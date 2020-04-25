<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Note;
use App\Author;
use Validator;


class NoteController extends BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $authors = Author::where("user_id", $request->user()->id)->get();
        $notes = [];
        foreach ($authors as $author) {
            $notes[] = $author->note();
        }

        return $this->sendResponse($notes, 'Notes retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required',
            'text' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $note = Note::create($input);

        $author = new Author();
        $author->note_id = $note->id;
        $author->user_id = $request->user()->id;
        $author->is_creator = true;
        $author->save();


        return $this->sendResponse($note->toArray(), 'Note created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $note = Note::find($id);

        if (is_null($note)) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse($note->toArray(), 'Note retrieved successfully.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Note $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Note $note) {
        $input = $request->all();

        if (!empty($input['text'])) {
            $note->text = $input['text'];
        }
        if (!empty($input['title'])) {
            $note->title = $input['title'];
        }

        $note->save();

        return $this->sendResponse($note->toArray(), 'Note updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Note $note
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Note $note) {
        $note->delete();

        return $this->sendResponse($note->toArray(), 'Note deleted successfully.');
    }
}
