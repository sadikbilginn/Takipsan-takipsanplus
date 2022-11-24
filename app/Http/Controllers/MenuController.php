<?php

namespace App\Http\Controllers;

use App\Menu;
use App\Role;
use Illuminate\Support\Facades\Cache;
use Validator;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $this->data['roles'] = Role::all();
        $this->data['menus'] = Menu::where('parent_id', 0)->with('children')->orderBy('sort')->get();
        $this->data['menu_all'] = Menu::all_record(0);

        return View('menu.index', $this->data);
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
                'parent_id'     => 'Üst Menü',
                'icon'          => 'İsim',
                'title'         => 'Menü Adı',
                'title_en'      => 'Menü Adı En',
                'link'          => 'Link',
                'roles'         => 'Role'
            );

            $rules = array(
                'parent_id'       => 'required',
                'icon'            => 'required',
                'title'           => 'required',
                'title_en'        => 'required',
                'link'            => 'nullable',
                'roles'           => 'nullable'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $menu                 = new Menu;
            $menu->parent_id      = $request->get('parent_id');
            $menu->icon           = $request->get('icon');
            $menu->title          = $request->get('title');
            $menu->title_en       = $request->get('title_en');
            $menu->uri            = $request->get('link');
            if($menu->save()){
                $roles = $request->get('roles');
                if(!empty($roles)){
                    $menu->roles()->attach($roles);
                }
            }

            Cache::forget('glb_menus');
            session()->flash('flash_message', array('Başarılı!','Menü kaydedildi.', 'success'));

        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));

        }

        return redirect()->route('menu.index');

    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['menus'] = Menu::where('parent_id', 0)->with('children')->orderBy('sort')->get();
        $this->data['menu'] = Menu::find($id);
        $this->data['menu_all'] = Menu::all_record(0);
        $this->data['roles'] = Role::all();

        $menuSelectRoles = [];
        if($this->data['menu']){
            foreach ($this->data['menu']->roles as $value)
            {
                $menuSelectRoles[] =  $value->id;
            }
            $this->data['menuSelectRoles'] = $menuSelectRoles;
        }

        return View('menu.edit', $this->data);
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
                'parent_id'     => 'Üst Menü',
                'icon'          => 'İsim',
                'title'         => 'Menü Adı',
                'title_en'      => 'Menü Adı En',
                'link'          => 'Link',
                'roles'         => 'Role'
            );

            $rules = array(
                'parent_id'       => 'required',
                'icon'            => 'required',
                'title'           => 'required',
                'title_en'        => 'required',
                'link'            => 'nullable',
                'roles'           => 'nullable'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->route('menu.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }

            $menu                 = Menu::find($id);
            $menu->parent_id      = $request->get('parent_id');
            $menu->icon           = $request->get('icon');
            $menu->title          = $request->get('title');
            $menu->title_en       = $request->get('title_en');
            $menu->uri            = $request->get('link');
            if($menu->save()){
                $roles = $request->get('roles');
                if(empty($roles)){
                    $menu->roles()->detach();
                }else{
                    $menu->roles()->sync($roles);
                }
            }

            Cache::forget('glb_menus');
            session()->flash('flash_message', array('Başarılı!','Menü güncellendi.', 'success'));

        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));

        }

        return redirect()->route('menu.index');
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
            $menu = Menu::find($id);
            $menu->delete();

            Cache::forget('glb_menus');
            session()->flash('flash_message', array('Başarılı!','Menü silindi.', 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));

        }

        return redirect()->route('menu.index');
    }

    /**
     * Menü düzeninde sıralama ve child düzeninin ayarlar.
     *
     */
    public function sorting(Request $request)
    {
        $data = json_decode($request->get('data'));
        $readbleArray = $this->menuSortingParseJsonArray($data);

        foreach($readbleArray as $key => $value){
            $menu                 = Menu::find($value['id']);
            $menu->sort           = $key;
            $menu->parent_id      = $value['parent_id'];
            $menu->save();
        }

        Cache::forget('glb_menus');
    }

}
