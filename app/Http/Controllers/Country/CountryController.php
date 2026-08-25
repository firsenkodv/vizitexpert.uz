<?php

namespace App\Http\Controllers\Country;

use Domain\Country\ViewModels\CountryViewModel;
use Domain\Excursion\ViewModels\ExcursionViewModel;
use Domain\Hotel\ViewModels\HotelViewModel;
use Domain\Info\ViewModels\InfoViewModel;
use Domain\Resort\ViewModels\ResortViewModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class CountryController extends Controller
{


    public function pages()
    {
        /**
         * Страница вывода всех стран
         **/

        $countries = CountryViewModel::make()->listCountries();
        // заголовок, описание категории и метатеги — из настроек страницы
        // (админка: «Категории» → «Страны»)
        $page = CountryViewModel::make()->getPageData();

        return view('pages.countries.countries', [
            'countries' => $countries,
            'page' => $page,
        ]);

    }

    public function page($slug)
    {
        /**
         * Страница вывода страны
         **/

        $country = CountryViewModel::make()->OneCountry($slug);
        $subcountries = CountryViewModel::make()->SubCountries($slug);

        abort_if(!$country, 404);

        return view('pages.countries.country', [
            'country' => $country,
            'subcountries' => $subcountries,
            'template' => $country->itemTemplate(),
        ]);

    }


    public function category($slug_country,$slug_subcountry)
    {



        /**
         * Страница вывода курортов определенной страны
         **/
        $country = CountryViewModel::make()->OneCountry($slug_country); // страна


        $hot_category = CountryViewModel::make()->HotCategoryRelation($slug_subcountry); // курорт страны (подлкатегория страны)

        abort_if(!$country || !$hot_category, 404);

        $subcountries = CountryViewModel::make()->SubCountries($slug_country); // подкатегории страны

        // Проверяем наличие через exists() — это SQL COUNT. Раньше стояло
        // count($hot_category->hotels), то есть связь целиком поднималась
        // в память ради одного числа: у «Отелей России» это 33 тысячи
        // записей, и страница падала на memory_limit.
        $resorts = $hot_category->resorts()->exists() ? $hot_category->resorts()->paginate(20) : [];

        $excursions = $hot_category->excursions()->exists() ? $hot_category->excursions()->paginate(20) : [];

        $hotels = $hot_category->hotels()->exists()
            ? $hot_category->hotels()->orderBy('imagescount', 'DESC')->orderBy('stars', 'DESC')->orderBy('desc', 'DESC')->orderBy('rating', 'DESC')->paginate(20)
            : [];

        $infos = $hot_category->infos()->exists() ? $hot_category->infos()->paginate(20) : [];

        return view('pages.countries.category', [
            'hot_category' => $hot_category,
            'subcountries' => $subcountries,
            'country' => $country,
            'resorts' => $resorts,
            'excursions' => $excursions,
            'hotels' => $hotels,
            'infos' => $infos,
            'template' => $hot_category->listTemplate(),
            'teaser_template' => $hot_category->teaserTemplate(),
        ]);

    }

    public function item($slug_country,$slug_subcountry, $slug_subcountry__item)
    {
        /**
         * Страница вывода курорта, отеля, экскурсии, прочего  определенной страны
         **/
        $country = CountryViewModel::make()->OneCountry($slug_country); // страна
        $hot_category = CountryViewModel::make()->HotCategoryRelation($slug_subcountry);  // курорт страны (подлкатегория страны)
        $subcountries = CountryViewModel::make()->SubCountries($slug_country); // подкатегории страны

        abort_if(!$country || !$hot_category, 404);

        // material подбирается ниже в одной из четырёх веток; если слаг
        // не совпал ни с одним материалом, переменная так и останется пустой
        $item = null;

        // Ниже эти четыре переменные нужны только как признак «в этой
        // подкатегории есть материалы такого типа», сами списки не выводятся.
        // Поэтому спрашиваем базу через exists(), а не тянем связь целиком:
        // у «Отелей России» в ней 33 тысячи строк.
        $resorts = $hot_category->resorts()->exists();
        $excursions = $hot_category->excursions()->exists();
        $hotels = $hot_category->hotels()->exists();
        $infos = $hot_category->infos()->exists();

        $view = 'pages.countries.item';

 /*       dump($resorts);
        dump($excursions);
        dump($hotels);
        dump($infos);*/

        if($resorts) {
            $item = ResortViewModel::make()->OneResort($slug_subcountry__item); // материал курорта

        }

        if($excursions) {
            $item = ExcursionViewModel::make()->OneExcursion($slug_subcountry__item); // материал экскурсии

        }

        if($hotels) {
            $item = HotelViewModel::make()->OneHotel($slug_subcountry__item); // материал отеля
            $view = 'pages.countries.hotel';


        }

        if($infos) {
            $item = InfoViewModel::make()->OneInfo($slug_subcountry__item); // материал полезного


        }


        abort_if(!$item, 404);

        return view($view, [
            'hot_category' => $hot_category,
            'item' => $item,
            'subcountries' => $subcountries,
            'country' => $country,
            'template' => $item->itemTemplate(),
        ]);

    }



}
