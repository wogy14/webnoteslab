<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use Validator;


class UserController extends BaseController {

  /**
   * Display the specified resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request) {
    return $this->sendResponse($request->user()->toArray(), 'User retrieved successfully.');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param User $user
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request) {
    $user = $request->user();
    $input = $request->all();
    $validator = Validator::make($input, [
      'email' => 'email'
    ]);

    if ($validator->fails()) {
      return $this->sendError('Validation Error.', $validator->errors());
    }

    if (!empty($input['email'])) {
      if ($input['email'] != $user->email && User::where('email','=',$input['email'])->count() != 0) {
        return $this->sendError('Validation Error.', ['email' => 'Email already exists.']);
      }
      $user->email = $input['email'];
    }

    if (!empty($input['name'])) {
      $user->name = $input['name'];
    }

    if (!empty($input['password'])) {
      $user->password = bcrypt($input['password']);
    }

    $user->save();

    return $this->sendResponse($user->toArray(), 'User updated.');
  }
}
