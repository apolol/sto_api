<!DOCTYPE html>
<html>
<head>
  <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="w-full p-9 mx-auto">
    <div class="flex flex-wrap items-center gap-1">
        <div class="flex flex-wrap w-7/12">
            <input type="text w-full" class="w-full text-[11px]" value="Рахунок на оплату №: {{$data->number}}R від {{now()->format('d.m.Y')}}"></input>
        </div>
        <div class="w-2/12">
            <img class=" h-[60px]" src="https://www.westcars.ua/logo.webp" width="150" height="150">
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
    <div class="flex flex-wrap w-full mt-5">
        <div class="flex flex-wrap w-6/12">
            <div class="w-full shadow sm:rounded-lg">
                <div class="px-4 py-2 bg-gray-100 ">
                    <h3 class="text-[11px] font-semibold leading-6 text-gray-900">Інформація про компанію</h3>
                </div>

                <table class="w-full text-[11px] font-medium text-gray-800 table-fixed">
                    <tr >
                        <td class="w-2/12 px-4 py-1">Назва</td>
                        <td class="w-10/12 px-4 py-1"><strong>ФОП Гаврилова Л.Г.</strong></td>
                    </tr>
                    <tr >
                        <td class="px-4 py-1">Адреса</td>
                        <td class="px-4 py-1">м. Луцьк, вул. Карбишева 2</td>
                    </tr>
                    <tr >
                        <td class="px-4 py-1">IBAN</td>
                        <td class="px-4 py-1">UA373052990000026007030811641</td>
                    </tr>
                    <tr >
                        <td class="px-4 py-1">ЄДРПОУ</td>
                        <td class="px-4 py-1">1599408340</td>
                    </tr>
                    <tr >
                        <td class="px-4 py-1">ІПН</td>
                        <td class="px-4 py-1">159940834030</td>
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
                    <h3 class="text-[11px] font-semibold leading-6 text-gray-900">Інформація про покупця</h3>
                </div>
                <table class="w-full text-[11px] font-medium text-gray-800 table-fixed">
                    <tr>
                        <td class="w-4/12 px-4 py-1">Назва</td>
                        <td class="w-8/12 px-4 py-1"><strong>{{$data?->client->company_name}}</strong></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-1">Адреса</td>
                        <td class="px-4 py-1">{{$data?->client->company_address}}</td>
                    </tr>

                    <tr>
                        <td class="px-4 py-1">ЄДРПОУ</td>
                        <td class="px-4 py-1">{{$data?->client->company_edrpu}}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-1">Авто</td>
                        <td class="px-4 py-1">{{$data?->car->brand->parent->title}} {{$data?->car->brand->title}} | {{$data?->car->car_plate}} | {{$data?->car->odometer}} км</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-1">Оплатити до:</td>
                        <td class="px-4 py-1">{{$day_to_pay}}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="w-full font-bold text-gray-800 mt-3 text-[11px]">
            Примітка: рахунок дійсний протягом 5 банківських днів.
        </div>
        <div class="w-full mt-6 overflow-hidden shadow sm:rounded-lg">
            <div class="px-4 py-1 bg-gray-100 ">
                <h3 class="text-[11px] font-semibold leading-6 text-center text-gray-900">Перелік наданих послуг / виконаних робіт</h3>
            </div>
                <table class="w-full text-[11px] text-gray-800 table-fixed">
                    <tr class="text-left border-b border-gray-200">
                        <th class="w-1/12 px-4 py-1 text-left">№</th>
                        <th class="w-5/12 px-4 py-1 text-left">Назва</th>
                        <th class="w-2/12 px-4 py-1 text-right">Ціна без ПДВ, грн</th>
                        <th class="w-2/12 px-4 py-1 text-right">Кількість, шт</th>
                        <th class="w-2/12 px-4 py-1 text-right">Сума без ПДВ, грн</th>
                    </tr>

                     @foreach ($data->works as $work)
                        <tr class="border-b border-gray-200 ">
                            <td class="px-4 py-1">{{$loop->index + 1}}</td>
                            <td class="px-4 py-1">{{$work?->work_name->title ?? ''}} УКТ ЗЕД 45.20.1</td>
                            <td class="px-4 py-1 text-right">{{number_format((float)$work->price, 2, '.', '') }} грн</td>
                            <td class="px-4 py-1 text-right">{{number_format((float) $work->count, 2, '.', '')}} шт</td>
                            <td class="px-4 py-1 text-right">{{ number_format((float)$work->price * (float)$work->count, 2, '.', '')}} грн</td>
                        </tr>
                    @endforeach

                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 font-bold text-right">Сума</td>
                        <td class="px-4 py-1 text-right">{{$sum_for_work}} грн</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 font-bold text-right">ПДВ</td>
                        <td class="px-4 py-1 text-right">{{$sum_pdv_work}} грн</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 font-bold text-right">Всього з ПДВ</td>
                        <td class="px-4 py-1 text-right">{{$sum_work_with_pdv}} грн</td>
                    </tr>
                </table>
        </div>

        @if($data->products->count() > 0)
            <div class="w-full mt-6 overflow-hidden shadow sm:rounded-lg">
                <div class="px-4 py-1 text-center bg-gray-100">
                    <h3 class="text-[11px] font-semibold leading-6 text-gray-900">Встановлені запасні частини та матеріали</h3>
                </div>
                <table class="w-full text-[11px] text-gray-800 table-fixed">
                    <tr class="text-left border-b border-gray-200">
                        <th class="w-1/12 px-4 py-1 text-left">№</th>
                        <th class="w-5/12 px-4 py-1 text-left">Назва</th>
                        <th class="w-5/12 px-4 py-1 text-left">Бренд</th>
                        <th class="w-2/12 px-4 py-1 text-right">Ціна без ПДВ, грн</th>
                        <th class="w-2/12 px-4 py-1 text-right">Кількість, шт</th>
                        <th class="w-2/12 px-4 py-1 text-right">Сума без ПДВ, грн</th>
                    </tr>
                    @foreach ($data->products as $prod)
                        <tr class="border-b border-gray-200 ">
                            <td class="px-4 py-1">{{$loop->index + 1}}</td>
                            <td class="px-4 py-1">{{$prod->title}} УКТ ЗЕД {{$prod->uktz}}</td>
                            <td class="px-4 py-1">{{$prod->brand}}</td>
                            <td class="px-4 py-1 text-right">{{number_format((float)$prod->price_for_client, 2, '.', '') }} грн</td>
                            <td class="px-4 py-1 text-right">{{number_format((float)$prod->count, 2, '.', '') }}</td>
                            <td class="px-4 py-1 text-right">{{number_format((float)$prod->price_for_client * $prod->count, 2, '.', '') }} грн</td>
                        </tr>
                    @endforeach
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 font-bold text-right">Сума</td>
                        <td class="px-4 py-1 text-right">{{$sum_for_prod}} грн</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 font-bold text-right">ПДВ</td>
                        <td class="px-4 py-1 text-right">{{$sum_pdv_parts}} грн</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 text-right"></td>
                        <td class="px-4 py-1 font-bold text-right">Всього з ПДВ</td>
                        <td class="px-4 py-1 text-right">{{$sum_parts_with_pdv}} грн</td>
                    </tr>
                    <!-- each row -->
                </table>
            </div>
        @endif

        <div class="w-full mt-6 overflow-hidden shadow sm:rounded-lg">
            <table class="w-full text-[11px] text-gray-800 table-fixed">
                <tr class="text-left border-b border-gray-200">
                    <th class="w-1/12 px-4 py-1 text-left">Вартість</th>
                    <th class="w-5/12 px-4 py-1 text-left">Сума без ПДВ</th>
                    <th class="w-2/12 px-4 py-1 text-right">ПДВ</th>
                    <th class="w-2/12 px-4 py-1 text-right">Загальна сума з ПДВ</th>
                </tr>
                    <tr class="border-b border-gray-200 ">
                        <td class="px-4 py-1">Всього, грн</td>
                        <td class="px-4 py-1">{{$sum_without_pdv}}</td>
                        <td class="px-4 py-1 text-right">{{$sum_pdv}} грн</td>
                        <td class="px-4 py-1 text-right">{{$sum_wit_pdv }}</td>
                    </tr>
                <!-- each row -->
            </table>
        </div>

        <div class="w-full mt-2 text-sm font-bold text-left">
             <input type="text" class="w-full" value="До оплати (прописом): {{$text_pay}}">
        </div>
        <div class="w-full mt-2 text-[10px] text-left">
            Всі роботи та послуги виконані якісно та в повному обсязі. Претензій до обсягу, якості та строку виконаних робіт та до якості ТЗ не маю. З обсягом робіт та послуг згоден.
        </div>
        <div class="flex flex-wrap justify-between w-full mt-10 text-[11px] px-10">

            <div class="w-7/12">

            </div>
            <div class="w-5/12 font-bold">
                Виписав (ла) Фізична особа-підприємець
                <br>
                <br>
                        __________________Ліза ГАВРИЛОВА
            </div>
        </div>
    </div>


</body>

</html>
