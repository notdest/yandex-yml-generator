<?php






class ymlDocument extends DomDocument 
{

	protected $currencies 	;
	protected $categories 	;
	protected $offers		;
	protected $shop 		;	


	public function __construct($name, $company ,$url ,$enc = "UTF-8")		// или windows-1251
	{
		parent::__construct('1.0',$enc);

		$imp = new DOMImplementation;
		$this->appendChild( $imp->createDocumentType('yml_catalog', '', 'shops.dtd') );			// делаем доктайп

		$root 			= $this->createElement('yml_catalog');									// делаем основные элементы
		$shop 			= $this->createElement('shop');
		$this->shop 	= $shop ;
		$root->setAttribute('date', date('Y-m-d H:i'));
		$root->appendChild($shop);
		$this->appendChild($root);

		if(mb_strlen($name,$this->encoding) >20 ) throw new RuntimeException("name='$name' длиннее 20 символов");

		$this 	->add('name'	,$name)
				->add('company'	,$company)
				->add('url'		,$url)
			 	->add('currencies')
				->add('categories')
				->add('offers');

		$this->currencies 	= $this->getElementsByTagName('currencies'	)->item(0);
		$this->categories 	= $this->getElementsByTagName('categories'	)->item(0);
		$this->offers 		= $this->getElementsByTagName('offers'		)->item(0);
	}



	public function cms($name,$version = false)
	{
		$this->add('platform',$name);
		if( $version!==false ) $this->add('version',$version);
		return $this ;
	}



	public function agency($name)
	{
		$this->add('agency',$name);
		return $this;
	}



	public function email($mail)
	{
		if( !filter_var($mail,FILTER_VALIDATE_EMAIL) ) $this->exc(' Некорректный Email');
		$this->add('email',$mail);
		return $this;
	}



	public function cpa($val = true)
	{
		if( !is_bool($val) ) $this->exc(' cpa должен быть boolean');
		$this->add('cpa',($val)?'1':'0');
		return $this;
	}



	public function deliveryCost($val)
	{
		if( !is_int($val) ) $this->exc(' Стоимость доставки должна быть int');
		$this->add('local_delivery_cost',$val);
		return $this;
	}



	public function currency($id,$rate,$plus = 0)
	{
		if(strpos($rate, ',')!==false ) $this->exc("rate разделяется только точкой");
		if(strpos($plus, ',')!==false ) $this->exc("plus разделяется только точкой");

		$c 	= $this->createElement( 'currency');

		$c->setAttribute('id', $id);
		$c->setAttribute('rate', $rate);
		if($plus)	$c->setAttribute('plus', $plus);

		$this->currencies->appendChild($c);
		return $this;
	}



	public function category($id,$name,$parentId = false)
	{	
		if( (!is_int($id)) || ($id < 1) ) 	$this->exc("id должен быть целым положительным числом > 0");

		if( ($parentId!==false) && ((!is_int($parentId)) || ($parentId < 1)) ) 
											$this->exc("parentId должен быть целым положительным числом > 0");

		$c 		= $this->createElement( 'category',$name);
		$c->setAttribute('id', $id);
		if($parentId !== false)	$c->setAttribute('parentId', $parentId);

		$this->categories->appendChild($c);
		return $this;
	}




	public function simple( $price, $currency,$category,$name, $url='' )
	{
		$offer 		= $this->newOffer( $price, $currency,$category,'simple',$url);
		$offer->addStr('name',$name,120);
		return $offer;
	}



	public function arbitrary( $price, $currency,$category,$vendor,$model, $url='' )
	{
		$offer 		= $this->newOffer( $price, $currency,$category,'arbitrary',$url);
		$offer->setAttribute('type', 'vendor.model');
		$offer->add('vendor',$vendor);
		$offer->add('model',$model);
		return $offer;
	}




	public function book( $price, $currency,$category,$name, $url='' )
	{
		$offer 		= $this->newOffer( $price, $currency,$category,'book',$url);
		$offer->setAttribute('type', 'book');
		$offer->add('name',$name);
		return $offer;
	}




	public function audiobook( $price, $currency,$category,$name, $url='' )
	{
		$offer 		= $this->newOffer( $price, $currency,$category,'audiobook',$url);
		$offer->setAttribute('type', 'audiobook');
		$offer->add('name',$name);
		return $offer;
	}



	public function music( $price, $currency,$category,$name, $url='' )
	{
		$offer 		= $this->newOffer( $price, $currency,$category,'music',$url);
		$offer->setAttribute('type', 'artist.title');
		$offer->add('title',$name);
		return $offer;
	}


	public function video( $price, $currency,$category,$name, $url='' )
	{
		$offer 		= $this->newOffer( $price, $currency,$category,'video',$url);
		$offer->setAttribute('type', 'artist.title');
		$offer->add('title',$name);
		return $offer;
	}


	public function tour( $price, $currency,$category,$name,$days,$included,$transport, $url='' )
	{
		$offer 		= $this->newOffer( $price, $currency,$category,'tour',$url);
		$offer->setAttribute('type', 'event-ticket');
		$offer->add('name',$name);
		$offer->add('days',$days);
		$offer->add('included',$included);
		$offer->add('transport',$transport);
		return $offer;
	}


	public function event( $price, $currency,$category,$name,$place,$date, $url='' )
	{
		$offer 		= $this->newOffer( $price, $currency,$category,'event',$url);
		$offer->setAttribute('type', 'tour');
		$offer->add('name',$name);
		$offer->add('place',$place);
		$offer->add('date',$date);
		return $offer;
	}

	protected function newOffer( $price, $currency,$category,$type,$url )
	{
		$offer 			= new ymlOffer($type);
		$this->offers->appendChild($offer);  	


		if( mb_strlen($url,$this->encoding) >512 )		throw new RuntimeException("url должен быть короче 512 символов");

		if( (!is_int($category)) || ($category<1) || ($category>=pow(10,19)) )
											throw new RuntimeException("categoryId - целое число, не более 18 знаков");

		$offer 	->add('price',$price)
				->add('currencyId',$currency)
				->add('categoryId',$category);
		if($url) $offer->add('url',$url);


		return $offer;
	}



	protected function exc($text)
	{
		throw new RuntimeException($text);
	}


	protected function add($name,$value=false)												// добавление элемента к shop
	{
		if($value !== false)
			$this->shop->appendChild($this->createElement( $name ,$value ));
		else
				$this->shop->appendChild($this->createElement( $name ));
		return $this ;
	}

}

?>