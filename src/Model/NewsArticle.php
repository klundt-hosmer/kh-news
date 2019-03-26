<?php

namespace micahsheets\Model;

use Page;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\DataObject;
use SilverStripe\AssetAdmin\Forms\UploadField;
use micahsheets\Model\NewsHolder;

class NewsArticle extends Page {

	private static $table_name = 'NewsArticle';

	private static $icon = 'micahsheets/silverstripe-news:client/images/newspaper-file.gif';

	private static $db = [
		'Summary' => 'HTMLText',
		'Author' => 'Varchar(128)',
		'OriginalPublishedDate' => 'Date',
		'ExternalURL' => 'Varchar(255)',
		'Source' => 'Varchar(128)',
	];
	/**
	 * The InternalFile is used when the news article is mostly contained in a file based item -
	 * if this is set, then the URL to the item is returned in the call to "Link" for this asset.
	 *
	 * @var array
	 */
	private static $has_one = [
		'InternalFile' => File::class,
		'NewsSection' => NewsHolder::class,
		'Thumbnail' => Image::class,
	];

//	public function getCMSFields() {
//		$fields = parent::getCMSFields();
//
//		$fields->addFieldToTab('Root.Main', TextField::create('Author', _t('NewsArticle.AUTHOR', 'Author')), 'Content');
//		$fields->addFieldToTab('Root.Main', $dp = DateField::create('OriginalPublishedDate', _t('NewsArticle.PUBLISHED_DATE', 'When was this article first published?')), 'Content');
//
//		//$dp->setConfig('showcalendar', true);
//
//		$fields->addFieldToTab('Root.Main', TextField::create('ExternalURL', _t('NewsArticle.EXTERNAL_URL', 'External URL to article (will automatically redirect to this URL if no article content set)')), 'Content');
//		$fields->addFieldToTab('Root.Main', TextField::create('Source', _t('NewsArticle.SOURCE', 'News Source')), 'Content');
//
//		$fields->addFieldToTab('Root.Main', $if = UploadField::create('Thumbnail', _t('NewsArticle.THUMB', 'Thumbnail')), 'Content');
//		$if->setAllowedMaxFileNumber(1)->setFolderName('news-articles/thumbnails');
//		$if->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
//
//		if (!$this->OriginalPublishedDate) {
//			// @TODO Fix this to be correctly localized!!
//			$this->OriginalPublishedDate = date('Y-m-d');
//		}
//
//		$fields->addFieldToTab('Root.Main', UploadField::create('InternalFile', _t('NewsArticle.INTERNAL_FILE', 'Select a file containing this news article, if any'))->setFolderName('news'), 'Content');
//		$fields->addFieldToTab('Root.Main', $summary = HTMLEditorField::create('Summary', _t('NewsArticle.SUMMARY', 'Article Summary (displayed in listings)')), 'Content');
//		$summary->addExtraClass('stacked');
//
//		$this->extend('updateArticleCMSFields', $fields);
//
//		return $fields;
//	}

	/**
	 * When the article is saved, and this article's section dictates that it
	 * needs to be filed, then do so
	 */
	public function onBeforeWrite() {
		parent::onBeforeWrite();

		// dummy initial date
		if (!$this->OriginalPublishedDate) {
			// @TODO Fix this to be correctly localized!!
			$this->OriginalPublishedDate = date('Y-m-d 12:00:00');
		}

		$parent = $this->Parent();

		// just in case we've been moved, update our section
		$section = $this->findSection();
		$this->NewsSectionID = $section->ID;

		$newlyCreated = $section->ID == $parent->ID;
		$changedPublishDate = $this->isChanged('OriginalPublishedDate', 2);

		if (($changedPublishDate || $newlyCreated) && ($section->AutoFiling || $section->FilingMode)) {
			if (!$this->Created) {
				$this->Created = date('Y-m-d H:i:s');
			}
			$pp = $this->PartitionParent();
			if ($pp->ID != $this->ParentID) {
				$this->ParentID = $pp->ID;
			}
		}

	}

//	/**
//	 * Make sure all parents are published when publishing a news article
//	 */
//	public function onBeforePublish() {
//		// go through all parents that are news holders and publish them if they haven't been
//		$this->publishSection();
//	}
//
//	public function onAfterPublish() {
//		// $this->publishSection();
//	}
//
//	/**
//	 * Ensure's the section is published.
//	 *
//	 * We need to do it both before and after publish because of some quirks with
//	 * folders not existing on one but existing on the other depending on the order of
//	 * writing the objects
//	 */
//	protected function publishSection() {
//		$parent = NewsHolder::get()->byID($this->ParentID);
//		while ($parent && $parent instanceof NewsHolder) {
//			if (!$parent->isPublished()) {
//				$parent->doPublish();
//			}
//			$parent = $parent->Parent();
//		}
//	}
//
//	/**
//	 * Get the top level parent of this article that is marked as a section
//	 *
//	 *  @return NewsHolder
//	 */
//	public function Section() {
//		if ($this->NewsSectionID) {
//			return $this->NewsSection();
//		}
//
//		$section = $this->findSection();
//		return $section;
//	}
//
//	/**
//	 * Find the section this news article is currently in, based on ancestor pages
//	 */
//	public function findSection() {
//		if ($this->ParentID && $this->Parent() instanceof NewsHolder) {
//			return $this->Parent()->findSection();
//		}
//		return $this;
//	}
//
//	/**
//	 * Gets the parent for this article page based on its section, and its
//	 * creation date
//	 */
//	public function PartitionParent() {
//		$section = $this->findSection();
//		$holder = $section->getPartitionedHolderForArticle($this);
//		return $holder;
//	}
//
//	/**
//	 * Indicates if this has an external URL link
//	 *
//	 * @return boolean
//	 */
//	public function HasExternalLink() {
//		return strlen($this->ExternalURL) || $this->InternalFileID;
//	}
//
//	/**
//	 * Link to the news article. If it has an external URL set, or a file, link to that instead.
//	 *
//	 * @param String $action
//	 * @return String
//	 */
//	public function Link($action='') {
//		if (strlen($this->ExternalURL) && !strlen($this->Content)) {
//			// redirect away
//			return $this->ExternalURL;
//		}
//		if ($this->InternalFile()->ID) {
//			$file = $this->InternalFile();
//			return $file->Link($action);
//		}
//		return parent::Link($action);
//	}
//
//
//	/**
//	 * Pages to update cache file for static publisher
//	 *
//	 */
//	public function pagesAffectedByChanges() {
//		$parent = $this->Parent();
//		$urls 	= array($this->Link());
//
//		// add all parent (holders)
//		while($parent && $parent->ParentID > -1 && $parent instanceof NewsHolder) {
//			$urls[] = $parent->Link();
//			$parent = $parent->Parent();
//		}
//
//		$this->extend('updatePagesAffectedByChanges', $urls);
//
//		return $urls;
//	}

}