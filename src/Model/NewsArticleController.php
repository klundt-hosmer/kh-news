<?php

namespace micahsheets\khnews\Model;

use PageController;

class NewsArticleController extends PageController {
  
  	function Author()
	{
		return $this->Author;
	}

	function Source()
	{
		return $this->Source;
	}
  
}
