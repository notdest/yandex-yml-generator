YmlDocument Class
============

Наследуется от [DomDocument](http://php.net/manual/ru/class.domdocument.php)

`__construct($name, $company ,$enc = "UTF-8")` - тут создается сам xml и добавляются первые обязательные параметры.

---------
Далее идет группа параметров самого магазина(общие поля):

`url($url)` - проверяем длину, 50 символов.

`cms($name,$version = false)` - добавляем cms и опционально версию.

`agency($name)` - студия, просто добавляем.

`email($mail)` - валидируем и добавляем.

`delivery($cost,$days,$before = -1)` - добавить одну доставку. много проверок.

`cpa($val = true)` - на входе bool, на выходе "0" или "1".

`currency($id,$rate,$plus = 0)` - проверяем отсутствие запятых в числах. Опционально надбавка сверху курса.

`category($id,$name,$parentId = false)` - id целые и положительные . Опционально id родителя.

------------------
Далее идет группа функций, которые возвращают товарные предложения нужного типа.<br>
 **Возможно требует внимания**, поскольку я часто забываю здесь установить обязательный параметр, или путаю значение для `type`.

`simple( $name, $id, $price, $currency, $category, $from = NULL )` - создаем упрощенный оффер, проверяем длину имени.

`arbitrary( $model, $vendor, $id, $price, $currency, $category, $from = NULL )` - создаем произвольный оффер.

`book($name, $publisher, $age, $age_u, $id, $price, $currency, $category, $from = NULL)` - оффер с книгами.

`audiobook( $name, $publisher, $id, $price, $currency, $category, $from = NULL )` - оффер с аудиокнигами.

`artist( $title, $id, $price, $currency, $category, $from = NULL )` - аудио и видеопродукция.

`tour( $name,$days,$included,$transport, $id, $price, $currency, $category, $from = NULL )` - туры, побоялся устанавливать ограничение на длину имени.

`event(  $name,$place,$date, $id, $price, $currency, $category, $from = NULL)` - событие, длину имени тоже не ограничиваю.

`medicine( $name, $id, $price, $currency, $category, $from = NULL )` - лекарства. Ряд параметров устанавливаю принудительно.

---------------
Далее идет ряд служебных `protected`-функций:

`newOffer( $id, $price, $currency, $category, $type, $from )` - вызывается при создании любого типа оффера, требует и проверяет поля обязательные для всех типов.

`exc($text)` - короткий псевдоним для выкидывания исключений.

`add($name,$value=false)` - Добавляем элемент к элементу `shop`. Все общие поля магазина используют эту функцию.