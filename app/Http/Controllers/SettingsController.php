<?php

namespace App\Http\Controllers;

use App\Setting;
use Validator,Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Cache\Factory;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = [];
        foreach(config('settings.group_key') as $key => $value){
            $settings[$key] = Setting::all()->where('group_key', '=', $key)->sortBy('sort');
        }
        $this->data['settings'] = $settings;

        return view('settings.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('settings.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $attribute = array(
                'group_key'     => 'Grup Başlığı',
                'required'      => 'Zorunlu Alan?',
                'area_type'     => 'Alan Tipi',
                'title'         => 'Başlık',
                'description'   => 'Açıklama',
                'locale'        => 'Dil Desteği',
                'key'           => 'Key Değeri',
                'value'         => 'Value Değeri',
                'sort'          => 'Sıralama',
            );

            $rules = array(
                'group_key'         => 'required',
                'required'          => 'required|numeric',
                'area_type'         => 'required',
                'title'             => 'required',
                'description'       => 'nullable',
                'locale'            => 'required|numeric',
                'key'               => 'required|unique:settings',
                'value'             => 'nullable',
                'sort'              => 'required|numeric|max:127'
            );
            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $setting = new Setting;
            $setting->group_key      = $request->get('group_key');
            $setting->required       = $request->get('required');
            $setting->area_type      = $request->get('area_type');
            $setting->title          = $request->get('title');
            $setting->description    = $request->get('description');
            $setting->locale         = $request->get('locale');
            $setting->key            = $request->get('key');
            $setting->value          = (is_array($request->get('value')) ? json_encode($request->get('value')) : $request->get('value'));
            $setting->sort           = $request->get('sort');
            $setting->save();

            session()->flash('flash_message', array('Başarılı!','Ayarlar kaydedildi.', 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));

        }

        return redirect()->route('settings.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Factory $cache)
    {
        $data = $request->all();
        $attachments = $request->file('FILE');

        if($attachments) {
            foreach ($attachments as $key => $file){

                if(is_array($file)){

                    foreach ($file as $key2 => $files){

                        $ext = config('settings.file_type_image');
                        $file_ex = $files->getClientOriginalExtension();

                        if(in_array($file_ex, $ext)){

                            $validator = Validator::make($file, [
                                $key      => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                            ]);

                            if ($validator->fails()) {

                                session()->flash('flash_message', array('Başarısız!','Hata! Yüklediğiniz dosya uzantısı jpeg,png,jpg,gif bunlardan biri olmalıdır.', 'error'));
                                return redirect()->route('settings.index');

                            }

                            $file_ex = $files->getClientOriginalExtension();

                            if($file_ex == 'svg'){

                                $file_name = md5($key.$key2).'-svg.'.$file_ex;
                                Storage::disk('upload')->makeDirectory('images');
                                $destination = 'upload/images';
                                $files->move($destination, $file_name);

                            }else{
                                $file_name = md5($key.$key2).'-img.'.$file_ex;
                                Storage::disk('upload')->makeDirectory('images');
                                $image = Image::make($files->getRealPath());

                                $file_width = $request->get($key.'width') == '' ? $image->width() : $request->get($key.'width');
                                $file_height = $request->get($key.'height') == '' ? $image->height() : $request->get($key.'width');

                                $image->resize($file_width, $file_height, function ($constraint) {
                                    $constraint->aspectRatio();
                                });
                                $image->save('upload/images/'. $file_name);
                            }

                            $data[$key][$key2] = $file_name;

                        }else{

                            $validator = Validator::make($file, [
                                $key      => 'mimes:doc,docx,xls,xlsx,pdf|max:2048'
                            ]);

                            if ($validator->fails()) {

                                session()->flash('flash_message', array('Başarısız!','Hata! Yüklediğiniz dosya uzantısı doc,docx,xls,xlsx,pdf bunlardan biri olmalıdır.', 'error'));
                                return redirect()->route('settings.index');

                            }

                            $file_name = md5($key.$key2).'-file.'.$file_ex;
                            Storage::disk('upload')->makeDirectory('files');
                            $destination = 'upload/files';
                            $files->move($destination, $file_name);

                            $data[$key][$key2] = $file_name;

                        }

                    }

                }else{

                    $ext = config('settings.file_type_image');
                    $file_ex = $file->getClientOriginalExtension();
                    if(in_array($file_ex, $ext)){

                        $validator = Validator::make($attachments, [
                            $key      => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                        ]);

                        if ($validator->fails()) {

                            session()->flash('flash_message', array('Başarısız!','Hata! Yüklediğiniz dosya uzantısı jpeg,png,jpg,gif bunlardan biri olmalıdır.', 'error'));
                            return redirect()->route('settings.index');

                        }

                        $file_ex = $file->getClientOriginalExtension();

                        if($file_ex == 'svg'){

                            $file_name = $key.'-svg.'.$file_ex;
                            Storage::disk('upload')->makeDirectory('images');
                            $destination = 'upload/images';

                            $file->move($destination, $file_name);

                        }else{
                            $file_name = $key.'-img.'.$file_ex;

                            Storage::disk('upload')->makeDirectory('images');
                            $image = Image::make($file->getRealPath());

                            $file_width = $request->get($key.'width') == '' ? $image->width() : $request->get($key.'width');
                            $file_height = $request->get($key.'height') == '' ? $image->height() : $request->get($key.'width');

                            $image->resize($file_width, $file_height, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                            $image->save('upload/images/'. $file_name);
                        }

                        $data[$key] = $file_name;

                    }else{

                        $validator = Validator::make($attachments, [
                            $key      => 'mimes:doc,docx,xls,xlsx,pdf|max:2048'
                        ]);

                        if ($validator->fails()) {

                            session()->flash('flash_message', array('Başarısız!','Hata! Yüklediğiniz dosya uzantısı doc,docx,xls,xlsx,pdf bunlardan biri olmalıdır.', 'error'));
                            return redirect()->route('settings.index');

                        }

                        $file_name = $key.'-file.'.$file_ex;
                        Storage::disk('upload')->makeDirectory('files');
                        $destination = 'upload/files';
                        $file->move($destination, $file_name);

                        $data[$key] = $file_name;

                    }

                }

            }

        }

        try{

            unset($data['_token']);
            unset($data['FILE']);

            foreach ($data as $key => $value){

                $new_data = (is_array($value) ? json_encode($value) : $value);

                Setting::where('key', $key)->update(array('value'=> $new_data));
            }

            $cache->forget('db_settings');

            session()->flash('flash_message', array('Başarılı!','Ayarlar kaydedildi.', 'success'));

        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));

        }

        return redirect()->route('settings.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $setting = Setting::find($id);
            $setting->delete();

            session()->flash('flash_message', array('Başarılı!','Bilgielriniz başarı ile silindi.', 'success'));

        }
        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->route('settings.index');
    }
}
