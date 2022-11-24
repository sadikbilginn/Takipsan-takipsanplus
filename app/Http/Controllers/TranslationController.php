<?php

namespace App\Http\Controllers;

use App\Locale;
use App\Translation;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Validator;

class TranslationController extends Controller
{
    private $path;
    private $disk;
    private $locales;

    public function __construct()
    {

        parent::__construct();
        $this->disk = new Filesystem;
        $this->path = $path = realpath(base_path('resources/lang'));
        $this->locales = Locale::all();
    }

    /**
     * Kaynaktan bir liste görüntüler.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $translation = Translation::all()->sortByDesc('id');

        return View('translation.index')->with('translation', $translation);
    }

    /**
     * Yeni bir kaynak oluşturmak için formu gösterir.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('translation.create');
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

            $group = Str::slug($request->get('group'));
            $key = Str::slug($request->get('key'),'_');

            $attribute = array(
                'group'     => 'Grup',
                'key'       => 'Key',
                'value'     => 'Value',
            );

            $rules = array(
                'group'             => 'required',
                'key'               => 'required|unique:translations,key,null,null,group,' . $group,
                'value'             => 'nullable',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $translation                 = new Translation;
            $translation->group          = $group;
            $translation->key            = $key;
            $translation->value          = json_encode($request->get('value'));
            $translation->save();

            $this->createFile($group);
            $keys = [$key => $request->get('value')];
            $this->fillKeys($group, $keys);

            session()->flash('flash_message', array('Başarılı!','Çeviri kaydedildi.', 'success'));
        }

        catch (\Exception $e){
            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->route('translation.index');
    }

    /**
     * Belirtilen kaynağı düzenlemek için formu gösterir.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $translation = Translation::find($id);

        return View('translation.edit')->with('translation', $translation);
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

            $group = Str::slug($request->get('group'));
            $key = Str::slug($request->get('key'),'_');

            $attribute = array(
                'group'     => 'Grup',
                'key'       => 'Key',
                'value'     => 'Value',
            );

            $rules = array(
                'group'             => 'required',
                'key'               => 'required|unique:translations,key,'.$id.',id,group,' . $group,
                'value'             => 'nullable',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attribute);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $translation                 = Translation::find($id);
            $this->removeKey($translation->group, $translation->key);

            $translation->group          = $group;
            $translation->key            = $key;
            $translation->value          = json_encode($request->get('value'));
            $translation->save();

            $this->createFile($group);
            $keys = [$key => $request->get('value')];
            $this->fillKeys($group, $keys);

            session()->flash('flash_message', array('Başarılı!','Çeviri güncellendi.', 'success'));
        }

        catch (\Exception $e){
            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->route('translation.index');
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
            $translation = Translation::find($id);
            $translation->delete();

            $this->removeKey($translation->group, $translation->key);

            session()->flash('flash_message', array('Başarılı!','Çeviri silindi.', 'success'));
        }

        catch (\Exception $e){

            session()->flash('flash_message', array('Başarısız!','Hata! Lütfen tekrar deneyiniz.', 'error'));
        }

        return redirect()->route('translation.index');
    }

    /**
     * Dosya oluşturma
     *
     * @param  string  $fileName
     */
    public function createFile($fileName)
    {
        foreach ($this->locales as $key => $locale) {
            $file = $this->path."/{$locale->path}/{$fileName}.php";
            if (! $this->disk->exists($file)) {
                file_put_contents($file, "<?php \n\n return[];");
            }
        }
    }

    /**
     * Dosya içeriğini doldurur.
     *
     * @param  string  $fileName
     * @param  array  $keys
     */
    public function fillKeys($fileName, array $keys)
    {
        $appends = [];

        foreach ($keys as $key => $values) {
            foreach ($values as $languageKey => $value) {
                $filePath = $this->path."/{$languageKey}/{$fileName}.php";

                Arr::set($appends[$filePath], $key, $value);
            }
        }

        foreach ($appends as $filePath => $values) {
            $fileContent = $this->getFileContent($filePath, true);

            $newContent = array_replace_recursive($fileContent, $values);

            $this->writeFile($filePath, $newContent);
        }
    }

    /**
     * Dosya'ya içerik yazar.
     *
     * @param  string  $filePath
     * @param  array  $translations
     */
    public function writeFile($filePath, array $translations)
    {
        $content = "<?php \n\nreturn [";

        $content .= $this->stringLineMaker($translations);

        $content .= "\n];";

        file_put_contents($filePath, $content);
    }

    /**
     * Dosya'ya içerik eklenirken marker ayarlarını düzenler.
     *
     * @param  array  $array
     * @param  string  $prepend
     */
    private function stringLineMaker($array, $prepend = '')
    {
        $output = '';

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->stringLineMaker($value, $prepend.'    ');

                $output .= "\n{$prepend}    '{$key}' => [{$value}\n{$prepend}    ],";
            } else {
                $first = substr($value, 0, 1);
                if($first == '['){
                    $output .= "\n{$prepend}    '{$key}' => {$value},";
                }else{
                    $value = str_replace('\"', '"', addslashes($value));
                    $output .= "\n{$prepend}    '{$key}' => '{$value}',";
                }
            }
        }

        return $output;
    }

    /**
     * Dosya içeriğinden veri siler.
     *
     * @param string $fileName
     * @param string $key
     */
    public function removeKey($fileName, $key)
    {
        foreach ($this->locales as $key2 => $locale) {
            $filePath = $this->path."/{$locale->path}/{$fileName}.php";

            $fileContent = $this->getFileContent($filePath);

            Arr::forget($fileContent, $key);

            $this->writeFile($filePath, $fileContent);
        }
    }

    /**
     * Dosya izinlerini tanımlar.
     *
     * @param string $filePath
     * @param boolean $createIfNotExists
     * @return array
     * @throws FileNotFoundException
     */
    public function getFileContent($filePath, $createIfNotExists = false)
    {
        if ($createIfNotExists && ! $this->disk->exists($filePath)) {
            if (! $this->disk->exists($directory = dirname($filePath))) {
                mkdir($directory, 0777, true);
            }

            file_put_contents($filePath, "<?php\n\nreturn [];");

            return [];
        }

        try {
            return (array) include $filePath;
        } catch (\ErrorException $e) {
            throw new FileNotFoundException('File not found: '.$filePath);
        }
    }
}
