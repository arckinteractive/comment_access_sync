<?php

namespace Comment\Access\Sync;

const PLUGIN_ID = 'comment_access_sync';
const UPGRADE_VERSION = 20141209;

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

function init() {
	elgg_register_event_handler('update', 'all', __NAMESPACE__ . '\\entity_update');
	elgg_register_plugin_hook_handler('cron', 'daily', __NAMESPACE__ . '\\daily_cron');
}


function entity_update($e, $t, $entity) {
	if (!elgg_instanceof($entity)) {
		return true;
	}
	
	$dbprefix = elgg_get_config('dbprefix');
	$comment_id = add_metastring('generic_comment');
	$discussion_id = add_metastring('group_topic_post');
	$sql = "UPDATE {$dbprefix}annotations"
		. " SET access_id = {$entity->access_id}"
		. " WHERE entity_guid = {$entity->guid}"
		. " AND name_id IN ({$comment_id}, {$discussion_id})";
	update_data($sql);
	
	return true;
}

/**
 * Detects and fixes access violations
 * 
 * @return boolean
 */
function check_access_violations() {
	$dbprefix = elgg_get_config('dbprefix');
	$comment_id = add_metastring('generic_comment');
	$discussion_id = add_metastring('group_topic_post');
	
	$sql = "SELECT COUNT(a.id) as total FROM {$dbprefix}annotations a"
		. " JOIN {$dbprefix}entities e ON a.entity_guid = e.guid"
		. " WHERE e.access_id != a.access_id AND a.name_id IN ({$comment_id}, {$discussion_id})";
		
	$result = get_data($sql);
	
	if (!$result[0]->total) {
		return false;
	}
	
	$sql = "UPDATE {$dbprefix}annotations a"
		. " JOIN {$dbprefix}entities e ON a.entity_guid = e.guid"
		. " SET a.access_id = e.access_id"
		. " WHERE a.name_id IN ({$comment_id}, {$discussion_id}) AND a.access_id != e.access_id";
		
	return update_data($sql);
}


function daily_cron($h, $t, $r, $p) {
	check_access_violations();
	
	return $r;
}