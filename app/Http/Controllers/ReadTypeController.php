<?php

namespace App\Http\Controllers;

use App\ReadType;
use Validator;
use Illuminate\Http\Request;

class ReadTypeController extends Controller
{

    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $read_types = ReadType::all();

        return View('read_type.index')->with('read_types', $read_types);
    }

    /**
     * Yeni bir kaynak oluşturmak için formu gösterir.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('read_type.create');
    }

    /**
     * Yeni oluşturulan bir kaynağı database'e kayıt eder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{

            $attribute = array(
                'name'                  => 'Okuma Adı',
                'name_en'               => 'Okuma Adı',
                'reader'                => 'Reader',
                'reader_mode'           => 'Reader Mode',
                'estimated_population'  => 'Est. Population',
                'search_mode'           => 'Search Mode',
                'session'               => 'Session',
                'status'                => 'Durumu'
            );

            $rules = array(
                'name'                  => 'required',
                'name_en'               => 'required',
                'reader'                => 'required',
                'reader_mode'           => 'nullable',
                'estimated_population'  => 'required',
                'search_mode'           => 'nullable',
                'session'               => 'required',
                'status'                => 'nullable',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $read_type                          = new ReadType;
            $read_type->name                    = $request->get('name');
            $read_type->name_en                 = $request->get('name_en');
            $read_type->reader                  = $request->get('reader');
            $read_type->reader_mode             = $request->get('reader_mode');
            $read_type->estimated_population    = $request->get('estimated_population');
            $read_type->search_mode             = $request->get('search_mode');
            $read_type->session                 = $request->get('session');
            $read_type->string_set              = $request->get('string_set');
            $read_type->status                  = $request->has('status') ? true : false;
            $read_type->created_user_id         = auth()->user()->id;
            $read_type->save();

            session()->flash('flash_message', array('Başarılı!','Okuma Tipi kaydedildi.', 'success'));

        }

        catch (\Exception $e){
            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->route('read-type.index');
    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $read_type     = ReadType::find($id);

        return view('read_type.edit')->with('read_type', $read_type);
    }

    /**
     * Database üzerindeki belirtilen kaynağı günceller.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{

            $attribute = array(
                'name'                  => 'Okuma Adı',
                'name_en'               => 'Okuma Adı',
                'reader'                => 'Reader',
                'reader_mode'           => 'Reader Mode',
                'estimated_population'  => 'Est. Population',
                'search_mode'           => 'Search Mode',
                'session'               => 'Session',
                'common_power'          => 'Ortak Güç Kullan',
                'status'                => 'Durumu'
            );

            $rules = array(
                'name'                  => 'required',
                'name_en'               => 'required',
                'reader'                => 'required',
                'reader_mode'           => 'nullable',
                'estimated_population'  => 'required',
                'search_mode'           => 'nullable',
                'session'               => 'required',
                'common_power'          => 'nullable',
                'status'                => 'nullable',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $read_type                         = ReadType::find($id);
            $read_type->name                    = $request->get('name');
            $read_type->name_en                 = $request->get('name_en');
            $read_type->reader                  = $request->get('reader');
            $read_type->reader_mode             = $request->get('reader_mode');
            $read_type->estimated_population    = $request->get('estimated_population');
            $read_type->search_mode             = $request->get('search_mode');
            $read_type->session                 = $request->get('session');
            $read_type->string_set              = $request->get('string_set');
            $read_type->status                  = $request->has('status') ? true : false;
            $read_type->updated_user_id         = auth()->user()->id;
            $read_type->save();

            session()->flash('flash_message', array('Başarılı!','Okuma Tipi güncellendi.', 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->route('read-type.index');
    }

    /**
     * Belirtilen kaynağı database üzerinden kaldırır.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{

            $read_type                     = ReadType::find($id);
            $read_type->updated_user_id    = auth()->user()->id;
            $read_type->save();
            $read_type->delete();

            session()->flash('flash_message', array('Başarılı!','Okuma Tipi silindi.', 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->route('read-type.index');
    }

}
