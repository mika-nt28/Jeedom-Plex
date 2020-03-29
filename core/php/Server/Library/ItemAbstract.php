<?php
abstract class Plex_Server_Library_ItemAbstract 
	extends Plex_Server_Library_SectionAbstract
	implements Plex_Server_Library_ItemInterface
{
	protected $allowSync;
	protected $librarySectionId;
	protected $viewGroup;
	protected $ratingKey;
	protected $grandparentKey;
	protected $parentRatingKey;
	protected $key;
	protected $type;
	protected $title;
	protected $titleSort;
	protected $summary;
	protected $index;
	protected $thumb;
   	protected $addedAt;
	protected $updatedAt;
	protected $media;
	
	const ENDPOINT_CHILDREN = 'children';
	const ENDPOINT_ALL_LEAVES = 'allLeaves';
	
	public function setAttributes($attribute)
	{
		if (isset($attribute['allowSync'])) {
			$this->setAllowSync($attribute['allowSync']);
		}
		if (isset($attribute['librarySectionID'])) {
			$this->setLibrarySectionId($attribute['librarySectionID']);
		}
		if (isset($attribute['viewGroup'])) {
			$this->setViewGroup($attribute['viewGroup']);
		}
		if (isset($attribute['parentRatingKey'])) {
			$this->setParentRatingKey($attribute['parentRatingKey']);
		}
		if (isset($attribute['grandparentKey'])) {
			$this->setGrandParentKey($attribute['grandparentKey']);
		}
		if (isset($attribute['ratingKey'])) {
			$this->setRatingKey($attribute['ratingKey']);
		}
		if (isset($attribute['key'])) {
			$this->setKey($attribute['key']);
		}
		if (isset($attribute['type'])) {
			$this->setType($attribute['type']);
		}
		if (isset($attribute['title'])) {
			$this->setTitle($attribute['title']);
		}
		if (isset($attribute['titleSort'])) {
			$this->setTitleSort($attribute['titleSort']);
		}
		if (isset($attribute['summary'])) {
			$this->setSummary($attribute['summary']);
		}
		if (isset($attribute['index'])) {
			$this->setIndex($attribute['index']);
		}
		if (isset($attribute['thumb'])) {
			$this->setThumb($attribute['thumb']);
		}
		if (isset($attribute['addedAt'])) {
			$this->setAddedAt($attribute['addedAt']);
		}
		if (isset($attribute['updatedAt'])) {
			$this->setUpdatedAt($attribute['updatedAt']);
		}
		if (isset($attribute['Media'])) {
			$this->setMedia($attribute['Media']);
		}
	}
	public function getItemByIndex($index)
	{
		// Since we 'hop' from the overridden 'getPolymorphicItem' method, we
		// have to extend the depth by one here to properly identify the calling
		// function.
		$itemType = $this->functionToType(
			$this->getCallingFunction(3)
		);
		
		// Find the get method and make sure it exists in the calling class.
		$getMethod = sprintf('get%ss', ucfirst($itemType));
		
		if (method_exists($this, $getMethod)) {
			foreach ($this->{$getMethod}() as $item) {
				if ($item->getIndex() === $index) {
					// So, this might seem a bit recursive, but there's method
					// to this madness. Once we have identified the correct item
					// by its key, we use the parent item retrieval system to
					// get the item by its rating key. We do this because Plex
					// limits the amount of data that comes back with an item
					// when you ask for more than one at a time. By asking for
					// for it singularly here, we guarantee we get the most data
					// back, like grandparent and parent keys and titles.
					return parent::getPolymorphicItem($item->getRatingKey());
				}
			}
		}
	}
	public function getPolymorphicItem($polymorphicData, $scopedToItem = FALSE)
	{
		// At the item level, instead of assuming an integer is a rating key, we
		// assume an integer is an index. This allows us to retrieve seasons,
		// episodes, and tracks by their number in sequence, which is a more
		// common way than by its Plex assigned rating key.
		if (is_int($polymorphicData)) {
			return $this->getItemByIndex($polymorphicData);
		} else {
			// If we're not retrieving by index, then we simply default to the
			// parent function, however, we scope it to 'item' so the calling
			// function is identified by the right depth and we use a 'get'
			// function to find the items instead of search.
			return parent::getPolymorphicItem($polymorphicData, TRUE);
		}
	}
	protected function buildChildrenEndpoint()
	{
		return sprintf(
			'%s/%d/%s',
			Plex_Server_Library::ENDPOINT_METADATA,
			$this->getRatingKey(),
			self::ENDPOINT_CHILDREN
		);
	}
	protected function buildAllLeavesEndpoint()
	{
		return sprintf(
			'%s/%d/%s',
			Plex_Server_Library::ENDPOINT_METADATA,
			$this->getRatingKey(),
			self::ENDPOINT_ALL_LEAVES
		);
	}
	public static function factory($type, $name, $address, $port)
	{
		$class = sprintf(
			'Plex_Server_Library_Item_%s',
			ucfirst($type)
		);
		
		return new $class($name, $address, $port);
	}
	public function doesAllowSync()
	{
		return (boolean) $this->allowSync;
	}
	public function setAllowSync($allowSync)
	{
		$this->allowSync = (boolean) $allowSync;
	}
	public function getLibrarySectionId()
	{
		return (int) $this->librarySectionId;
	}
	public function setLibrarySectionId($librarySectionId)
	{
		$this->librarySectionId = (int) $librarySectionId;
	}
	public function getGrandParentKey()
	{
		return (int) $this->grandparentKey;
	}
	public function setGrandParentKey($grandparentKey)
	{
		$this->grandparentKey = (int) $grandparentKey;
	}
	public function getParentRatingKey()
	{
		return (int) $this->parentRatingKey;
	}
	public function setParentRatingKey($parentRatingKey)
	{
		$this->parentRatingKey = (int) $parentRatingKey;
	}
	public function getRatingKey()
	{
		return (int) $this->ratingKey;
	}
	public function setRatingKey($ratingKey)
	{
		$this->ratingKey = (int) $ratingKey;
	}
	public function getKey()
	{
		return $this->key;
	}
	public function setKey($key)
	{
		$this->key = $key;
	}
	public function getViewGroup()
	{
		return $this->viewGroup;
	}
	public function setViewGroup($viewGroup)
	{
		$this->viewGroup = $viewGroup;
	}
	public function getType()
	{
		return $this->type;
	}
	public function setType($type)
	{
		$this->type = $type;
	}
	public function getTitle()
	{
		return $this->title;
	}
	public function setTitle($title)
	{
		$this->title = $title;
	}
	public function getTitleSort()
	{
		return $this->titleSort;
	}
	public function setTitleSort($titleSort)
	{
		$this->titleSort = $titleSort;
	}
	public function getSummary()
	{
		return $this->summary;
	}
	public function setSummary($summary)
	{
		$this->summary = $summary;
	}
	public function getIndex()
	{
		return (int) $this->index;
	}
	public function setIndex($index)
	{
		$this->index = (int) $index;
	}
	public function getThumb()
	{
		return $this->thumb;
	}
	public function setThumb($thumb)
	{
		$this->thumb = $thumb;
	}
	public function getAddedAt()
	{
		return $this->addedAt;
	}
	public function setAddedAt($addedAtTs)
	{
		$addedAt = new DateTime(sprintf('@%s', $addedAtTs));
		$this->addedAt = $addedAt;
	}
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}
	public function setUpdatedAt($updatedAtTs)
	{
		$updatedAt = new DateTime(sprintf('@%s', $updatedAtTs));
		$this->updatedAt = $updatedAt;
	}
	public function getMedia()
	{
		return $this->media;
	}
	public function setMedia($media)
	{
		$mediaObject = new Plex_Server_Library_Item_Media(reset($media));
		$this->media = $mediaObject;
	}
}
