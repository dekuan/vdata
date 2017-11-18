<?php

namespace dekuan\vdata;


class CVDataException extends \Exception
{
	protected $m_arrInfo;
	protected $m_sMessage;

	function __construct( $nErrorId, $sErrorDesc = '', $arrVData = [], $sVersion = '1.0' )
	{
		$this->m_arrInfo	= CVData::GetInstance()->GetVDataArray( $nErrorId, $sErrorDesc, $arrVData, $sVersion );
		$this->m_sMessage	= sprintf
		(
			"%d: %s, %s",
			$this->m_arrInfo[ 'errorid' ],
			$this->m_arrInfo[ 'errordesc' ],
			@ json_encode( $this->m_arrInfo[ 'vdata' ] )
		);
		
		parent::__construct( $this->m_sMessage );
	}

	public function getErrorId()
	{
		return isset( $this->m_arrInfo[ 'errorid' ] ) ? $this->m_arrInfo[ 'errorid' ] : CConst::ERROR_UNKNOWN;
	}

	public function getErrorDesc()
	{
		return isset( $this->m_arrInfo[ 'errordesc' ] ) ? $this->m_arrInfo[ 'errordesc' ] : '';
	}

	public function getVData()
	{
		return isset( $this->m_arrInfo[ 'vdata' ] ) ? $this->m_arrInfo[ 'vdata' ] : [];
	}

	public function getVersion()
	{
		return isset( $this->m_arrInfo[ 'version' ] ) ? $this->m_arrInfo[ 'version' ] : '';
	}
}