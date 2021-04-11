<?php
namespace Taro\PageMaker\Core;

trait HelperTrait 
{
	private function h($string)
	{
		return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
	}	
}