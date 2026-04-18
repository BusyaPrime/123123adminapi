<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Companies\Models\CompanyReview;
use App\Domain\Companies\Jobs\CompanyReviewJob;
use App\Domain\Companies\Resources\CompanyReviewResource;

class FrontendController extends Controller
{
    public function websiteView(Request $request)
    {
        $settings = $this->getSettings();
        $en = $this->getTranslationContent('en');
        $ru = $this->getTranslationContent('ru');
        $uz = $this->getTranslationContent('uz');

        return view(
            'admin.frontend.website',
            [
                'settings' => $settings,
                'languages' => [
                    'ru' => $ru,
                    'uz' => $uz,
                    'en' => $en,
                ]
            ]
        );
    }

    public function settings(Request $request){
        try {
            $settings = $this->getSettings();
            $lang = [
                'ru' => $this->getTranslationContent('ru'),
                'uz' => $this->getTranslationContent('uz'),
                'en' => $this->getTranslationContent('en'),
            ];

            foreach($settings as $key => $setting){
                if (!is_array($setting)) {
                    continue;
                }

                if (isset($lang['ru'][$key], $lang['uz'][$key], $lang['en'][$key])) {
                    $settings[$key]['translations']['ru'] = $lang['ru'][$key];
                    $settings[$key]['translations']['uz'] = $lang['uz'][$key];
                    $settings[$key]['translations']['en'] = $lang['en'][$key];
                }
            }

            $metaInformations = [];
            foreach (['ru', 'uz', 'en'] as $locale) {
                if (isset($lang[$locale]['metaInformations'])) {
                    $metaInformations[$locale] = $lang[$locale]['metaInformations'];
                }
            }

            if (!empty($metaInformations)) {
                $settings['metaInformations'] = $metaInformations;
            }

            return response()->json($settings, 200);
        } catch (\Throwable $th) {
            report($th);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function settingsByLang(Request $request)
    {
        $lang = $request->get('lang', 'ru');
        try {
            $settings = $this->getSettings();
            
            // foreach ($settings as $key => $setting) {
            //     if (isset($lang['ru'][$key]) && isset($lang['uz'][$key]) || isset($lang['en'][$key])) {
            //         $settings[$key]['translations']['ru'] = $lang['ru'][$key];
            //         $settings[$key]['translations']['uz'] = $lang['uz'][$key];
            //         $settings[$key]['translations']['en'] = $lang['en'][$key];
            //     }
            // }
            // $settings['metaInformations']['ru'] = $lang['ru']['metaInformations'];
            // $settings['metaInformations']['uz'] = $lang['uz']['metaInformations'];
            // $settings['metaInformations']['en'] = $lang['en']['metaInformations'];
            
            return response()->json($this->getTranslationContent($lang), 200);
        } catch (\Throwable $th) {
            report($th);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }


    public function websiteUpdate(Request $request)
    {
        return $this->websiteView($request);
    }

    public function updateFrontendData(Request $request)
    {
        try {
            $res = $this->update($request) ?? 'Данные сохранены';
            return response()->json(['message' => $res], 200);
        } catch (\Throwable $th) {
            report($th);
            return response()->json(['message' => 'Ошибка при сохранении данных'], 500);
        }
    }

    public function update(Request $request)
    {
        $language = $request->input('language');

        $contentName = $request->input('content-name');
        $langContent = $this->getTranslationContent($language);
        $settings = $this->getSettings();
        $input = $request->all();

        if (!isset($langContent[$contentName]))
            return "Не найден контент с именем: $contentName";
        if (isset($langContent[$contentName]) && isset($input['lang']))
            $this->editJson($langContent[$contentName], $input['lang'], $contentName);
        if (isset($settings[$contentName]) && isset($input['settings']))
            $this->editJson($settings[$contentName], $input['settings'], $contentName);

        $updatedTr = $this->updateTranslation($language, $langContent);
        $updatedSettings = $this->updateSettings($settings);

        if (!$updatedTr || !$updatedSettings)
            return 'Не удалось записать данные в файл';
    }

    private function editJson(&$json, $input, $contentName = null)
    {
        foreach($input as $mainKey => $val){
            if(!is_array($val)){
                if(array_key_exists($mainKey, $json)){
                    if(!is_file($val)){
                        $json[$mainKey] = $val;
                    }
                    else {
                        $lastFile = isset($json[$mainKey]['filename']) ? $json[$mainKey]['filename'] : '';
                        $json[$mainKey] = $this->saveFile($val, $contentName, $lastFile);
                    }
                }
            } else {
                $data = [];
                foreach ($val as $index => $value) {
                    foreach ($value as $key => $subValue) {
                        if(!is_array($subValue)){
                            $def = $key;
                            if($key == 'default' && isset($subValue)){
                                if($mainKey == 'gallery') $def = 'image';
                                else if($mainKey == 'icons' || $mainKey == 'partners') $def = 'icon';
                                else if($mainKey == 'contents') $def = 'background';
                            }
                            if(!is_file($subValue)){
                                if($key == 'default' && ($def == 'image' || $def == 'icon' || $def == 'background')){
                                    $data[$index][$def] = json_decode($subValue, true);
                                } else {
                                    $data[$index][$def] = $subValue;
                                }
                            } else {
                                $lastFile = isset($json[$mainKey][$index][$key]) ? $json[$mainKey][$index][$key]['filename'] : '';
                                $data[$index][$def] = $this->saveFile($subValue, $contentName."/$mainKey/", $subValue);
                            }
                        } else {
                            $subItems = [];
                            foreach ($subValue as $sInd => $sV) {
                                foreach ($sV as $sKey => $sVal) {
                                    $_k = $sKey;
                                    if($sKey == 'default'){
                                        if ($key == 'gallery')
                                            $_k = 'image';
                                        else if ($key == 'icons' || $key == 'partners')
                                            $_k = 'icon';
                                        else if ($key == 'contents')
                                            $_k = 'background';
                                    }
                                    if(!is_file($sVal)) {
                                        if($sKey == 'default' && ($_k == 'image' || $_k == 'icon' || $_k == 'background')){
                                            $subItems[$sInd][$_k] = json_decode($sVal, true);
                                        } else {
                                            $subItems[$sInd][$_k] = $sVal;
                                        }
                                    }
                                    else {
                                        $lastFile = isset($json[$mainKey][$index][$key][$sInd][$sKey]) ? $json[$mainKey][$index][$key][$sInd][$sKey]['filename'] : '';
                                        $subItems[$sInd][$_k] = $this->saveFile($sVal, $contentName . "/$mainKey/$key", $lastFile);
                                    }
                                }
                            }
                            $data[$index][$key] = array_values($subItems);
                        }
                    }
                }
                $json[$mainKey] = array_values($data);
            }
        }
    }
    private function saveFile($inputFile, $contentName, $lastFile = '')
    {
        $fileName = date('Y-m-d_H:i:s') . '_' . \Str::random(10) . '.' . $inputFile->getClientOriginalExtension();
        $mime = $inputFile->getClientMimeType();
        if($mime != 'image/jpg' && $mime != 'image/jpeg' && $mime != 'image/png' && $mime != 'image/gif' && $mime != 'video/mp4' && $mime != 'image/svg+xml') return null;
        
        $dir = "uploads/frontend/$contentName";
        $fullDir = public_path($dir);
        
        if (!\Storage::exists($fullDir)) {
            \Storage::makeDirectory($fullDir);
        }

        if (\File::exists("$fullDir/$lastFile")) {
            $res = \File::delete("$fullDir/$lastFile");
        }

        $file = $inputFile->move($fullDir, $fileName);
        return [
            'filename' => $file->getFileName(),
            'path' => $dir
        ];
    }

    private function getSettings()
    {
        $path = $this->getSettingsPath();
        if (!file_exists($path)) {
            return [];
        }

        $decoded = json_decode(file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function getSettingsPath()
    {
        return \Storage::disk('public')->path('frontend/settings.json');
    }
    private function getTranslateFilePath($lang)
    {
        return \Storage::disk('public')->path("frontend/languages/$lang.json");
    }
    private function getTranslationContent($lang)
    {
        $path = $this->getTranslateFilePath($lang);
        if (!file_exists($path)) {
            return [];
        }

        $decoded = json_decode(file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }
    private function updateTranslation($lang, $data)
    {
        $path = $this->getTranslateFilePath($lang);
        return file_exists($path) ? file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE)) : null;
    }
    private function updateSettings($data)
    {
        $path = $this->getSettingsPath();
        return file_exists($path) ? file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE)) : null;
    }

    public function appReviewsView(){
        $reviews = CompanyReview::orderBy('created_at', 'DESC')->paginate(15);
        return view('admin.app-reviews.index',[
            'reviews' => $reviews
        ]);
    }

    public function appReviewForm(CompanyReview $review = null)
    {
        return view('admin.app-reviews.form', [
            'review' => $review
        ]);
    }
    
    public function showAppReview(CompanyReview $review)
    {
        return view('admin.app-reviews.show', [
            'review' => $review
        ]);
    }
    
    public function storeAppReview(Request $request, CompanyReview $review = null){
        try {
            $this->dispatchSync(new CompanyReviewJob($request, $review));
            return redirect()->route('admin.frontend.app-reviews')->with('success', trans('admin.store_success'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('danger', trans('admin.store_failed'));
        }
    }
    
    public function deleteAppReview(CompanyReview $review){
        try {
            if($review->logo) $review->deleteImage($review->logo);
            $review->delete();
            return redirect()->route('admin.frontend.app-reviews')->with('success', trans('admin.delete_success'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('danger', trans('admin.store_failed'));
        }
    }
    
    public function reviewsResource(){
        try {
            $reviews = CompanyReview::published()->orderBy('created_at', 'DESC')->limit(15)->get();
            return CompanyReviewResource::collection($reviews);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error getting reviews'], 500);
        }
    }

    public function offer()
    {
        return view('admin.frontend.offer');
    }

    public function storeOffer(Request $request)
    {
        try {
            $fileRu = $request->file('file-ru');
            $fileUz = $request->file('file-uz');

            if(!is_null($fileRu)) {
                if($fileRu->getClientMimeType() !== 'application/pdf') {
                    return redirect()->back()->with('danger', trans('Тип файла должен быть PDF'));
                }

                $fileRu->storeAs('offers', 'offer-ru.pdf');
            }

            if (!is_null($fileUz)) {
                if ($fileUz->getClientMimeType() !== 'application/pdf') {
                    return redirect()->back()->with('danger', trans('Тип файла должен быть PDF'));
                }

                $fileUz->storeAs('offers', 'offer-uz.pdf');
            }
            
            return redirect()->route('admin.frontend.offer')->with('success', trans('admin.store_success'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('danger', trans('admin.store_failed'));
        }
    }

}
