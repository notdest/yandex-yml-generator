YmlDocument Class
============

наследуется от http://php.net/manual/ru/class.domdocument.php


`__construct($name, $company ,$url ,$enc = "UTF-8")`
Тут создается сам xml и добавляются первые обязательные параметры. **Их надо проверить!!!**

`cms($name,$version = false)`	добавляем cms и опционально версию

`agency($name)` студия, тупо добавляем

`email($mail)` валидируем и добавляем

`function delivery($cost,$days,$before = -1)` добавить одну доставку. много проверок

`cpa($val = true)`  на входе bool, на выходе "0" или "1"

`currency($id,$rate,$plus = 0)` проверяем отсутствие запятых в числах. Опционально надбавка сверху курса

`category($id,$name,$parentId = false)` int > 0 . Опционально ид родителя

------------------

`simple( $price, $currency,$category,$name, $url='' )` - создаем упрощенный оффер, проверяем длину имени

`arbitrary( $price, $currency,$category,$vendor,$model, $url='' )` - создаем произвольный оффер

`book( $price, $currency,$category,$name, $url='' )` - оффер с книгами, на имя забил

`audiobook( $price, $currency,$category,$name, $url='' )` - оффер с аудиокнигами, на имя забил

`music( $price, $currency,$category,$name, $url='' )` - музыка, на имя забил

`video( $price, $currency,$category,$name, $url='' )` - видео, name становится title

`tour( $price, $currency,$category,$name,$days,$included,$transport, $url='' )` - тур, явно напутал type

`event( $price, $currency,$category,$name,$place,$date, $url='' )` - событие, явно напутал type

---------------
`newOffer( $price, $currency,$category,$type,$url )` - служебная функция, **все сверху её юзают**

`exc($text)` - обижаемся и выкидываем исключени

`add($name,$value=false)` - добавляем элемент в этом, глобальном пространстве