<?php






class ymlDocument extends DomDocument 
{

	protected $currencies 	;
	protected $categories 	;
	protected $offers		;
	protected $shop 		;	


	public function __construct($name, $company ,$enc = "UTF-8")		// или windows-1251
	{
		parent::__construct('1.0',$enc);

		$imp = new DOMImplementation;

		$root 			= $this->createElement('yml_catalog');									// делаем основные элементы
		$shop 			= $this->createElement('shop');
		$this->shop 	= $shop ;
		$root->setAttribute('date', date('Y-m-d H:i'));
		$root->appendChild($shop);
		$this->appendChild($root);

		if(mb_strlen($name,$this->encoding) >20 ) throw new RuntimeException("name='$name' длиннее 20 символов");

		$this 	->add('name'	,$name)
				->add('company'	,$company)
			 	->add('currencies')
				->add('categories')
				->add('offers');

		$this->currencies 	= $this->getElementsByTagName('currencies'	)->item(0);
		$this->categories 	= $this->getElementsByTagName('categories'	)->item(0);
		$this->offers 		= $this->getElementsByTagName('offers'		)->item(0);
	}


	public function url($url)
	{
		if( mb_strlen($url,$this->encoding) >50 )	$this->exc(	"url должен быть короче 50 символов"	);	
		$this->add('url',$url);
		return $this;
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


	public function delivery($cost,$days,$before = -1)
	{
		$dlvs 	= $this->getElementsByTagName('delivery-options');

		if( !$dlvs->length ){
			$dlv 	= $this->createElement( 'delivery-options');
			$this->shop->appendChild($dlv);
		}else{
				$dlv 	= $dlvs[0];
				$opts 	= $dlv->getElementsByTagName('option');
				if($opts->length >= 5) $this->exc("максимум 5 опций доставки");
		}

		if( !is_int($cost) || $cost<0 ) 			$this->exc("cost должно быть целым и положительным");
		if( preg_match("/[^0-9\-]/",$days) )		$this->exc("days должно состоять из цифр и тирэ");
		if( !is_int($before) || $before>24 )		$this->exc("order-before должно быть целым и меньше 25");

		$opt 	= $this->createElement( 'option');

		$opt->setAttribute('cost', $cost);
		$opt->setAttribute('days', $days);

		if($before >= 0) $opt->setAttribute('order-before', $before);

		$dlv->appendChild($opt);

		return $this;
	}


	public function cpa($val = true)
	{
		if( !is_bool($val) ) $this->exc(' cpa должен быть boolean');
		$this->add('cpa',($val)?'1':'0');
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


	public function simple( $name, $id, $price, $currency, $category, $from = NULL )
	{
		$offer 		= $this->newOffer(  $id, $price, $currency, $category,'simple', $from );
		$offer->addStr('name',$name,120);
		return $offer;
	}


	public function arbitrary( $model, $vendor, $id, $price, $currency, $category, $from = NULL )
	{
		$offer 		= $this->newOffer(  $id, $price, $currency, $category,'arbitrary', $from );
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


	protected function newOffer( $id, $price, $currency, $category, $type, $from )
	{
		$offer 			= new ymlOffer($type,$this->encoding);
		$this->offers->appendChild($offer);  	

		if(preg_match("/[^a-z,A-Z,0-9]/",$id)) 		$this->exc("id должен содержать только латинские буквы и цифры");
		if( strlen($id)>20 )						$this->exc("id длиннее 20 символов");

		if( (!is_int($category)) || ($category<1) || ($category>=pow(10,19)) )
											$this->exc("categoryId - целое число, не более 18 знаков");

		if( !is_int($price) || $price<0 ) 			$this->exc("price должно быть целым и положительным");

		if( !is_null($from)){
			if( !is_bool($from) ) 					$this->exc('from должен быть boolean');
		}

		$offer 	->setAttribute('id',$id );
		$offer 	->add('currencyId',$currency)
				->add('categoryId',$category);

		$pr 	= new DomElement('price',$price);
		$offer->appendChild($pr);
		if( !is_null($from))		$pr->setAttribute('from',($from) ? 'true' :'false' );

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