<?php
class Graphite {
	/**
	 * @return array
	 */
	public static function getProjects() {
		$json = WebCache::get(
			'graphite-index',
			'http://graphite.wmflabs.org/metrics/find?query=*'
		);
		$data = json_decode($json);
		$projects = array_map(function ($obj) {
			return $obj->id;
		}, $data);
		return $projects;
	}

	/**
	 * @param string $project
	 * @return array
	 */
	public static function getHostsForProject($project) {
		$json = WebCache::get(
			'graphite-project-' . $project,
			'http://graphite.wmflabs.org/metrics/find?'
			. http_build_query(array('query' => "$project.*"))
		);
		$data = json_decode($json);
		$projects = array_map(function ($obj) {
			return $obj->text;
		}, $data);
		return $projects;
	}
}
