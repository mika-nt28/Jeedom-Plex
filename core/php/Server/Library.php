<?php
class Plex_Server_Library extends Plex_Server
{
	const ENDPOINT_LIBRARY = 'library';
	const ENDPOINT_SECTION = 'sections';
	const ENDPOINT_RECENTLY_ADDED = 'recentlyAdded';
	const ENDPOINT_ON_DECK = 'onDeck';
	const ENDPOINT_METADATA = 'metadata';
	const TYPE_MOVIE = 'movie';
	const TYPE_ARTIST = 'artist';
	const TYPE_ALBUM = 'album';
	const TYPE_TRACK = 'track';
	const TYPE_PHOTO = 'photo';
	const TYPE_SHOW = 'show';
	const TYPE_SEASON = 'season';
	const TYPE_EPISODE = 'episode';
	protected function buildUrl($endpoint)
	{
		$endpoint = sprintf(
			'%s/%s',
			self::ENDPOINT_LIBRARY,
			$endpoint
		);

		// Some of the polymorphic methods leave double slashes, so here we
		// simply clean them up.
		$endpoint = str_replace('///', '/', $endpoint);
		$endpoint = str_replace('//', '/', $endpoint);

		$url = sprintf(
			'%s/%s',
			$this->getBaseUrl(),
			$endpoint
		);
		
		return $url;
	}
	public function byMediaKey($key)
	{
		$key = str_replace('/library/', '/', $key);
		$items=$this->getItems($key);
		return $items;
	}
	public function getSectionByMediaKey($endpoint)
	{
		$endpoint = str_replace('/library/', '/', $endpoint);
		$endpoint = str_replace('/children', '', $endpoint);
		$media=$this->getItems($endpoint);
		if(is_array($media))
			$section=$media[0]->getLibrarySectionId();
		else
			$section=$media->getLibrarySectionId();
		return $this->getSectionByKey($section);
	}
	protected function getItems($endpoint)
	{
		$items = array();
		$itemArray = $this->makeCall($this->buildUrl($endpoint));
		
		foreach ($itemArray as $attribute) {
			// Not all attributes at this point have a 'type.' Sometimes they
			// represent a different sort of list like 'All episodes.' In this
			// case we skip it by checking the integrity of the 'type' index. 
			// If there is no type index then it is not an item.
			if (isset($attribute['type'])) {
				$item = Plex_Server_Library_ItemAbstract::factory(
					$attribute['type'],
					$this->name,
					$this->address,
					$this->port
				);
				$item->setAttributes($attribute);
				$items[] = $item;
			}
		}
		return $items;
	}
	public function functionToType($function)
	{
		$availableTypes = array(
			self::TYPE_MOVIE,
			self::TYPE_ARTIST,
			self::TYPE_ALBUM,
			self::TYPE_TRACK,
			self::TYPE_PHOTO,
			self::TYPE_SHOW,
			self::TYPE_SEASON,
			self::TYPE_EPISODE
		);
		
		foreach ($availableTypes as $type) {
			if (strpos(strtolower($function), $type) != FALSE) {
				return $type;
			}
		}
	}
	public function getSections()
	{
		$sections = array();
		$sectionArray = $this->makeCall(
			$this->buildUrl(self::ENDPOINT_SECTION)
		);
		
		foreach ($sectionArray as $attribute) {
			$section = Plex_Server_Library_SectionAbstract::factory(
				$attribute['type'],
				$this->name,
				$this->address,
				$this->port
			);
			$section->setAttributes($attribute);

			$sections[] = $section;
		}
		
		return $sections;
	}
	
	public function getSectionByKey($key)
	{
		foreach ($this->getSections() as $section) {
			if ($section->getKey() == $key) {
				return $section;
			}
		}

		throw new Plex_Exception_Server_Library(
			'RESOURCE_NOT_FOUND',
			array('section', $key)
		);
	}
	public function getSection($polymorphicData)
	{
		// If we have an integer we are getting the section by key.
		if (is_int($polymorphicData)) {
			foreach ($this->getSections() as $section) {
				if ($section->getKey() == $polymorphicData) {
					return $section;
				}
			}
		// If we have a string we are getting the section by title.
		} else if (is_string($polymorphicData)) {
			foreach ($this->getSections() as $section) {
				if ($section->getTitle() == $polymorphicData) {
					return $section;
				}
			}
		}

		throw new Plex_Exception_Server_Library(
			'RESOURCE_NOT_FOUND',
			array('section', $polymorphicData)
		);
	}
	public function getRecentlyAddedItems()
	{
		return $this->getItems(self::ENDPOINT_RECENTLY_ADDED);
	}
	public function getOnDeckItems()
	{
		return $this->getItems(self::ENDPOINT_ON_DECK);
	}
}
