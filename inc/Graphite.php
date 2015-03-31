<?php
class Graphite {
	/**
	 * @return array
	 */
	public static function getProjects() {
		$json = WebCache::get(
			'wikitech-v1-projects',
			'https://wikitech.wikimedia.org/w/api.php?format=json&'
			. http_build_query(array(
				'action' => 'query',
				'list' => 'novaprojects',
			))
		);
		$data = json_decode($json);
		if (!isset($data->query->novaprojects)) {
			return array();
		}
		sort($data->query->novaprojects);
		return $data->query->novaprojects;
	}

	/**
	 * @param string $project
	 * @return array
	 */
	public static function getHostsForProject($project) {
		$json = WebCache::get(
			'wikitech-v1-' . $project,
			'https://wikitech.wikimedia.org/w/api.php?format=json&'
			. http_build_query(array(
				'action' => 'query',
				'list' => 'novainstances',
				// TODO: Don't hardcode eqiad (https://phabricator.wikimedia.org/T94514)
				'niregion' => 'eqiad',
				'niproject' => $project,
			))
		);
		$data = json_decode($json);
		if (!isset($data->query->novainstances)) {
			return array();
		}
		$instances = array_map(function ($obj) {
			return $obj->name;
		}, $data->query->novainstances);
		sort($instances);
		return $instances;
	}
}
