<?php

namespace Database\Factories;

use App\Models\Pharmacy;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\odel=Pharmacy>
 */
class PharmacyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model =Pharmacy::class;

    public function definition(): array
    {
        return [
     
          'name'=> fake()->randomElement([
            'صيدلية العزبي',
            'صيدلية سيف',
            'صيدلية رشدي',
            'صيدلية دلمار وعطا الله',
            'صيدلية دوائي',
            'صيدلية فارمسي 1',
            'صيدلية دواء',
            'صيدلية روكسي',
            'صيدلية ابن سينا',
            'صيدلية النهدي',
            'صيدلية وايت كوت',
            'صيدلية جراند',
            'صيدلية الشروق',
            'صيدلية الفؤاد',
            'صيدلية الحكمة',
            'صيدلية الشفا',
          ]),
          'license_id'=>fake()->unique()->random_int(700,1000),
          'phone'=>fake()->numerify('010########'),
          'location' => fake()->randomElement([
            'القاهرة - مدينة نصر',
            'القاهرة - مصر الجديدة',
            'القاهرة - التجمع الخامس',
            'القاهرة - المعادي',
            'القاهرة - شبرا',
            'الجيزة - المهندسين',
            'الجيزة - الدقي',
            'الجيزة - الهرم',
            'الإسكندرية - سيدي جابر',
            'الإسكندرية - سموحة',
            'الإسكندرية - محرم بك',
            'المنصورة - المشاية',
            'الزقازيق - القومية',
            'طنطا - سيجر',
            'أسيوط - شارع الجمهورية',
            'الأقصر - الكرنك',
        ]),
        ];
    }
}
