<?php

	/**
	 * Dashboard class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Dashboard class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGDashboardView extends TBGIdentifiableClass
	{
		
		const VIEW_PREDEFINED_SEARCH = 1;
		const VIEW_SAVED_SEARCH = 2;
		const VIEW_LOGGED_ACTION = 3;
		const VIEW_LAST_COMMENTS = 4;
		const VIEW_FRIENDS = 5;
		const VIEW_PROJECTS = 6;
		const VIEW_MILESTONES = 7;
		
		const VIEW_PROJECT_INFO = 101;
		const VIEW_PROJECT_TEAM = 102;
		const VIEW_PROJECT_CLIENT = 103;
		const VIEW_PROJECT_SUBPROJECTS = 104;
		const VIEW_PROJECT_LAST15 = 105;
		const VIEW_PROJECT_STATISTICS_PRIORITY = 106;
		const VIEW_PROJECT_STATISTICS_STATUS = 111;
		const VIEW_PROJECT_STATISTICS_RESOLUTION = 112;
		const VIEW_PROJECT_STATISTICS_STATE = 113;
		const VIEW_PROJECT_STATISTICS_CATEGORY = 114;
		const VIEW_PROJECT_RECENT_ISSUES = 107;
		const VIEW_PROJECT_RECENT_ACTIVITIES = 108;
		const VIEW_PROJECT_UPCOMING = 109;
		const VIEW_PROJECT_DOWNLOADS = 110;

		const TYPE_USER = 1;
		const TYPE_PROJECT = 2;
		const TYPE_TEAM = 3;
		const TYPE_CLIENT = 4;

		public static $_b2dbtablename = 'TBGDashboardViewsTable';

		protected $_type;

		protected $_view;

		protected $_pid;

		protected $_tid;

		protected $_target_type;

		public static function getViews($tid, $target_type)
		{
			$views = array();
			if ($res = TBGDashboardViewsTable::getTable()->getViews($tid, $target_type))
			{
				foreach ($res as $id => $row)
				{
					$views[$id] = new TBGDashboardView($id, $row);
				}
			}

			return $views;
		}
		
		public static function getAvailableViews($target_type)
		{
			switch ($target_type)
			{
				case TBGDashboardView::TYPE_USER:
					$searches = array();
					$searches[self::VIEW_PREDEFINED_SEARCH] = array(	TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES => \caspar\core\Caspar::getI18n()->__('Issues reported by me'),
																				TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES => \caspar\core\Caspar::getI18n()->__('Open issues assigned to me'),
																				TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES => \caspar\core\Caspar::getI18n()->__('Open issues assigned to my teams'),
																				TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES => \caspar\core\Caspar::getI18n()->__('Open issues'),
																				TBGContext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES => \caspar\core\Caspar::getI18n()->__('Closed issues'),
																				TBGContext::PREDEFINED_SEARCH_PROJECT_MOST_VOTED => \caspar\core\Caspar::getI18n()->__('Most voted issues'));
					$searches[self::VIEW_LOGGED_ACTION] = array( 0 => \caspar\core\Caspar::getI18n()->__("What you've done recently"));
					if (\caspar\core\Caspar::getUser()->canViewComments())
					{
						$searches[self::VIEW_LAST_COMMENTS] = array( 0 => \caspar\core\Caspar::getI18n()->__('Recent comments'));
					}
					$searches[self::VIEW_SAVED_SEARCH] = array();
					$allsavedsearches = Caspar::getB2DBInstance()->getTable('TBGSavedSearchesTable')->getAllSavedSearchesByUserIDAndPossiblyProjectID(\caspar\core\Caspar::getUser()->getID());
					foreach ($allsavedsearches as $savedsearches)
					{
						foreach ($savedsearches as $a_savedsearch)
						{
							$searches[self::VIEW_SAVED_SEARCH][$a_savedsearch->get(TBGSavedSearchesTable::ID)] = $a_savedsearch->get(TBGSavedSearchesTable::NAME);
						}
					}
					break;
				case TBGDashboardView::TYPE_PROJECT:
					$issuetype_icons = array();
					foreach (\thebuggenie\entities\Issuetype::getIcons() as $key => $descr)
					{
						$issuetype_icons[] = \caspar\core\Caspar::getI18n()->__('Recent issues: %type%', array('%type%' => $descr));
					}
					
					$searches = array();
					$searches[self::VIEW_PROJECT_INFO] = array( 0 => \caspar\core\Caspar::getI18n()->__('About this project'));
					$searches[self::VIEW_PROJECT_TEAM] = array( 0 => \caspar\core\Caspar::getI18n()->__('Project team'));
					$searches[self::VIEW_PROJECT_CLIENT] = array( 0 => \caspar\core\Caspar::getI18n()->__('Project client'));
					$searches[self::VIEW_PROJECT_SUBPROJECTS] = array( 0 => \caspar\core\Caspar::getI18n()->__('Subprojects'));
					$searches[self::VIEW_PROJECT_LAST15] = array( 0 => \caspar\core\Caspar::getI18n()->__('Graph of closed vs open issues, past 15 days'));
					$searches[self::VIEW_PROJECT_STATISTICS_PRIORITY] = array( 0 => \caspar\core\Caspar::getI18n()->__('Statistics by priority'));
					$searches[self::VIEW_PROJECT_STATISTICS_CATEGORY] = array( 0 => \caspar\core\Caspar::getI18n()->__('Statistics by category'));
					$searches[self::VIEW_PROJECT_STATISTICS_STATUS] = array( 0 => \caspar\core\Caspar::getI18n()->__('Statistics by status'));
					$searches[self::VIEW_PROJECT_STATISTICS_RESOLUTION] = array( 0 => \caspar\core\Caspar::getI18n()->__('Statistics by resolution'));
					$searches[self::VIEW_PROJECT_RECENT_ISSUES] = $issuetype_icons;
					$searches[self::VIEW_PROJECT_RECENT_ACTIVITIES] = array( 0 => \caspar\core\Caspar::getI18n()->__('Recent activities'));
					$searches[self::VIEW_PROJECT_UPCOMING] = array( 0 => \caspar\core\Caspar::getI18n()->__('Upcoming milestones and deadlines'));
					$searches[self::VIEW_PROJECT_DOWNLOADS] = array( 0 => \caspar\core\Caspar::getI18n()->__('Latest downloads'));
					break;
			}

			return $searches;
		}

		public static function setViews($tid, $target_type, $views)
		{
			Caspar::getB2DBInstance()->getTable('TBGDashboardViewsTable')->clearViews($tid, $target_type);
			foreach($views as $key => $view)
			{
				Caspar::getB2DBInstance()->getTable('TBGDashboardViewsTable')->addView($tid, $target_type, $view);
			}
		}

		public static function resetViews($tid, $target_type)
		{
			$views = array();
			self::setUserViews($tid, $target_type, $views);
		}

		public function getType()
		{
			return $this->_type;
		}

		public function setType($_type)
		{
			$this->_type = $_type;
		}

		public function getDetail()
		{
			return $this->_view;
		}

		public function setDetail($detail)
		{
			$this->_view = $view;
		}

		public function getProjectID()
		{
			return $this->_pid;
		}

		public function setProjectID($pid)
		{
			$this->_pid = $pid;
		}

		public function getTargetID()
		{
			return $this->_tid;
		}

		public function setTargetID($tid)
		{
			$this->_tid = $tid;
		}

		public function getTargetType()
		{
			return $this->_target_type;
		}

		public function setTargetType($target_type)
		{
			$this->_target_type = $target_type;
		}

		public function isSearchView()
		{
			return (in_array($this->getType(), array(
				self::VIEW_PREDEFINED_SEARCH,
				self::VIEW_SAVED_SEARCH
			)));
		}

		public function getSearchParameters($rss = false)
		{
			$paramaters = ($rss) ? array('format' => 'rss') : array();
			switch ($this->getType())
			{
				case TBGDashboardView::VIEW_PREDEFINED_SEARCH :
					$parameters['predefined_search'] = $this->getDetail();
					break;
				case TBGDashboardView::VIEW_SAVED_SEARCH :
					$parameters['saved_search'] = $this->getDetail();
					break;
			}
			return $parameters;
		}

		public function getTitle()
		{
			switch ($this->getType())
			{
				
			}
		}

	}
