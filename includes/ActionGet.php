<?php

namespace ExampleAPI;

/**
* Handles basic SQL Select by ID
*/
class ActionGet implements IAction
{
	/**
	* @var string ID for getting an item(s)
	*/
	protected $id;
	
	/**
	* Constructor
	* @param string $tID ID for getting an item(s)
	*/
	public function __construct( $tID )
	{
		$this->id = $tID;
	}
	
	/**
	* Executes an SQL query to get an item(s) using an input ID
	* @param IAPI $tAPI API that called this function
	* @param IRoute $tRoute Route that called this function
	*/
	public function execute( IAPI $tAPI, IRoute $tRoute )
	{
		$tempConnection = null;
		if ( $tAPI->getConnection()->tryConnect( $tAPI, $tempConnection ) )
		{
			$tempQueryResult = $tempConnection->query( "SELECT * FROM " . $tRoute->table . " WHERE " . $tRoute->IDColumn . "=" . $this->id );
			if ( $tempQueryResult )
			{
				$tempListLength = $tempQueryResult->num_rows;
				if ( $tempListLength > 1 ) // won't ever happen if id's are unique
				{
					$tempList = [];
					for ( $i = 0; $i < $tempListLength; ++$i )
					{
						$tempList[] = $tempQueryResult->fetch_assoc();
					}
					
					$tAPI->getOutput()->data( $tempList );
				}
				else if ( $tempListLength == 1 )
				{
					$tAPI->getOutput()->data( $tempQueryResult->fetch_assoc() );
				}
				else
				{
					$tAPI->getOutput()->error( 204, "no records found" );
				}
			}
			else
			{
				$tAPI->getOutput()->error( 500, $tempConnection->error );
			}
			
			$tempConnection->close();
		}
	}
}

?>