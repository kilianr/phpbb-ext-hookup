<?php

/**
 *
 * @package rgn
 * @copyright (c) 2015 Martin Beckmann
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace gn36\hookup\cron;

//TODO
class weekly_reset extends \phpbb\cron\task\base
{
	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\log\log_interface */
	protected $log;

	/** @var \gn36\rgn_hpdata\functions\inoutbot */
	protected $inoutbot;

	protected $inoutbot_interval;

	public function __construct(\phpbb\cache\service $cache, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\log\log_interface $log, \gn36\rgn_hpdata\functions\inoutbot $inoutbot, $inoutbot_interval, $root_path, $php_ext)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->log = $log;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->inoutbot_interval = $inoutbot_interval;
		$this->inoutbot = $inoutbot;
	}

	/**
	 * Run this cronjob (and delete prunable tasks)
	 * @see \phpbb\cron\task\task::run()
	 */
	public function run()
	{
		$now = time();

		inoutbot::reset();

		$this->config->set('rgn_inoutbot_last_run', $now, true);
	}

	/**
	 * Returns whether this cron job can run
	 * @see \phpbb\cron\task\base::is_runnable()
	 * @return bool
	 */
	public function is_runnable()
	{
		return isset($this->config['rgn_inoutbot_last_run']);
	}

	/**
	 * Should this cron job run now because enough time has passed since last run?
	 * @see \phpbb\cron\task\base::should_run()
	 * @return bool
	 */
	public function should_run()
	{
		$now = time();

		// Only run in december
		if(date('m') != 12)
		{
			return false;
		}

		// Run only in the first few days of december
		if(date('d') >= 15)
		{
			return false;
		}

		// Run at most every 20 days (i.e. once per Year in december)
		return $now > $this->config['rgn_inoutbot_reset_last_run'] + 86400 * 20;
	}

}
