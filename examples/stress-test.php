<?php
set_time_limit(300);
$limit = 320000;     // вот столько надо для файла 500мб

error_reporting(E_ALL);
ini_set('display_errors', '1');
include('../src/ymlOffer.php');
include("../src/ymlDocument.php");

use notdest\yandexYmlGenerator\ymlDocument ;

$start  = time();
  
  $y     = new ymlDocument('Магаз','ООО Шикарный магаз интернейшнл');


$y ->url('http://best.seller.ru');
 $y ->cms('Joomla!','3.4')
     ->agency('Webdivision.ru')
     ->email('notdest@gmail.com');
  $y ->currency('RUR',1)
     ->currency('USD','CBRF',3)
     ->currency('EUR',70.8)
     ->category(1,'Книги')
     ->category(2,'Детективы',1)
     ->category(3,'Боевики',1)
     ->category(4,'Видео')
     ->category(5,'Комедии',4);
  $y ->delivery(300,4,18) 
     ->delivery(500,0,15)
     ->delivery(0,'7-8')

     ->cpa();

$iter = 0;

while ($iter < $limit) {                // цикл нагрузки

                
    $offer = $y->simple('Наручные часы Casio A1234567B', 'id01id1111', 900, "USD",15 /* , true*/ );


    $offer  ->model('V RACER NYLON')
            ->vendor('Adidas')
            ->vendorCode('I do not know')
            ->cbid(80)
            ->bid(90)
            ->fee(220)
            ->available(false)
            ->url("http://magaz.ru/tovar.html")
            ->oldprice(1500)
            ->pic('http://best.seller.ru/img/device12345.jpg')
            ->pic('http://best.seller.ru/img/device124.jpg')
            ->pic('http://best.seller.ru/img/devi45.jpg')
            ->delivery(/* false*/ )
            ->dlvOption(300,4,18)
            ->dlvOption(0,'7-8')
            ->pickup()
            ->store()
            ->description(
'<h3>Односторонний матрас средней жесткости  EVS 500</h3>
    <p>Наполнители:</p>
    <ul>
      <li>пенополиуретан</li>
      <li>латексированная кокосовая койра</li>
    </ul>'
 ,true)
            ->sale('первым десяти покупателям скидка 15%')
            ->warranty()
            ->origin('Демократическая Республика Конго')
            ->adult()
            ->barcode(11122299)
            ->cpa(false)
            ->param('Размер экрана','27','дюйм')
            ->param('Материал','алюминий')
            ->expiry('P1Y2M10DT2H30M')
            ->weight(15.1)
            ->dimensions(14.0,80.2,90.0)
            ->downloadable()
            ->age(5,'month')
            ->group_id(111111111)
            ->rec('123123,1214,243') ;

$iter ++;
}

echo " Пройдено $iter итераций<br>\n";
echo "время выполнения: ", time()-$start," сек.<br>\n";
echo "максимальное использование памяти: ",memory_get_peak_usage(true), " байт<br>\n";

?>
