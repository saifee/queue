<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [
            [
                'id' => 1,
                'code' => 'gb',
                'name' => 'English',
                'display' => 'US English Female',
                'token_translation' =>  "Token",
                'please_proceed_to_translation' =>  "Please Proceed to",
            ],
            [
                'id' => 2,
                'code' => 'fr',
                'name' => 'French',
                'display' => 'French Female',
                'token_translation' =>  "Jeton",
                'please_proceed_to_translation' =>  "Veuillez passer à",
            ],
            [
                'id' => 3,
                'code' => 'in',
                'name' => 'Hindi',
                'display' => 'Hindi Female',
                'token_translation' =>  " टोकन",
                'please_proceed_to_translation' =>  " कृपया आगे बढ़ें",
            ],
            [
                'id' => 4,
                'code' => 'sa',
                'name' => 'Arabic',
                'display' => 'Arabic Male',
                'token_translation' =>  " رمز",
                'please_proceed_to_translation' =>  "يرجى المتابعة إلى",
            ],
            [
                'id' => 5,
                'code' => 'es',
                'name' => 'Spanish',
                'display' => 'Spanish Female',
                'token_translation' =>  " simbólico",
                'please_proceed_to_translation' =>  "por favor proceda a",
            ],
            [
                'id' => 6,
                'code' => 'pt',
                'name' => 'Portuguese',
                'display' => 'Portuguese Female',
                'token_translation' =>  " símbolo",
                'please_proceed_to_translation' =>  "Por favor, prossiga para",
            ],
            [
                'id' => 7,
                'code' => 'it',
                'name' => 'Italian',
                'display' => 'Italian Female',
                'token_translation' =>  " gettone",
                'please_proceed_to_translation' =>  "si prega di procedere a",
            ],
            [
                'id' => 8,
                'code' => 'id',
                'name' => 'Indonesian',
                'display' => 'Indonesian Female',
                'token_translation' =>  " token",
                'please_proceed_to_translation' =>  "silakan lanjutkan ke",
            ],
            [
                'id' => 9,
                'code' => 'de',
                'name' => 'German',
                'display' => 'German Female',
                'token_translation' =>  " token",
                'please_proceed_to_translation' =>  "Bitte fahren Sie mit fort",
            ]
          
        ];
        foreach ($languages as $language) {
            $data =  Language::find($language['id']);
            if ($data) {
                $data->code = $language['code'];
                $data->name = $language['name'];
                $data->display = $language['display'];
                $data->token_translation = $language['token_translation'];
                $data->please_proceed_to_translation = $language['please_proceed_to_translation'];
                $data->save();
            } else Language::create($language);

        }
    }
}
