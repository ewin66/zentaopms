<?php
$lang->score->common       = 'My Point';
$lang->score->record       = 'Points';
$lang->score->current      = 'My Points';
$lang->score->level        = 'Level Point';
$lang->score->reset        = 'Reset';
$lang->score->tips         = 'Point added yesterday: <strong>%d</strong><br/> Total Points: <strong>%d</strong>';
$lang->score->resetTips    = 'It will take a while. <strong>Do not close the window.</strong>';
$lang->score->resetStart   = 'Start';
$lang->score->resetLoading = 'Processing: ';
$lang->score->resetFinish  = 'Finish';

$lang->score->id      = 'ID';
$lang->score->userID  = 'UserID';
$lang->score->account = 'User';
$lang->score->module  = 'Module';
$lang->score->method  = 'Actions';
$lang->score->before  = 'Before';
$lang->score->score   = '+/-';
$lang->score->after   = 'After';
$lang->score->time    = 'Time';
$lang->score->desc    = 'Description';
$lang->score->noLimit = 'No limit';
$lang->score->times   = 'Times';
$lang->score->hour    = 'Hours';

$lang->score->modules['task']        = 'Task';
$lang->score->modules['tutorial']    = 'Tutorial';
$lang->score->modules['user']        = 'User';
$lang->score->modules['ajax']        = 'Other';
$lang->score->modules['doc']         = 'Document';
$lang->score->modules['todo']        = 'Todo';
$lang->score->modules['story']       = 'Story';
$lang->score->modules['bug']         = 'Bug';
$lang->score->modules['testcase']    = 'Case';
$lang->score->modules['testtask']    = 'Request';
$lang->score->modules['build']       = 'Build';
$lang->score->modules['project']     = 'Project';
$lang->score->modules['productplan'] = 'Plan';
$lang->score->modules['release']     = 'Release';
$lang->score->modules['block']       = 'Block';
$lang->score->modules['search']      = 'Search';

$lang->score->methods['task']['create']              = 'Create Task';
$lang->score->methods['task']['close']               = 'Close Task';
$lang->score->methods['task']['finish']              = 'Finish Task';
$lang->score->methods['tutorial']['finish']          = 'Finish Tutorial';
$lang->score->methods['user']['login']               = 'Login';
$lang->score->methods['user']['changePassword']      = 'Change Password';
$lang->score->methods['user']['editProfile']         = 'Edit Profile';
$lang->score->methods['ajax']['selectTheme']         = 'Change Theme';
$lang->score->methods['ajax']['selectLang']          = 'Change Language';
$lang->score->methods['ajax']['showSearchMenu']      = 'Advanced Search';
$lang->score->methods['ajax']['customMenu']          = 'Custom Menu';
$lang->score->methods['ajax']['dragSelected']        = 'Drag on List Page';
$lang->score->methods['ajax']['lastNext']            = 'Next Page Shortcut';
$lang->score->methods['ajax']['switchToDataTable']   = 'Switch Data Table'; 
$lang->score->methods['ajax']['submitPage']          = 'Change Paging Number';
$lang->score->methods['ajax']['quickJump']           = 'Quick Jump';
$lang->score->methods['ajax']['batchCreate']         = 'First Batch Create';
$lang->score->methods['ajax']['batchEdit']           = 'First Batch Edit';
$lang->score->methods['ajax']['batchOther']          = 'Other Batch Actions';
$lang->score->methods['doc']['create']               = 'Create Document';
$lang->score->methods['todo']['create']              = 'Create Todo';
$lang->score->methods['story']['create']             = 'Create Story';
$lang->score->methods['story']['close']              = 'Close Story';
$lang->score->methods['bug']['create']               = 'Report Bug';
$lang->score->methods['bug']['confirmBug']           = 'Confirm Bug';
$lang->score->methods['bug']['createFormCase']       = 'Report Bug form Case';
$lang->score->methods['bug']['resolve']              = 'Solve Bug';
$lang->score->methods['bug']['saveTplModal']         = 'Save Template';
$lang->score->methods['testtask']['runCase']         = 'Run Test Case';
$lang->score->methods['testcase']['create']          = 'Create Test Case';
$lang->score->methods['build']['create']             = 'Create Build';
$lang->score->methods['project']['create']           = 'Create Project';
$lang->score->methods['project']['close']            = 'Finish Project';
$lang->score->methods['productplan']['create']       = 'Create Plan';
$lang->score->methods['release']['create']           = 'Create Release';
$lang->score->methods['block']['set']                = 'Custom Block';
$lang->score->methods['search']['saveQuery']         = 'Save Query';
$lang->score->methods['search']['saveQueryAdvanced'] = 'Advanced Search';

$lang->score->extended['user']['changePassword'] = 'Get ##strength,1## point, if the password strength is medium. Get ##strength,2## points, if it is strong.';
$lang->score->extended['project']['close']       = 'After the project is close, project manager gets ##manager,close## point and team members get ##member,close## points. If it is done on time or earlier, the project manager gets ##manager,onTime## point and team members get ##member,onTime## points.';
$lang->score->extended['bug']['resolve']         = 'After a bug is resolved, get extra points according to its severity. S1, + ##severity,3##; S2 + ##severity,2##, S3 + ##severity,1##.';
$lang->score->extended['bug']['confirmBug']      = 'After a bug is confirmed, get extra points according to its severity. S1, + ##severity,3##; S2 + ##severity,2##, S3 + ##severity,1##.';
$lang->score->extended['task']['finish']         = 'After a task is done, get extra points by round(man-hour / 10  Estimates / Cost) + Priority point (p1 ##pri,1##, p2 ##pri,2##).';
$lang->score->extended['story']['close']         = 'After a story is closed, its creator will get extra ##createID## points.';
