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
			if ($host === 'overview') {
				$title = "$project cluster";
			} else {
				$title = $host;
			}
			$html .= '<optgroup label="' . htmlspecialchars($title) . '">';
			foreach ($graphConfigs as $graphID => &$graph) {
				$html .= '<option value="h_' . htmlspecialchars("{$host}_{$graphID}") . '">'
					. htmlspecialchars($graph['title'])
					. '</option>';
			}
			$html .= '</optgroup>';
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
			if ($hostName === 'overview') {
				$hostTitle = "$project cluster";
				$hostTarget = '*';
			} else {
				$hostTitle = $hostName;
				$hostTarget = $hostName;
			}
			$html .= '<h3 id="h_' . htmlspecialchars($hostName) . '">' . htmlspecialchars($hostTitle) . '</h3>';
			foreach ($graphConfigs as $graphID => &$graph) {
				$html .= '<h4 id="h_' . htmlspecialchars("{$hostName}_{$graphID}") . '">'
					. htmlspecialchars("$hostTitle {$graph['title']}")
					. '</h4>';

				if ($hostName === 'overview') {
					if (is_array($graph['overview'])) {
						$targets = $graph['overview'];
					} else {
						// Default graph for cluster overview: apply sum() to the values
						$targets = array_map(function ($target) use ($graph) {
							return preg_replace('/HOST([^\),]+)/', $graph['overview'] . '(HOST$1)', $target);
						}, $graph['targets']);
					}
				} else {
					$targets = $graph['targets'];
				}

				$targetQuery = '';
				foreach ($targets as $target) {
					$targetQuery .= '&target=' . urlencode(str_replace('HOST', "$project.$hostTarget", $target));
				}

				$html .= '<img width="800" height="250" src="//graphite.wmflabs.org/render/?'
					. htmlspecialchars(http_build_query(array(
						'title' => "$hostTitle {$graph['title']} last day",
						'width' => 800,
						'height' => 250,
						'from' => '-24h',
						'hideLegend' => 'false',
						'uniqueLegend' => 'true',
					)) . $targetQuery)
					. '">'
					. '<br><img width="400" height="250" src="//graphite.wmflabs.org/render/?'
					. htmlspecialchars(http_build_query(array(
						'title' => "$hostTitle {$graph['title']} last week",
						'width' => 400,
						'height' => 250,
						'from' => '-1week',
						'hideLegend' => 'false',
						'uniqueLegend' => 'true',
					)) . $targetQuery)
					. '">'
					. '<img width="400" height="250" src="//graphite.wmflabs.org/render/?'
					. htmlspecialchars(http_build_query(array(
						'title' => "$hostTitle {$graph['title']} last month",
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
