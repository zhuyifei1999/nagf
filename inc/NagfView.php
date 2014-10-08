<?php
class NagfView {
	protected $data;

	public function __construct(stdClass $data) {
		$this->data = $data;
	}

	/**
	 * @return string HTML
	 */
	public function getProjectMenu() {
		$data = $this->data;
		$projects = Graphite::getProjects();
		return '<select name="project" required class="form-control nagf-select-project">'
			. '<option value="" disabled>Select project</option>'
			. implode('', array_map(function ($project) use ($data) {
				return '<option'
					. ($project === $data->project ? ' selected' : '')
					. '>' . htmlspecialchars($project) . '</option>';
			}, $projects))
			. '</select>';
	}

	/**
	 * @return string HTML
	 */
	protected function getHostMenu() {
		$project = $this->data->project;
		$hosts = $this->data->hosts;
		$graphConfigs = $this->data->hostGraphsConfig;

		$html = '<select required class="form-control nagf-select-metric">'
			. '<option value="" disabled>Select metric</option>';

		array_unshift($hosts, 'overview');
		foreach ($hosts as $host) {
			$html .= '<option value="h_' . htmlspecialchars($host) . '">' .  htmlspecialchars($host) . '</option>';
			foreach ($graphConfigs as $graphID => &$graph) {
				$html .= '<option value="h_' . htmlspecialchars("{$host}_{$graphID}") . '">'
					. htmlspecialchars("$host: {$graph['title']}")
					. '</option>';
			}
		}
		$html .= '</select>';
		return $html;
	}

	/**
	 * @return string HTML
	 */
	public function getHostForm() {
		if (!$this->data->project) {
			return '';
		}
		return '<form class="navbar-form navbar-right only-js" role="form">'
			. '<div class="form-group">'
			. $this->getHostMenu(
				$this->data->project,
				$this->data->hosts,
				$this->data->hostGraphsConfig
			)
			. '</div>'
			. '</form>';
	}

	/**
	 * @return string HTML
	 */
	public function getPage() {
		if ($this->data->project) {
			return $this->getProjectPage(
				$this->data->project,
				$this->data->hosts,
				$this->data->hostGraphsConfig
			);
		}
		return $this->getHomePage();
	}

	/**
	 * @param string $project
	 * @param Array $hosts
	 * @return string HTML
	 */
	protected function getProjectPage($project, Array $hosts, Array $graphConfigs) {
		$html = '<h1>' . htmlspecialchars($project) . '</h1>';

		array_unshift($hosts, 'overview');

		$sections = array();
		foreach ($hosts as $hostName) {
			$html .= '<h3 id="h_' . htmlspecialchars($hostName) . '">' . htmlspecialchars($hostName) . '</h3>';
			foreach ($graphConfigs as $graphID => &$graph) {
				$html .= '<h4 id="h_' . htmlspecialchars("{$hostName}_{$graphID}") . '">'
					. htmlspecialchars("$hostName: {$graph['title']}")
					. '</h4>';
				$targetQuery = '';

				if ($hostName !== 'overview') {
					$hostTarget = $hostName;
					$targets = $graph['targets'];
				} else {
					$hostTarget = '*';
					if (isset($graph['overview'])) {
						$targets = $graph['overview'];
					} else {
						// Default overview: sum() the source values
						$targets = array_map(function ($target) {
							return preg_replace('/HOST([^\),]+)/', 'sum(HOST$1)', $target);
						}, $graph['targets']);
					}
				}

				foreach ($targets as $target) {
					$targetQuery .= '&target=' . urlencode(str_replace('HOST', "$project.$hostTarget", $target));
				}

				$html .= '<img width="800" height="250" src="//graphite.wmflabs.org/render/?'
					. htmlspecialchars(http_build_query(array(
						'title' => $graph['title'] . ' last day',
						'width' => 800,
						'height' => 250,
						'from' => '-24h',
						'hideLegend' => 'false',
						'uniqueLegend' => 'true',
					)) . $targetQuery)
					. '">'
					. '<br><img width="400" height="250" src="//graphite.wmflabs.org/render/?'
					. htmlspecialchars(http_build_query(array(
						'title' => $graph['title'] . ' last week',
						'width' => 400,
						'height' => 250,
						'from' => '-1week',
						'hideLegend' => 'false',
						'uniqueLegend' => 'true',
					)) . $targetQuery)
					. '">'
					. '<img width="400" height="250" src="//graphite.wmflabs.org/render/?'
					. htmlspecialchars(http_build_query(array(
						'title' => $graph['title'] . ' last month',
						'width' => 400,
						'height' => 250,
						'from' => '-1month',
						'hideLegend' => 'false',
						'uniqueLegend' => 'true',
					)) . $targetQuery)
					. '">';
			}
		}

		return $html;
	}

	/**
	 * @return string HTML
	 */
	protected function getHomePage() {
		return '<blockquote>Not another Graphite frontend.</blockquote>'
			. '<p>Select a project from the menu to view the relevant monitoring graphs.</p>';
	}
}
