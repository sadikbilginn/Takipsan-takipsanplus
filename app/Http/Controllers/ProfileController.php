<?php

namespace App\Http\Controllers;

use App\User;
use Validator, Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = User::find(auth()->user()->id);
        
        return view('user.show')->with('user', $user);
    }

    public function update(Request $request, $id)
    {

        try{
            $attribute = array(
                'username'              => 'Kullanıcı Adı',
                'name'                  => 'Ad Soyad',
                'email'                 => 'E-Mail',
               // 'password'              => 'Şifre',
               // 'password_confirmation' => 'Şifre Tekrar'
            );

            $rules = array(
                'username'              => 'required|unique:users,username,'.$id,
                'name'                  => 'required|string|max:255',
                'email'                 => 'required|string|email|max:150',
             //   'password'              => 'nullable|string|min:8|confirmed',
             //   'password_confirmation' => 'nullable|string|min:8'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $user                     = User::find($id);
            $user->username           = $request->get('username');
            $user->name               = $request->get('name');
            $user->email              = $request->get('email');
            /*
            if($request->get('password') != ''){
                $user->password           = bcrypt($request->get('password'));
            }
            */
            $user->save();


            session()->flash('flash_message', array('Başarılı!','Profil güncellendi.', 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));

        }

        return redirect()->route('profile.show');
    }

    public function edit_password()
    {
        $user = User::find(auth()->user()->id);
        
        return view('user.edit_password')->with('user', $user);
    }
    
    public function update_password(Request $request, $id)
    {
        $user = User::find(auth()->user()->id);

        try{
            $attribute = array(
                'old_password'          => trans('portal.old_password'),
                'password'              => trans('portal.new_password'),
                'password_confirmation' => trans('portal.password_confirmation')
            );

            $rules = array(
                'old_password'              => 'required',
                'password'                  => 'min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation'     => 'min:6|'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = User::find($id);
         
            // mevcut şifresini doğru girip girmediğini kontrol etmek için adamın formda girdiği old_password ile login olup olamadığını kontrol ediyoruz.
            if (Auth::attempt(['username' => $user->username, 'password' => $request->get('old_password')])) {

                $user->password = bcrypt($request->get('password'));
                if($user->save()){
                    session()->flash('flash_message', array(trans('portal.successful'), trans('portal.updated_password'), 'success'));
                    return redirect()->route('home');
                }
                else{
                    session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
                    return redirect()->back()->withInput();
                }
            }
            //Mevcut şifresini yanlış girdiği için şifreyi değiştiremez.
            else
            {
                session()->flash('flash_message', array(trans('portal.failed'), trans('portal.wrong_password'), 'error'));
                return redirect()->back()->withInput();
            }
        }

        catch (\Exception $e){

            session()->flash('flash_message', array(trans('portal.failed'), trans('portal.error_text'), 'error'));
            return redirect()->back()->withInput();
        }
    }
}
