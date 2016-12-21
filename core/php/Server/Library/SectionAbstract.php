<?php
abstract class Plex_Server_Library_SectionAbstract extends Plex_Server_Library
{
	protected $art;
	protected $refreshing;
	protected $key;
	protected $type;
	protected $title;
	protected $agent;
	protected $scanner;
	protected $language;
	protected $uuid;
	protected $updatedAt;
	protected $createdAt;
	const ENDPOINT_CATEGORY_ALL = 'all';
	const ENDPOINT_CATEGORY_UNWATCHED = 'unwatched';
	const ENDPOINT_CATEGORY_NEWEST = 'newest';
	const ENDPOINT_CATEGORY_RECENTLY_ADDED = 'recentlyAdded';
	const ENDPOINT_CATEGORY_RECENTLY_VIEWED = 'recentlyViewed';
	const ENDPOINT_CATEGORY_ON_DECK = 'onDeck';
	const ENDPOINT_CATEGORY_COLLECTION = 'collection';
	const ENDPOINT_CATEGORY_FIRST_CHARACTER = 'firstCharacter';
	const ENDPOINT_CATEGORY_GENRE = 'genre';
	const ENDPOINT_CATEGORY_YEAR = 'year';
	const ENDPOINT_SEARCH = 'search';
	const SEARCH_TYPE_MOVIE = 1;
	const SEARCH_TYPE_SHOW = 2;
	const SEARCH_TYPE_EPISODE = 4;
	const SEARCH_TYPE_ARTIST = 8;
	const SEARCH_TYPE_TRACK = 10;
	public function setAttributes($attribute)
	{
		if (isset($attribute['art'])) {
			 $this->setArt($attribute['art']);
		}
		if (isset($attribute['refreshing'])) {
			$this->setRefreshing($attribute['refreshing']);
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
		if (isset($attribute['agent'])) {
			$this->setAgent($attribute['agent']);
		}
		if (isset($attribute['scanner'])) {
			$this->setScanner($attribute['scanner']);
		}
		if (isset($attribute['language'])) {
			$this->setLanguage($attribute['language']);
		}
		if (isset($attribute['uuid'])) {
			$this->setUuid($attribute['uuid']);
		}
		if (isset($attribute['updatedAt'])) {
			$this->setUpdatedAt($attribute['updatedAt']);
		}
		if (isset($attribute['createdAt'])) {
			$this->setCreatedAt($attribute['createdAt']);
		}
	}
	protected function buildEndpoint($endpoint)
	{
		return sprintf(
			'%s/%d/%s',
			Plex_Server_Library::ENDPOINT_SECTION,
			$this->getKey(),
			$endpoint
		);
	}
	protected function buildSearchEndpoint($type, $query)
	{
		$parameters = array(
			'type' => $type,
			'query' => trim($query)
		);
		
		$endpoint = sprintf(
			'%s?%s',
			self::ENDPOINT_SEARCH,
			http_build_query($parameters)
		);
		
		return $this->buildEndpoint($endpoint);
	}
	protected function getAllItems()
	{
		return $this->getItems(
			$this->buildEndpoint(self::ENDPOINT_CATEGORY_ALL)
		);
	}
	protected function getUnwatchedItems()
	{
		return $this->getItems(
			$this->buildEndpoint(self::ENDPOINT_CATEGORY_UNWATCHED)
		);
	}
	protected function getNewestItems()
	{
		return $this->getItems(
			$this->buildEndpoint(self::ENDPOINT_CATEGORY_NEWEST)
		);
	}
	protected function getRecentlyAddedSectionItems()
	{
		return $this->getItems(
			$this->buildEndpoint(self::ENDPOINT_CATEGORY_RECENTLY_ADDED)
		);
	}
	protected function getOnDeckSectionItems()
	{
		return $this->getItems(
			$this->buildEndpoint(self::ENDPOINT_CATEGORY_ON_DECK)
		);
	}
	protected function getRecentlyViewedItems()
	{
		return $this->getItems(
			$this->buildEndpoint(self::ENDPOINT_CATEGORY_RECENTLY_VIEWED)
		);
	}
	protected function getItemsByCollection($collectionKey)
	{
		return $this->getItems(
			$this->buildEndpoint(
				sprintf(
					'%s/%d',
					self::ENDPOINT_CATEGORY_COLLECTION,
					$collectionKey
				)
			)
		);
	}
	protected function getItemsByFirstCharacter($character)
	{
		return $this->getItems(
			$this->buildEndpoint(
				sprintf(
					'%s/%s',
					self::ENDPOINT_CATEGORY_FIRST_CHARACTER,
					$character
				)
			)
		);
	}
	protected function getItemsByGenre($genreKey)
	{
		return $this->getItems(
			$this->buildEndpoint(
				sprintf(
					'%s/%d',
					self::ENDPOINT_CATEGORY_GENRE,
					$genreKey
				)
			)
		);
	}
	protected function getItemsByYear($year)
	{
		return $this->getItems(
			$this->buildEndpoint(
				sprintf(
					'%s/%d',
					self::ENDPOINT_CATEGORY_YEAR,
					$year
				)
			)
		);
	}
	protected function getPolymorphicItem($polymorphicData, $scopedToItem = FALSE)
	{
		if (is_int($polymorphicData)) {
			// If we have an integer then we can assume we have a rating key.
			if ($item = reset(
				$this->getItems(
					sprintf(
						'%s/%d',
						Plex_Server_Library::ENDPOINT_METADATA,
						$polymorphicData
					)
				)
			)) {
				return $item;
			}

			throw new Plex_Exception_Server_Library(
				'RESOURCE_NOT_FOUND',
				array('item', $polymorphicData)
			);

		} else if (strpos($polymorphicData, Plex_Server_Library::ENDPOINT_METADATA)
			!== FALSE) {
			// If the single item endpoint appears in the polymorphic data then
			// is assumed we are dealing with a key, which is already a valid
			// endpoint.
			
			// A key will contain the library endpoint, which will be added back
			// by our getItems method, so we strip it out here. The buildUrl
			// method will handle stripping out and double slashes caused by
			// this.
			$endpoint = str_replace(
				Plex_Server_Library::ENDPOINT_LIBRARY, 
				'',
				$polymorphicData
			);
			
			if ($item = reset($this->getItems($endpoint))) {
				return $item;
			}

			throw new Plex_Exception_Server_Library(
				'RESOURCE_NOT_FOUND',
				array('item', $polymorphicData)
			);

		} else {
			// If we don't have a rating key or a key then we just assume we're
			// doing an exact title match.
			
			// If we are scoped to item it means an item is trying to retrieve
			// children or grandchildren. This has two implications. We can't
			// search at this level, so we have to "get" then loop/match/return.
			// It also means that to get the calling function we have to change
			// the depth as there is an extra function inbetween.
			$depth = 2;
			$functionType = 'search';
			
			if ($scopedToItem) {
				$depth = 3;
				$functionType = 'get';
			}
			
			// Find the item type.
			$itemType =  $this->functionToType(
				$this->getCallingFunction($depth)
			);
			
			// Find the search method and make sure it exists in the search
			// class.
			$searchMethod = sprintf('%s%ss', $functionType, ucfirst($itemType));
			
			if (method_exists($this, $searchMethod)) {
				foreach ($this->{$searchMethod}($polymorphicData) as $item) {
					if ($item->getTitle() === $polymorphicData) {
						if ($scopedToItem) {
							// So, this might seem a bit recursive, but there's
							// method to this madness. If we are scoped to an 
							// item and we have identified the item by its 
							// title, we have to refetch the item singularly by 
							// its rating key. We do this because we have used a
							// "get" method to find this item instead of a 
							// "search" method and Plex limits the amount of 
							// data that comes back with an item when you ask
							// for more than one at a time. By asking for it 
							// singularly here, we guarantee we get the most 
							// data back, like grandparent and parent keys and 
							// titles.
							return self::getPolymorphicItem(
								$item->getRatingKey()
							);
						} else {
							return $item;
						}
					}
				}
			}
			
			// Tried to do an exact title match and came up empty.
			throw new Plex_Exception_Server_Library(
				'RESOURCE_NOT_FOUND',
				array('item', $polymorphicData)
			);
		}
	}
	public function getCollections()
	{
		return $this->makeCall(
			$this->buildUrl(
				$this->buildEndpoint(self::ENDPOINT_CATEGORY_COLLECTION)
			)
		);
	}
	public function getGenres()
	{
		return $this->makeCall(
			$this->buildUrl(
				$this->buildEndpoint(self::ENDPOINT_CATEGORY_GENRE)
			)
		);
	}
	public static function factory($type, $name, $address, $port)
	{
		$class = sprintf(
			'Plex_Server_Library_Section_%s',
			ucfirst($type)
		);
		
		return new $class($name, $address, $port);
	}
	public function getArt()
	{
		return $this->art;
	}
	public function setArt($art)
	{
		$this->art = $art;
	}
	public function isRefreshing()
	{
		return (boolean) $this->refreshing;
	}
	public function setRefreshing($refreshing)
	{
		$this->refreshing = (boolean) $refreshing;
	}
	public function getKey()
	{
		return (int) $this->key;
	}
	public function setKey($key)
	{
		$this->key = (int) $key;
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
	public function getAgent()
	{
		return $this->agent;
	}
	
	public function setAgent($agent)
	{
		$this->agent = $agent;
	}
	public function getScanner()
	{
		return $this->scanner;
	}
	public function setScanner($scanner)
	{
		$this->scanner = $scanner;
	}
	
	public function getLanguage()
	{
		return $this->language;
	}
	public function setLanguage($language)
	{
		$this->language = $language;
	}
	public function getUuid()
	{
		return $this->uuid;
	}
	
	public function setUuid($uuid)
	{
		$this->uuid = $uuid;
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
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	public function setCreatedAt($createdAtTs)
	{
		$createdAt = new DateTime(sprintf('@%s', $createdAtTs));
		$this->createdAt = $createdAt;
	}
}
