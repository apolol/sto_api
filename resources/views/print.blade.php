<!DOCTYPE html>
<html>
<head>
  <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="w-full p-3 mx-auto">
    <div class="flex flex-wrap items-center gap-1">
        <div class="flex flex-wrap w-7/12">
            <h1 class="w-full text-xl">Акт виконаних робіт</h1>
            <input class="text-sm text-gray-400 w-full" type="text" value="№: {{$data->number}} | Дата: {{(!is_null($data->finish_work)? $data->finish_work->format('d/m/Y') : '')}}"></input>
        </div>
        <div class="w-2/12">
            <img class=" h-[80px]" src="https://www.westcars.ua/logo.webp" width="150" height="150">
        </div>
        <div class="w-2/12 ml-3 text-[11px]">
            <span class="font-bold">Westcars</span>
            <br/>
            м. Луцьк, вул. Карбишева 2
            <br/>
            066-777-2000
            <br/>
            info@westcars.ua
        </div>
    </div>
    <div class="flex flex-wrap w-full mt-10">
        <div class="flex flex-wrap w-6/12">
            <div class="w-full shadow sm:rounded-lg">
                <div class="px-4 py-2 bg-gray-100 ">
                    <h3 class="text-[11px] font-semibold leading-6 text-gray-900">Інформація про компанію</h3>
                </div>

                <table class="w-full text-[11px] font-medium text-gray-800 table-fixed">
                    <tr >
                        <td class="w-2/12 px-4 py-1">Назва</td>
                        <td class="w-10/12 px-4 py-1"><strong>ФОП Гаврилов Б.</strong></td>
                    </tr>
                    <tr >
                        <td class="px-4 py-1">Адреса</td>
                        <td class="px-4 py-1">м. Луцьк, вул. Карбишева 2</td>
                    </tr>
                    <tr >
                        <td class="px-4 py-1">IBAN</td>
                        <td class="px-4 py-1">UA273052990000026003010807356</td>
                    </tr>
                    <tr >
                        <td class="px-4 py-1">ІПН</td>
                        <td class="px-4 py-1">2803111996</td>
                    </tr>
                    <tr >
                        <td class="px-4 py-1">Телефон</td>
                        <td class="px-4 py-1">066-77-72-000</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="flex flex-wrap w-6/12">
            <div class="w-full ml-3 overflow-hidden shadow sm:rounded-lg">
                <div class="px-4 py-2 bg-gray-100 ">
                    <h3 class="text-[11px] font-semibold leading-6 text-gray-900">Інформація про клієнта</h3>
                </div>
                <table class="w-full text-[11px] font-medium text-gray-800 table-fixed">
                    <tr >
                        <td class="w-2/12 px-4 py-1">ПІБ</td>
                        <td class="w-10/12 px-4 py-1"><strong>{{$data?->client->first_name}} {{$data?->client->last_name}}</strong></td>
                    </tr>
                    <tr >
                        <td class="px-4 py-1">Адреса</td>
                        <td class="px-4 py-1">Волинська обл; м. Луцьк;</td>
                    </tr>
                    <tr >
                        <td class="px-4 py-1">Телефон</td>
                        <td class="px-4 py-1">{{$data?->client->phone}}</td>
                    </tr>
                     <tr >
                        <td class="px-4 py-1">Авто</td>
                        <td class="px-4 py-1">{{$data?->car->brand->parent->title}} {{$data?->car->brand->title}} | {{$data?->car->car_plate}} | {{$data?->car->odometer}} км</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="w-full mt-6 overflow-hidden shadow sm:rounded-lg">
            <div class="px-4 py-1 bg-gray-100 ">
                <h3 class="text-[11px] font-semibold leading-6 text-center text-gray-900">Виконані роботи</h3>
            </div>
                <table class="w-full text-[11px] text-gray-800 table-fixed">
                    <tr class="text-left border-b border-gray-200">
                        <th class="w-6/12 px-4 py-1 text-left">Назва</th>
                        <th class="w-2/12 px-4 py-1 text-right">Ціна</th>
                        <th class="w-2/12 px-4 py-1 text-right">Кількість</th>
                        <th class="w-2/12 px-4 py-1 text-right">Сума</th>
                    </tr>

                     @foreach ($data->works as $work)
                        <tr class="border-b border-gray-200 ">
                            <td class="px-4 py-1">{{$work?->work_name->title ?? ''}}</td>
                            <td class="px-4 py-1 text-right">{{number_format((float)$work->price, 2, '.', '') }} грн</td>
                            <td class="px-4 py-1 text-right">{{number_format((float) $work->count, 2, '.', '')}} шт</td>
                            <td class="px-4 py-1 text-right">{{ number_format((float)$work->price * (float)$work->count, 2, '.', '')}} грн</td>
                        </tr>
                    @endforeach

                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 font-bold text-right">Сума</td>
                        <td class="px-4 py-1 text-right">{{$sum_for_work}} грн</td>
                    </tr>
                </table>
        </div>
        @if($data->products != null)
            <div class="w-full mt-6 overflow-hidden shadow sm:rounded-lg">
                <div class="px-4 py-1 text-center bg-gray-100">
                    <h3 class="text-[11px] font-semibold leading-6 text-gray-900">Встановлені запасні частини та матеріали</h3>
                </div>
                <table class="w-full text-[11px] text-gray-800 table-fixed">
                    <tr class="text-left border-b border-gray-200">
                        <th class="w-6/12 px-4 py-1 text-left">Назва</th>
                        <th class="w-2/12 px-4 py-1 text-right">Ціна</th>
                        <th class="w-2/12 px-4 py-1 text-right">Кількість</th>
                        <th class="w-2/12 px-4 py-1 text-right">Сума</th>
                    </tr>
                    @foreach ($data->products as $prod)
                        <tr class="border-b border-gray-200 ">
                            <td class="px-4 py-1">{{$prod->title}} {{$prod->articul}} {{$prod->brand}}</td>
                            <td class="px-4 py-1 text-right">{{number_format((float)$prod->price_for_client, 2, '.', '') }} грн</td>
                            <td class="px-4 py-1 text-right">{{number_format((float)$prod->count, 2, '.', '') }}</td>
                            <td class="px-4 py-1 text-right">{{number_format((float)$prod->price_for_client * $prod->count, 2, '.', '') }} грн</td>
                        </tr>
                    @endforeach
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 font-bold text-right">Сума</td>
                        <td class="px-4 py-1 text-right">{{$sum_for_prod}} грн</td>
                    </tr>
                    <!-- each row -->
                </table>
            </div>
        @endif

        <div class="w-full mt-4 text-sm font-bold text-right">
            До сплати: {{number_format($sum_for_prod + $sum_for_work, 2, '.', '')}} грн
        </div>
        <div class="w-full mt-2 text-[10px]  text-left">
            Всі роботи та послуги виконані якісно та в повному обсязі. Претензій до обсягу, якості та строку виконаних робіт та до якості ТЗ не маю. З обсягом робіт та послуг згоден.
        </div>
        <div class="flex flex-wrap justify-between w-full mt-10 text-[11px]">
            <div class="w-full mb-10 text-sm font-bold text-center">
                Підписи сторін
            </div>
            <div class="w-4/12">
                ФОП Гаврилов   __________________
            </div>
            <div class="w-7/12">
                {{$data?->client->first_name}} {{$data?->client->last_name}} __________________
            </div>
        </div>
    </div>


</body>

</html>
