Поля для типа "Аудиокниги"
============

Аналогичная таблица внизу [документации Яндекса](https://yandex.ru/support/partnermarket/export/audiobooks.html).

Если поле выделено **жирным**, значит оно в конструкторе и обязательное, создать описание без него не получится. Если название(именно название ) поля выделено `как код`, то скрипт будет работать и без него, но в комментариях есть пометка, что в некоторых случаях оно обязательно. Также, если стоит ограничение, например int, то переменная должна быть приведена именно к этому типу, а не просто содержать число. В противном случае код вывалит исключение.

Параметры копировал как в документации, возможно они бредовые, смотри на конкурентов в выдаче.

Название	 			| 			Ограничение						| Псевдоним	
----------------------- | 			------------- 					|------------
|**name**				| длина <= 120<br>(ограничено не в табице)	| в конструкторе
|**publisher**			|						-					| в конструкторе
|`ISBN`					|						-					| `isbn()`
|author					|						-					|
|series					|						-					|
|year					| 					int 					|
|volume					|						-(было лень)		|
|part					|						-(было лень)		|
|language				|						-					|
|table_of_contents		|						-					| `contents()`
|type					|  					=audiobook 				| спрятан в конструкторе
|performed_by			|						-					|performer('Николай Фоменко')
|performance_type		|						-					|performance('радиоспектакль')
|storage				|						-					|
|format					|						-					|
|recording_length		|						-					|length('45.30')
|**id** 				| Цифры и буквы, до 20 символов, атрибут	| в конструкторе
|cbid 					| 			int, атрибут					|
|bid 					| 			int, атрибут					|
|fee 					| 			int, атрибут					|
|available				|			boolean, атрибут				|
|`url`					|		 длина <= 512						| 
|**price**				| 	int >= 0, boolean для from 				| в конструкторе 
|oldprice 				| 					int >= 0				|
|vat 					|						-					|
|**currencyId**			|						-					| в конструкторе
|**categoryId**			| 		целое, >0, не длиннее 18 зн.		| в конструкторе
|`picture`				| 	максимум 10 шт. Длина url <= 512 		| `pic()`
|delivery				|			boolean							|
|delivery-options		| не больше 5 шт, int и тирэ, и т.д.		| `dlvOption()`
|pickup					|					boolean					|
|store					|					boolean					|
|description			| 		длина <= 3000, возможен CDATA		|
|`sales_notes`			| 				длина <= 50					|  `sale()`
|min-quantity 			| 			int >= 1						| `minq()`
|step-quantity			| 			int >= 1						| `stepq()`	

|sales_notes			|			|!
|min-quantity			|			|
|step-quantity			|			|
|manufacturer_warranty	|			|
|country_of_origin		|			|
|adult					|			|
|barcode				|			|
|cpa					|			|
|param					|			|!
|expiry					|			|
|weight					|			|
|dimensions				|			|
|downloadable			|			|
|age					|			|

|age					| если year, то 0,6,12,16 или 18 <br> если month, то от 0 до 12| в конструкторе







|sales_notes			| 				длина <= 50					|  `sale()`
|min-quantity 			| 			int >= 1						| `minq()`
|step-quantity			| 			int >= 1						| `stepq()`
|manufacturer_warranty	|			boolean							| `warranty()`
|country_of_origin		|						-					| `origin()`
|adult					|					boolean					|
|barcode				| 			целое, 8, 12 или 13 цифр		|
|cpa					| 			boolean, 0 или 1 на выходе		|
|`param`				|						-					| 
|expiry					|						-					| 
|weight					| 					float , кг				|
|dimensions				| 		3 раза float, сантиметры			|
|downloadable			|					boolean					|