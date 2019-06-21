<?php
/**
 * The model file of translate module of ZenTaoCMS.
 *
 * @copyright   Copyright 2009-2012 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     translate
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php
class translateModel extends model
{
    public function addLang()
    {
        $data  = fixer::input('post')->add('createdBy', $this->app->user->account)->get();
        if(empty($data->name)) dao::$errors['message'][] = sprintf($this->lang->error->notempty, $this->lang->translate->name);
        if(empty($data->code)) dao::$errors['message'][] = sprintf($this->lang->error->notempty, $this->lang->translate->code);
        if(dao::isError()) return false;

        $langs = empty($this->config->global->langs) ? array() : json_decode($this->config->global->langs, true);
        $langs[$data->code] = $data;
        $this->loadModel('setting')->setItem('system.common.global.langs', json_encode($langs));

        $modules = glob($this->app->getModuleRoot() . '*');
        foreach($modules as $modulePath)
        {
            $moduleName   = basename($modulePath);
            $mainLangFile = $modulePath . DS . 'lang' . DS . $data->reference . '.php';
            $this->initModuleLang($moduleName, $data->code);
            if(file_exists($mainLangFile))
            {
                $targetFile = $modulePath . DS . 'lang' . DS . $data->code . '.php';
                if(!copy($mainLangFile, $targetFile)) dao::$errors['message'][] = sprintf($this->lang->translate->notice->failCopyFile, $mainLangFile, $targetFile);
            }

            $extLangPath = $modulePath . DS . 'ext' . DS . 'lang' . DS . $data->reference;
            if(is_dir($extLangPath))
            {
                mkdir($modulePath . DS . 'ext' . DS . 'lang' . DS . $data->code);
                $extLangFiles = glob($extLangPath . DS . '*.php');
                foreach($extLangFiles as $extLangFile)
                {
                    $targetFile = $modulePath . DS . 'ext' . DS . 'lang' . DS . $data->code . DS . basename($extLangFile);
                    if(!copy($extLangPath, $targetFile)) dao::$errors['message'][] = sprintf($this->lang->translate->notice->failCopyFile, $extLangFile, $targetFile);
                }
            }
        }

        return true;
    }

    public function initModuleLang($moduleName, $langCode)
    {
        $flow = $this->config->global->flow;
        $now  = helper::now();
        $initLangs = $this->getModuleLangs($moduleName, $langCode);
        foreach($initLangs as $key => $value)
        {
            $translation = new stdclass();
            $translation->lang            = $langCode;
            $translation->module          = $moduleName;
            $translation->key             = $key;
            $translation->value           = trim($value, ';');
            $translation->status          = 'waiting';
            $translation->version         = $this->config->version;
            $translation->translator      = $this->app->user->account;
            $translation->translationTime = $now;
            $translation->mode            = $flow;
            $this->dao->replace(TABLE_TRANSLATION)->data($translation)->exec();
        }
    }

    public function getModuleLangs($moduleName, $langCode)
    {
        $modulePath   = $this->app->getModuleRoot() . $moduleName;
        $mainLangFile = $modulePath . "/lang/{$langCode}.php";
        $langItems    = array();
        if(file_exists($mainLangFile)) $langItems += $this->getActiveItemsByFile($mainLangFile);

        $extLangPath = $modulePath . DS . 'ext' . DS . 'lang' . DS . $langCode;
        if(is_dir($extLangPath))
        {
            $extLangFiles = glob($extLangPath . DS . '*.php');
            foreach($extLangFiles as $extLangFile) $langItems += $this->getActiveItemsByFile($extLangFile);
        }
        return $langItems;
    }

    public function getActiveItemsByFile($fileName)
    {
        $lines       = file($fileName);
        $flow        = $this->config->global->flow;
        $inCondition = false;
        $inFlow      = true;
        $level       = 0;
        $items       = array();
        foreach($lines as $i => $line)
        {
            $line = trim($line);
            if(empty($line)) continue;
            if(strpos($line, '$lang->menuOrder') === 0) continue;
            if(strpos($line, 'include') === 0 or strpos($line, 'global') === 0) continue;
            if(strpos($line, 'unset(') !== false) continue;
            if(strpos($line, '/*') === 0 or strpos($line, '//') === 0 or strpos($line, '<?php') === 0) continue;
            if(strpos($line, 'if') !== false and trim($lines[$i + 1]) == '{')
            {
                $inCondition = true;
                $level ++;
                if(strpos($line, 'config->global->flow') !== false and strpos($line, $flow) === false) $inFlow = false;
            }
            if($line == '}' and $inCondition)
            {
                $level --;
                if($level == 0)
                {
                    $inCondition = false;
                    $inFlow      = true;
                }
            }
            if($line == '{' or $line == '}' or strpos($line, 'if') === 0 or strpos($line, 'else') === 0) continue;
            if($inFlow)
            {
                if(strpos($line, '$lang') === 0 and strpos($line, '=') !== false)
                {
                    $position = strpos($line, '=');
                    $key      = trim(substr($line, 0, $position));
                    $value    = trim(trim(substr($line, $position + 1)), ';');
                    $items[$key] = $value;
                }
                elseif(isset($key) and strpos($key, '$lang') === 0)
                {
                    $items[$key] .= "\n" . $line;
                }
            }
        }
        return $items;
    }

    public function checkDirPriv()
    {
        $cmd     = '';
        $modules = glob($this->app->getModuleRoot() . '*');
        foreach($modules as $modulePath)
        {
            if(is_dir($modulePath . '/lang') and !is_writable($modulePath . '/lang')) $cmd .= "chmod 777 {$modulePath}/lang <br />";
            if(is_dir($modulePath . '/ext/lang') and !is_writable($modulePath . '/ext/lang')) $cmd .= "chmod -R 777 {$modulePath}/ext/lang <br />";
        }
        return $cmd;
    }

    public function getModules()
    {
        $this->loadModel('dev');
        foreach($this->lang->dev->endGroupList as $group => $groupName) $this->lang->dev->groupList[$group] = $groupName;

        $moduleList = glob($this->app->getModuleRoot() . '*');
        $modules    = array();
        foreach($moduleList as $module)
        {
            if(!is_dir($module . '/lang') and !is_dir($module . '/ext/lang')) continue;
            $module = basename($module);
            $group  = zget($this->config->dev->group, $module, 'other');
            $modules[$group][] = $module;
        }

        return $modules;
    }

    public function getLangItemCount()
    {
        $moduleGroups = $this->getModules();
        $moduleRoot   = $this->app->getModuleRoot();
        $itemCount    = 0;
        foreach($moduleGroups as $group => $modules)
        {
            foreach($modules as $module)
            {
                $items = $this->getModuleLangs($module, 'zh-cn');
                $itemCount += count($items);
            }
        }
        return $itemCount;
    }

    public function getProgress()
    {
        $langs = $this->dao->select("`lang`,sum(if((status != 'waiting'),1,0)) as waitItems, count(*) as count")->from(TABLE_TRANSLATION)->groupBy('`lang`')->fetchAll('lang');
        foreach($langs as $lang => $data) $data->progress = round($data->waitItems / $data->count, 3);
        return $langs;
    }

    /**
     * Get Translations 
     * 
     * @param  string $version 
     * @param  string $language 
     * @param  string $module 
     * @param  array  $langKeys 
     * @access public
     * @return void
     */
    public function getTranslations($language, $module = '', $langKeys = array())
    {
        return $this->dao->select('id,lang,`key`,`value`,status')->from(TABLE_TRANSLATION)
            ->where('lang')->eq($language)
            ->beginIF(!empty($module))->andWhere('module')->eq($module)->fi()
            ->beginIF(!empty($langKeys))->andWhere('langKey')->in($langKeys)->fi()
            ->orderBy('id asc')
            ->fetchAll('key');
    }

    /**
     * Add translation in database
     * 
     * @param  string    $zentaoVersion 
     * @param  string    $language 
     * @param  string    $module 
     * @access public
     * @return void
     */
    public function addTranslation($zentaoVersion, $language, $module)
    {
        $addData       = '';
        $allWords      = ''; //Use to stat word count.
        $account       = $this->app->user->account;
        $postData      = $this->untieModuleLang($_POST);
        $allEntry      = count($postData);
        $translateData = $this->getTranslations($zentaoVersion, $language, $module, array_keys($postData));
        $translated    = count(array_keys($translateData));

        foreach($postData as $langKey => $translation)
        {
            if(empty($translation))
            {
                unset($postData[$langKey]);
                continue;
            }

            /* Check duplicate translation.*/
            if(isset($translateData[$langKey]) and in_array($translation, array_keys($translateData[$langKey])))
            {
                unset($postData[$langKey]);
                continue;
            }

            if(!isset($translateData[$langKey])) $translated++;
            if(get_magic_quotes_gpc()) $translation = stripslashes($translation);
            $allWords[$langKey] = $translation;

            $translation = $this->dbh->quote($translation);
            $addData  .= "('" . $account . "', '$zentaoVersion', '$module', '$language', '$langKey', $translation, '" . helper::now() . "'),";
        }

        if($addData)
        {
            /* Delete old translation when user edit these.*/
            $havingTranslate = $this->dao->select('id, translation')->from(TABLE_TRANSLATE)
                ->where('account')->eq($account)
                ->andWhere('version')->eq($zentaoVersion)
                ->andWhere('module')->eq($module)
                ->andWhere('language')->eq($language)
                ->andWhere('langKey')->in(array_keys($allWords))
                ->fetchPairs('id', 'translation', false);
            if($havingTranslate)
            {
                $this->dao->delete()->from(TABLE_TRANSLATE)->where('id')->in(array_keys($havingTranslate))->exec(false);
                $addedScores = $this->countWords(join(' ', $havingTranslate));
            }

            /* Add translate content*/
            $addTranslateData = "INSERT INTO `" . TABLE_TRANSLATE . "` (`account`, `version`, `module`, `language`, `langKey`, `translation`, `time`) VALUES" . rtrim($addData, ',');
            $this->dbh->exec($addTranslateData);

            /* Add translate log*/
            if(get_magic_quotes_gpc()) $postData = stripslashes(json_encode($postData));
            $postData = $this->dbh->quote($postData);
            $addLogData = "INSERT INTO `" . TABLE_TRANSLATELOG . "` (`account`, `version`, `module`, `language`, `content`, `time`) VALUES('" . $account . "', '$zentaoVersion', '$module', '$language', " . $postData . ", '" . helper::now() . "')";
            $this->dbh->exec($addLogData);

            /* Count words as scores*/
            $scores = $this->countWords(join(' ', $allWords));
            if(empty($addedScores))
            {
                $this->loadModel('score')->log($account, 'translate', 'in', $scores, 'TRANSLATE');
            }
            else
            {
                $scores = $scores - $addedScores;
                $scoreType = $scores >= 0 ? 'in' : 'out';
                $this->loadModel('score')->log($account, 'edittranslate', $scoreType, abs($scores), 'TRANSLATE');
            }

            /* compute percent of translation and save.*/
            $percent = round($translated / $allEntry * 100, 1) . '%';
            $languageLog = $this->dao->select('*')->from(TABLE_LANGUAGELOG)->where('version')->eq($zentaoVersion)
                ->andWhere('language')->eq($language)
                ->andWhere('module')->eq($module)
                ->fetch('', false);

            if($languageLog)
            {
                $this->dao->update(TABLE_LANGUAGELOG)->set('translated')->eq($translated)->set('percent')->eq($percent)->where('id')->eq($languageLog->id)->exec(false);
            }
            else
            {
                $languageLog->version    = $zentaoVersion;
                $languageLog->language   = $language;
                $languageLog->module     = $module;
                $languageLog->allEntry   = $allEntry;
                $languageLog->translated = $translated;
                $languageLog->percent    = $percent;
                $this->dao->insert(TABLE_LANGUAGELOG)->data($languageLog, false)->exec();
            }
        }
    }

    /**
     * untieModuleLang 
     * 
     * @param  string $moduleLangs 
     * @param  string $key 
     * @access public
     * @return void
     */
    public function untieModuleLang($moduleLangs = '', $key = '')
    {
        $untieLang = array();
        if(is_object($moduleLangs))
        {
            foreach($moduleLangs as $langKey => $moduleLang)
            {
                $nextKey = empty($key) ? $langKey : "$key->$langKey";
                if(is_array($moduleLang) or is_object($moduleLang))
                {
                    $untieLang += $this->untieModuleLang($moduleLang, $nextKey);
                }
                else
                {
                    $untieLang[$nextKey] = $moduleLang;
                }
            }
        }
        elseif(is_array($moduleLangs))
        {
            foreach($moduleLangs as $arrayKey => $moduleLang)
            {
                /* Init key for delete '"'*/
                if(strpos($arrayKey, '\"') !== false) $arrayKey = str_replace(array('\"', '\"'), '', $arrayKey);
                $arrayKey = !$arrayKey ? '' : (is_string($arrayKey) and !empty($key)) ? '"' . $arrayKey . '"' : $arrayKey;
                $nextKey = empty($key) ? $arrayKey : $key . '[' . $arrayKey . ']';
                if(is_array($moduleLang) or is_object($moduleLang))
                {
                    $untieLang += $this->untieModuleLang($moduleLang, $nextKey);
                }
                else
                {
                    $untieLang[$nextKey] = $moduleLang;
                }
            }
        }
        return $untieLang;
    }

    public function countWords($allWords)
    {
        $scores    = 0;

        $allWords  = trim($allWords);
        $allWords  = preg_replace('/<[a-z\/]+.*>/Ui', '', $allWords);

        $allWords  = preg_replace('/[\x80-\xff]{1,3}/', '', $allWords, -1, $scores); //Count chinese.
        $scores   += str_word_count($allWords);
        return $scores;
    }
}