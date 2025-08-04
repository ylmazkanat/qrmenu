<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    protected $baseUrl = 'https://translate.googleapis.com/translate_a/single';
    
    public function translate($text, $targetLang, $sourceLang = 'tr')
    {
        if (empty($text)) {
            return $text;
        }

        // Cache key oluştur
        $cacheKey = "translation_{$sourceLang}_{$targetLang}_" . md5($text);
        
        // Cache'den kontrol et
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::get($this->baseUrl, [
                'client' => 'gtx',
                'sl' => $sourceLang,
                'tl' => $targetLang,
                'dt' => 't',
                'q' => $text
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $translatedText = '';
                
                // Google Translate API response formatı: [[[translated_text, original_text, source_lang, target_lang], ...]]
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($data[0] as $translation) {
                        if (isset($translation[0])) {
                            $translatedText .= $translation[0];
                        }
                    }
                }
                
                // Sonucu cache'e kaydet (1 gün)
                Cache::put($cacheKey, $translatedText, 86400);
                
                return $translatedText ?: $text;
            }
        } catch (\Exception $e) {
            // Hata durumunda orijinal metni döndür
            return $text;
        }

        return $text;
    }

    public function translateArray($data, $targetLang, $sourceLang = 'tr')
    {
        if (!is_array($data)) {
            return $this->translate($data, $targetLang, $sourceLang);
        }

        $translated = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $translated[$key] = $this->translateArray($value, $targetLang, $sourceLang);
            } else {
                $translated[$key] = $this->translate($value, $targetLang, $sourceLang);
            }
        }

        return $translated;
    }

    public function getSupportedLanguages()
    {
        return [
            'tr' => 'Türkçe',
            'en' => 'English',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
            'it' => 'Italiano',
            'ru' => 'Русский',
            'ar' => 'العربية',
            'zh' => '中文',
            'ja' => '日本語',
            'ko' => '한국어',
            'pt' => 'Português',
            'nl' => 'Nederlands',
            'pl' => 'Polski',
            'sv' => 'Svenska',
            'da' => 'Dansk',
            'no' => 'Norsk',
            'fi' => 'Suomi',
            'hu' => 'Magyar',
            'cs' => 'Čeština',
            'sk' => 'Slovenčina',
            'ro' => 'Română',
            'bg' => 'Български',
            'hr' => 'Hrvatski',
            'sl' => 'Slovenščina',
            'et' => 'Eesti',
            'lv' => 'Latviešu',
            'lt' => 'Lietuvių',
            'mt' => 'Malti',
            'el' => 'Ελληνικά',
            'he' => 'עברית',
            'th' => 'ไทย',
            'vi' => 'Tiếng Việt',
            'id' => 'Bahasa Indonesia',
            'ms' => 'Bahasa Melayu',
            'hi' => 'हिन्दी',
            'bn' => 'বাংলা',
            'ur' => 'اردو',
            'fa' => 'فارسی',
            'ku' => 'Kurdî',
            'az' => 'Azərbaycan',
            'ka' => 'ქართული',
            'hy' => 'Հայերեն',
            'uk' => 'Українська',
            'be' => 'Беларуская',
            'kk' => 'Қазақ',
            'ky' => 'Кыргызча',
            'uz' => 'O\'zbek',
            'tg' => 'Тоҷикӣ',
            'mn' => 'Монгол',
            'ne' => 'नेपाली',
            'si' => 'සිංහල',
            'my' => 'မြန်မာ',
            'km' => 'ខ្មែរ',
            'lo' => 'ລາວ',
            'gl' => 'Galego',
            'eu' => 'Euskara',
            'ca' => 'Català',
            'cy' => 'Cymraeg',
            'ga' => 'Gaeilge',
            'is' => 'Íslenska',
            'mk' => 'Македонски',
            'sq' => 'Shqip',
            'bs' => 'Bosanski',
            'me' => 'Crnogorski',
            'sr' => 'Српски',
            'af' => 'Afrikaans',
            'sw' => 'Kiswahili',
            'yo' => 'Yorùbá',
            'ig' => 'Igbo',
            'ha' => 'Hausa',
            'zu' => 'isiZulu',
            'xh' => 'isiXhosa',
            'st' => 'Sesotho',
            'sn' => 'chiShona',
            'ny' => 'Chichewa',
            'rw' => 'Kinyarwanda',
            'mg' => 'Malagasy',
            'so' => 'Soomaali',
            'am' => 'አማርኛ',
            'ti' => 'ትግርኛ',
            'or' => 'ଓଡ଼ିଆ',
            'te' => 'తెలుగు',
            'ta' => 'தமிழ்',
            'ml' => 'മലയാളം',
            'kn' => 'ಕನ್ನಡ',
            'gu' => 'ગુજરાતી',
            'pa' => 'ਪੰਜਾਬੀ',
            'as' => 'অসমীয়া',
            'mni' => 'মৈতৈলোন্',
            'brx' => 'बड़ो',
            'sat' => 'ᱥᱟᱱᱛᱟᱲᱤ',
            'ks' => 'کٲشُر',
            'doi' => 'डोगरी',
            'sa' => 'संस्कृतम्',
            'bo' => 'བོད་ཡིག',
            'dz' => 'ཇོང་ཁ',
            'my' => 'မြန်မာ',
            'ka' => 'ქართული',
            'hy' => 'Հայերեն',
            'az' => 'Azərbaycan',
            'tr' => 'Türkçe',
        ];
    }
} 