<?php

class ymlOffer extends DomElement
{
	protected $enc  		;
	protected $type  		;
	protected $permitted 	;
	protected $aliases 		= array('origin'=>'country_of_origin','warranty'=>'manufacturer_warranty',
			'sale' =>'sales_notes','pic'=>'picture','isbn'=>'ISBN','pages'=>'page_extent','contents'=>'table_of_contents','performer' =>'performed_by',
			'performance'=>'performance_type','length'=>'recording_length','stars' => 'hotel_stars','priceMin'=>'price_min','priceMax' =>'price_max','hallPart'=>'hall_part',
			'premiere' => 'is_premiere', 'kids' => 'is_kids')	;


	public function __construct($type,$enc)
	{
		parent::__construct('offer');
		$this->type = $type;
		$p=array( 
			'simple' 	=>		array('group_id','minq','stepq','model','expiry','picture','weight','vat','age','store','pickup','delivery','vendor','vendorCode','manufacturer_warranty','downloadable','adult','rec','dimensions'),
			'arbitrary' =>		array('group_id','picture','minq','stepq','vat','age','store','pickup','delivery','vendorCode','manufacturer_warranty','adult','downloadable','typePrefix','rec','expiry','weight','dimensions'),
			'book'		=>		array('picture','vat','age','store','pickup','manufacturer_warranty','delivery','downloadable','author','series','year','ISBN','volume','part','language','binding','page_extent','minq','stepq','adult','table_of_contents','expiry','weight','dimensions'),
			'audiobook' =>		array('picture','adult','manufacturer_warranty','vat','minq','stepq','store','pickup','age','downloadable','author','series','year','delivery','ISBN','volume','part','language','table_of_contents','performed_by','performance_type','storage','format','recording_length','expiry','weight','dimensions'),
			'artist' 	=>		array('picture','minq','manufacturer_warranty','stepq','adult','vat','age','store','pickup','delivery','year','media','artist','expiry','weight','dimensions','downloadable','starring','director','originalName','country'),
			'tour' 		=>		array('picture','minq','stepq','vat','manufacturer_warranty','age','adult','store','pickup','delivery','country','worldRegion','region','dataTour','hotel_stars','room','meal','price_min','price_max','expiry','weight','dimensions','downloadable','options'),
			'event' 	=>		array('picture','vat','age','store','pickup','delivery','hall','hall_part','is_premiere','is_kids'));

		$p_all =array('sales_notes','country_of_origin','barcode','cpa','param'); // методы для всех

		$this->permitted 	= array_merge( $p[$type],$p_all ) ;

		$this->enc 			= $enc ;
	}


	public function available($val=true)
	{
		$this->check(	!is_bool($val) 		, "available должен быть boolean"	);
		$this->setAttribute('available',($val) ? 'true':'false' );
		return $this;
	}

	public function bid($bid)
	{
		$this->check(	!is_int($bid) 		, "bid должен быть integer"	);
		$this->setAttribute('bid',$bid );
		return $this;
	}

	public function cbid($cbid)
	{
		$this->check(	 !is_int($cbid)		, "cbid должен быть integer" );
		$this->setAttribute('cbid',$cbid );
		return $this;
	}

	public function fee($fee)
	{
		$this->check(	!is_int($fee) 		, "fee должен быть integer"	);
		$this->setAttribute('fee',$fee );
		return $this;
	}

	public function url($url)
	{
		$this->addStr('url',$url,512);
		return $this;
	}

	public function oldprice($oldprice)
	{
		$this->check(	(!is_int($oldprice)) || ($oldprice < 1)	 , "oldprice должен быть целым положительным числом > 0"	);
		$this->add('oldprice',$oldprice);
		return $this;
	}

	public function dlvOption($cost,$days,$before = -1)
	{
		$dlvs 	= $this->getElementsByTagName('delivery-options');

		if( !$dlvs->length ){
			$dlv 	= new DomElement('delivery-options');
			$this->appendChild($dlv);
		}else{
				$dlv 	= $dlvs[0];
				$opts 	= $dlv->getElementsByTagName('option');
				$this->check(	$opts->length >= 5	 	, "максимум 5 опций доставки"	);
		}

		$this->check(	!is_int($cost) || $cost<0	 	 , "cost должно быть целым и положительным"	);
		$this->check(	preg_match("/[^0-9\-]/",$days)	 , "days должно состоять из цифр и тирэ"	);
		$this->check(	!is_int($before) || $before>24	 , "order-before должно быть целым и меньше 25");

		$opt 	= new DomElement( 'option');
		$dlv->appendChild($opt);

		$opt->setAttribute('cost', $cost);
		$opt->setAttribute('days', $days);

		if($before >= 0) $opt->setAttribute('order-before', $before);

		return $this;
	}


	public function description($txt,$tags = false)
	{
		$this->check(	mb_strlen($txt,$this->enc)>3000	 , "description должен быть короче 3000 символов"	);

		if( $tags ){
			$cdata	= new DOMCdataSection($txt);
			$desc 	= new DomElement( 'description');
			$this->appendChild($desc);
			$desc->appendChild($cdata);
			return $this;
		}else{
				return $this->add('description');
		}

		
	}


	function __call($method, $args)
	{
		if( array_key_exists($method,$this->aliases) )	$method = $this->aliases[$method];

		$this->check(	!in_array( $method,$this->permitted )		 , "$method вызван при типе товара {$this->type}"	);

		// значения, которые просто добавляем
		if( in_array($method, array('model','series','author','vendorCode','vendor','expiry','rec',
			'typePrefix','country_of_origin','ISBN','volume','part','language','binding','table_of_contents','performed_by',
			'performance_type','storage','format','recording_length','artist','media','starring','director','originalName','country','worldRegion','region','dataTour'
			,'hotel_stars','room','meal','price_min','price_max','options','hall','hall_part','is_premiere','is_kids','vat')) )
			return $this->add($method,$args[0]);

		// флаги
		if( in_array($method, array('downloadable','adult','store','pickup','delivery','manufacturer_warranty')) )
		{
			if( !isset($args[0]) )	$args[0] = true;
			return $this->add($method,($args[0]) ? 'true' :'false');
		}	

		$method = '_'.$method;
		return $this->$method($args);
	}

	protected function _minq($args)
	{
		$this->check(	!is_int($args[0]) || $args[0]<1		 , "min-quantity должен содержать только цифры"	);
		return $this->add('min-quantity',$args[0]);
	}

	protected function _stepq($args)
	{
		$this->check(	!is_int($args[0]) || $args[0]<1 	, "step-quantity должен содержать только цифры"	);
		return $this->add('step-quantity',$args[0]);
	}

	protected function _page_extent($args)
	{
		$this->check(	!is_int($args[0])	, "page_extent должен содержать только цифры"	);
		$this->check(	$args[0]<0			, "page_extent должен быть положительным числом"	);
		return $this->add('page_extent',$args[0]);
	}


	protected function _sales_notes($args)
	{
		return $this->addStr('sales_notes',$args[0],50);
	}


	protected function _age($args)	
	{ 
		$this->check(	!is_int($args[0]) , 	"age должен иметь тип int" );

		$ageEl 	= new DomElement( 'age',$args[0] );
		$this->appendChild($ageEl);
		$ageEl->setAttribute('unit',$args[1] );

		switch ($args[1]) 
		{
			case 'year':
				$this->check(	!in_array($args[0],array(0,6,12,16,18)) , "age при age_unit=year должен быть 0, 6, 12, 16 или 18"	);
				break;

			case 'month':
				$this->check(	($args[0]<0)||($args[0]>12) 			, "age при age_unit=month должен быть 0<=age<=12"	);
				break;
			
			default:
				$this->check(	true , 									"age unit должен быть month или year"	);
				break;
		}
		return $this;
	}

	protected function _param($args)
	{
		$newEl 	= new DomElement('param',$args[1]);
		$this->appendChild($newEl);
		$newEl->setAttribute('name', $args[0]);
		if( isset($args[2]) ) 	$newEl->setAttribute('unit', $args[2]);
		return $this ;
	}

		
	protected function _picture( $args )
	{
		$pics	= $this->getElementsByTagName('picture');
		$this->check(	$pics->length >10	 , "Можно использовать максимум 10 картинок"	);
		$this->addStr('picture',$args[0],512);
		return $this;
	}

	
	protected function _group_id($args)
	{
		$this->check(	!is_int($args[0])	 , "group_id должен содержать только цифры"	);
		$this->check(	strlen($args[0])>9	 , "group_id не должен быть длиннее 9 символов"	);
		$this->setAttribute('group_id',$args[0] );
		return $this;
	}


	protected function _barcode($args)
	{
		$len 	= strlen($args[0]);
		$this->check(	!is_int($args[0])	 				, "barcode должен содержать только цифры"	);
		$this->check(	!($len==8 || $len==12 || $len==13)	, "barcode должен содержать 8, 12 или 13 цифр"	);
		return $this->add('barcode',$args[0]);
	}


	protected function _year($args)
	{
		$this->check(	!is_int($args[0])	 , 	"year должен быть int" );
		return $this->add('year',$args[0]);
	}


	protected function _dimensions($args)
	{		
		$this->check(	!is_float($args[0]) || !is_float($args[1]) || !is_float($args[2])	 , "dimensions должен быть float"	);
		return $this->add('dimensions',$args[0].'/'.$args[1].'/'.$args[2]);
	}


	protected function _weight($args)
	{		
		$this->check(	!is_float($args[0])	 				, "weight должен быть float"	);
		return $this->add('weight',$args[0]);
	}

	protected function _cpa($args)
	{
		if( !isset($args[0]) ) $args[0] = true;
		return $this->add('cpa', ($args[0])?'1':'0' );
	}

	protected function check($expr , $msg)
	{
		if( $expr ) throw new RuntimeException($msg);
	}

	public function addStr( $name,$val,$limit )
	{
		$this->check(	$limit && ( mb_strlen($val,$this->enc)>$limit) , 	"$name должен быть короче $limit символов");
		return $this->add( $name,$val );
	}

	public function add( $name,$val=false )
	{
		$newEl 	= ($val===false) ? new DomElement($name) : new DomElement($name,$val);
		$this->appendChild($newEl);
		return $this;
	}


}
