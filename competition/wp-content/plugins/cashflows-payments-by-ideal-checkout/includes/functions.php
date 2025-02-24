<?php

	if(!defined('ICCF_FUNCTIONS_LOADED'))
	{
		define('ICCF_FUNCTIONS_LOADED', true);
	}

	function iccf_isFolder($sPath)
	{
		if(file_exists($sPath))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	function iccf_loadFilesFromFolder($sPath, $sExtraExtension = '') // From ICCF_ROOT_PATH
	{
		if(iccf_isFolder($sPath))
		{
			$bFileFound = false;

			$aFiles = scandir($sPath);

			foreach($aFiles as $sFile)
			{
				if(strpos($sFile, $sExtraExtension . '.php') > 0)
				{
					$bFileFound = true;
					require_once($sPath . DIRECTORY_SEPARATOR . $sFile);
				}
			}

			if($bFileFound)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function iccf_log($xData, $sFile = '', $sLine = '', $sFileName = '')
	{
		if(is_array($xData))
		{
			$sData = json_encode($xData);
		}
		else
		{
			$sData = $xData;
		}

		if(empty($sFileName))
		{
			$sFileName = 'debug';
		}

		$sFilePathName = ICCF_ROOT_PATH . DIRECTORY_SEPARATOR . 'temp'. DIRECTORY_SEPARATOR . 'logs'. DIRECTORY_SEPARATOR . $sFileName . 'log';

		if(!empty($sFile))
		{
			error_log('FILE: ' . $sFile . '\n', 3, $sFilePathName);
		}

		if(!empty($sLine))
		{
			error_log('LINE: ' . $sLine . '\n', 3, $sFilePathName);
		}

		error_log('TIME: ' . date('c') . '\nDEBUG: ' . $sData . '\n', 3, $sFilePathName);
	}

	function idealcheckout_doHttpRequest($sUrl, $sPostData = '', $bRemoveHeaders = false, $iTimeout = 30, $bDebug = false, $aAdditionalHeaders = false)
	{
		// Setup the arguments
		$aArguments = array();
		$aArguments['timeout'] = $iTimeout;
		$aArguments['redirection'] = '5';
		$aArguments['httpversion'] = '1.1';

		if(isset($_SERVER['HTTP_USER_AGENT'])) {
			$aArguments['user-agent'] = $_SERVER['HTTP_USER_AGENT'];
		} else {
			$aArguments['user-agent'] = 'cashflowsWoocommerce/Plugin';
		}

		$aArguments['blocking'] = true;
		$aArguments['headers'] = $aAdditionalHeaders;
		$aArguments['cookies'] = array();

		if(!empty($sPostData))
		{
			$aArguments['body'] = $sPostData;

			$oResponse = wp_remote_post($sUrl, $aArguments);
		}
		else
		{
			$oResponse = wp_remote_get($sUrl, $aArguments);
		}

		$sResponse = wp_remote_retrieve_body($oResponse);

		if(empty($sResponse))
		{
			return '';
		}

		return $sResponse;
	}

?>