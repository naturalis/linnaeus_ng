<?php
	require_once __DIR__ . '/../vendor/autoload.php';
	require_once __DIR__ . '/../configuration/admin/constants.php';
	require_once __DIR__ . '/../configuration/admin/configuration.php';
	
	use Symfony\Component\Yaml\Yaml;
	
	// Get settings fie;
	$yaml = __DIR__ . '/../configuration/default_settings.yml';
	
	// Get to work!
	$app = new CheckSettings();
	$app->setYaml($yaml);
	$app->run();
	
	
	
	class CheckSettings {
		
		private $mysqli;
		private $settings;
		
		private $projectId;
		private $moduleId;
		private $module;
		private $added = [];
		private $errors = [];
		
		public function __construct () {
			$config = new configuration;
			$db = $config->getDatabaseSettings();
			$this->mysqli = new mysqli($db['host'], $db['user'], $db['password'], $db['database']);
			$this->mysqli->set_charset('utf8');
		}
		
		public function setYaml ($yaml) {
			$this->settings = Yaml::parseFile($yaml);
		}
		
		public function run () {
			// Change setting use_variations to use_taxon_variations
			$this->renameSetting('use_variations', 'use_taxon_variations');
			// Looping over projects isn't necessary?
			// foreach ($this->getProjectIds() as $this->projectId) {
				foreach ($this->settings as $this->module => $moduleData) {
					$this->moduleId = $this->getModuleId($this->module);
					foreach ($moduleData['settings'] as $setting => $value) {
						if (!$this->settingExists($setting)) {
							if ($this->addSetting($setting, $value['info'], $value['default_setting']) > 0) {
								$this->added[] = $this->module . ': setting "' . $setting . "\" added";
							} else {
								$this->errors[] = $this->module . ': "' . $setting . "\" could NOT be added!";
							}
						}
					}
				}
			// }
			die($this->printResult());
		}
		
		private function getModuleId ($module) {
			$stmt = $this->mysqli->prepare('select id from modules where module = ?');
			$stmt->bind_param('s', $module);
			$stmt->execute();
			$stmt->bind_result($r);
			$stmt->fetch();
			$stmt->close();
			return $r ?? -1;
		}
		
		private function settingExists ($setting) {
			$stmt = $this->mysqli->prepare('select id from module_settings where setting = ?');
			$stmt->bind_param('s', $setting);
			$stmt->execute();
			$stmt->bind_result($r);
			$stmt->fetch();
			$stmt->close();
			return !empty($r);
		}
		
		// Should include module, but deliberately skipped, as for its sole purpose this may
		// casuse problems. The multi-access key has been renamed several times...
		private function renameSetting ($oldName, $newName) {
			$q = 'update module_settings set setting = ? where setting = ?';
			$stmt = $this->mysqli->prepare($q);
			$stmt->bind_param('ss', $newName, $oldName);
			$stmt->execute();
			$stmt->close();
		}
		
		private function addSetting ($setting, $info, $default) {
			$q = 
				'insert into module_settings 
					(module_id, setting, info, default_value, created) 
				 values 
					(?, ?, ?, ?, now())';
			$stmt = $this->mysqli->prepare($q);
			$stmt->bind_param('isss', $this->moduleId, $setting, $info, $default);
			$stmt->execute();
			$stmt->close();
			return $this->mysqli->insert_id;
		}
			
		private function getSettingValue () {
			
		}
		
		private function getProjectIds () {
			$r = $this->mysqli->query('select id from projects');
			return array_column($r->fetch_all(), 0);
			
		}
		
		private function printResult () {
			$output = "Result:";
			if (empty($this->added) && empty($this->errors)) {
				return $output . " project up-to-date, no updates required.\n";
			}
			if (!empty($this->added)) {
				foreach ($this->added as $added) {
					$output .= "\n- $added";
				}
			}
			if (!empty($this->errors)) {
				foreach ($this->errors as $error) {
					$output .= "\n- $error";
				}
			}
			return $output .= "\n";
		}
			
	}
