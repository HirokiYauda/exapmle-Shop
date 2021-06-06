<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pref;
use App\Rules\ZipcodeRule;
use Auth;
use DB;

class MypageController extends Controller
{
    /**
     * User profile edit page
     *
     * @return View
     */
    public function edit()
    {
        $prefs = Pref::all();
        $user = Auth::user();
        return view('mypage.edit', compact('prefs', 'user'));
    }

    /**
     * User profile Address change page
     *
     * @return View
     */
    public function changeAddress()
    {
        // orderページからのアクセス以外は、TOPリダイレクト
        $refer = url()->previous();
        if (strpos($refer, 'order') === false) {
            return redirect()->route('top');
        }

        $prefs = Pref::all();
        $user = Auth::user();
        
        return view('mypage.change_address', compact('prefs', 'user'));
    }

    /**
     * User profile full update
     *
     * @return Redirect
     */
    public function fullUpdate(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id.',id'],
            'zip' => ['required', new ZipcodeRule],
            'pref_id' => ['required', 'integer'],
            'address1' => ['required', 'string', 'max:200'],
            'address2' => ['required', 'string', 'max:200'],
        ]);

        $title = "アカウント";
        $message = $this->update($user, $validatedData, $title);
        return redirect()->route('mypage_edit')->with('update_message', $message);
    }

    /**
     * User profile address update
     *
     * @return Redirect
     */
    public function addressUpdate(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'zip' => ['required', new ZipcodeRule],
            'pref_id' => ['required', 'integer'],
            'address1' => ['required', 'string', 'max:200'],
            'address2' => ['required', 'string', 'max:200'],
        ]);

        $title = "住所";
        $message = $this->update($user, $validatedData, $title);
        return redirect()->route('order')->with('update_message', $message);
    }

    /**
     * Common Update process
     *
     * @return String
     */
    public function update($user, $validatedData, $title)
    {
        // ハイフンなし
        $validatedData['zip'] = str_replace("-", "", $validatedData['zip']);
        
        $res = DB::transaction(function () use ($validatedData, $user) {
            return $user->fill($validatedData)->save();
        });

        // 更新の結果によってメッセージを変更
        $message = "${title}の更新に失敗しました。お手数ですが、もう一度やり直してください。";
        if (!empty($res)) {
            $message = "${title}を更新しました。";
        }

        return $message;
    }
}
