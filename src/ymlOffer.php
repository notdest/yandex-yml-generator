<?php
namespace yml_generator;
class ymlOffer extends \DomElement
{
    const DESCRIPTION_MAX_LENGTH = 3000;
	protected $type  		;
	protected $permitted 	;
	protected $aliases 		= array('origin'=>'country_of_origin','category'=>'market_category','deliveryCost' =>'local_delivery_cost','warranty'=>'manufacturer_warranty',
			'sale' =>'sales_notes','pic'=>'picture','isbn'=>'ISBN','pages'=>'page_extent','contents'=>'table_of_contents','performer' =>'performed_by',
			'performance'=>'performance_type','length'=>'recording_length','stars' => 'hotel_stars','priceMin'=>'price_min','priceMax' =>'price_max','hallPart'=>'hall_part',
			'premiere' => 'is_premiere', 'kids' => 'is_kids')	;


	public function __construct($type)
	{
		parent::__construct('offer');
		$this->type = $type;
		$p=array(
			'simple' 	=>		array('oldprice','market_category','picture','description','age','store','pickup','delivery','local_delivery_cost','vendor','vendorCode','sales_notes','manufacturer_warranty','country_of_origin','adult','barcode','cpa','param'),
			'arbitrary' =>		array('oldprice','market_category','picture','description','age','store','pickup','delivery','local_delivery_cost','vendorCode','sales_notes','manufacturer_warranty','country_of_origin','adult','barcode','cpa','param','downloadable','typePrefix','rec','expiry','weight','dimensions'),
			'book'		=>		array('oldprice','market_category','picture','description','age','store','pickup','delivery','local_delivery_cost','downloadable','author','publisher','series','year','ISBN','volume','part','language','binding','page_extent','table_of_contents'),
			'audiobook' =>		array('oldprice','market_category','picture','description','age','downloadable','author','publisher','series','year','ISBN','volume','part','language','table_of_contents','performed_by','performance_type','storage','format','recording_length'),
			'music' 	=>		array('oldprice','market_category','picture','description','age','store','pickup','delivery','barcode','year','media','artist'),
			'video' 	=>		array('oldprice','market_category','picture','description','age','store','pickup','delivery','adult','barcode','year','media','starring','director','originalName','country'),
			'tour' 		=>		array('oldprice','market_category','picture','description','age','store','pickup','delivery','country','worldRegion','region','dataTour','hotel_stars','room','meal','price_min','price_max','options'),
			'event' 	=>		array('oldprice','market_category','picture','description','age','store','pickup','delivery','hall','hall_part','is_premiere','is_kids'));

		$this->permitted 	= $p[$type];
	}


	public function id($id)
	{
		if(preg_match("/[^a-z,A-Z,0-9]/",$id)) 		throw new \RuntimeException("id должен содержать только латинские буквы и цифры");
		if( strlen($id)>20 )						throw new \RuntimeException("id длиннее 20 символов");
		$this->setAttribute('id',$id );
		return $this;
	}


	public function available($val=true)
	{
		if( !is_bool($val) )							throw new \RuntimeException("available должен быть boolean");
		$this->setAttribute('available',($val) ? 'true':'false' );
		return $this;
	}

	public function bid($bid)
	{
		if( !is_int($bid) )							throw new \RuntimeException("bid должен быть integer");
		$this->setAttribute('bid',$bid );
		return $this;
	}

	public function cbid($cbid)
	{
		if( !is_int($cbid) )							throw new \RuntimeException("cbid должен быть integer");
		$this->setAttribute('cbid',$cbid );
		return $this;
	}


	function __call($method, $args)
	{
		if( array_key_exists($method,$this->aliases) )	$method = $this->aliases[$method];

		if( !in_array($method, $this->permitted) )
			throw new \RuntimeException("$method вызван при типе товара {$this->type}");

		// значения, которые просто добавляем
		if( in_array($method, array('series','publisher','author','vendorCode','vendor','expiry','rec',
			'typePrefix','country_of_origin','market_category','local_delivery_cost','ISBN','volume','part','language','binding','table_of_contents','performed_by',
			'performance_type','storage','format','recording_length','artist','media','starring','director','originalName','country','worldRegion','region','dataTour'
			,'hotel_stars','room','meal','price_min','price_max','options','hall','hall_part','is_premiere','is_kids','oldprice')) )
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

	protected function _page_extent($args)
	{
		if( !is_int($args[0]) )		throw new \RuntimeException("page_extent должен содержать только цифры");
		if($args[0]<0)				throw new \RuntimeException("page_extent должен быть положительным числом");
		return $this->add('page_extent',$args[0]);
	}

	protected function _description($description)
	{
        // TODO no validation of tags
        $description = str_ireplace('<![CDATA[', '', $description[0]);
        $description = str_replace(']]>', '', $description);
        $description = '<![CDATA[' . $description . ']]>';
        return $this->addStr('description', $description, self::DESCRIPTION_MAX_LENGTH);
	}


	protected function _sales_notes($args)
	{
		return $this->addStr('sales_notes',$args[0],50);
	}


	protected function _age($args)
	{
		if( !is_int($args[0]))	throw new \RuntimeException("age должен иметь тип int");

		$ageEl 	= new \DomElement( 'age',$args[0] );
		$this->appendChild($ageEl);
		$ageEl->setAttribute('unit',$args[1] );

		switch ($args[1])
		{
			case 'year':
				if(!in_array($args[0],array(0,6,12,16,18)))
					throw new \RuntimeException("age при age_unit=year должен быть 0, 6, 12, 16 или 18");
				break;

			case 'month':
				if( ($args[0]<0)||($args[0]>12) )
					throw new \RuntimeException("age при age_unit=month должен быть 0<=age<=12");
				break;

			default:
					throw new \RuntimeException("age unit должен быть month или year");
				break;
		}
		return $this;
	}

	protected function _param($args)
	{
		$newEl 	= new \DomElement('param',$args[1]);
		$this->appendChild($newEl);
		$newEl->setAttribute('name', $args[0]);
		if( isset($args[2]) ) 	$newEl->setAttribute('unit', $args[2]);
		return $this ;
	}


	protected function _picture( $args )
	{
		$pics	= $this->getElementsByTagName('picture');
		if($pics->length >10)		throw new \RuntimeException("Можно использовать максимум 10 картинок");
		$this->addStr('picture',$args[0],512);
		return $this;
	}

	protected function _barcode($args)
	{
		if( !is_int($args[0]) )		throw new \RuntimeException("barcode должен содержать только цифры");
		$len 	= strlen($args[0]);
		if( !($len==8 || $len==12 || $len==13) ) throw new \RuntimeException("barcode должен содержать 8, 12 или 13 цифр");
		return $this->add('barcode',$args[0]);
	}


	protected function _year($args)
	{
		if( !is_int($args[0]) ) throw new \RuntimeException("year должен быть int");
		return $this->add('year',$args[0]);
	}


	protected function _dimensions($args)
	{
		if( !is_float($args[0]) || !is_float($args[1]) || !is_float($args[2]) )
			throw new \RuntimeException("dimensions должен быть float");
		return $this->add('dimensions',$args[0].'/'.$args[1].'/'.$args[2]);
	}


	protected function _weight($args)
	{
		if( !is_float($args[0]) ) throw new \RuntimeException("weight должен быть float");
		return $this->add('weight',$args[0]);
	}

	protected function _cpa($args)
	{
		if( !isset($args[0]) ) $args[0] = true;
		return $this->add('cpa', ($args[0])?'1':'0' );
	}

	public function addStr( $name,$val,$limit )
	{
		if( $limit && ( mb_strlen($val,"UTF-8")>$limit) )	throw new \RuntimeException("$name должен быть короче $limit символов");
		return $this->add( $name,$val );
	}

	public function add( $name,$val=false )
	{
		$newEl 	= ($val===false) ? new \DomElement($name) : new \DomElement($name,$val);
		$this->appendChild($newEl);
		return $this;
	}


}
