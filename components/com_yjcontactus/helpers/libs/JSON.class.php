<?php
	
	class JSON
	{
		//public $result;
		var $result = '';
		/**
		 * Class constructor
		 *
		 * @param array $array
		 */
		//public function __construct( $array )
		function JSON( $array )
		{
			$this->json_encode($array);			
		}
		/**
		 * Json string builder. It calls itself if array value in key=>value pair is an array.
		 * It cand generate JSON strings from multidimensional array
		 *
		 * @param array $array
		 * @param string $separator
		 * @return void
		 */
		//private function json_encode( $array = array(), $separator = '' )
		function json_encode( $array = array(), $separator = '' )		
		{
			$this->result .= '{';
			
			$pairs = array();
			foreach ($array as $key=>$value) 
			{
				if( is_array( $value ) )
				{
					$last_key = end( array_keys( $array ) );
					
					$this->result .= '"'.$key.'":';
					$this->json_encode( $value , ($key == $last_key ? '':',') );
				}
				else
					$pairs[] = '"'.$key.'":"'.$value.'"'; 
			}
			$this->result .= implode(',',$pairs).'}'.$separator;
		}
		
	}
?>