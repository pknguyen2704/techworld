<?php

namespace Modules\NewsFeed\Block;

use Magento\Framework\View\Element\Template;

class NewsFeedBlock extends Template
{
	public function getText()
	{
		return "News Feed test";
	}

	public function __construct(\Magento\Framework\View\Element\Template\Context $context)
	{
		parent::__construct($context);
	}
}