<?php
/*******************************************************************************************************************
| Software Name        : ViewShark
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) ViewShark
| Website              : https://www.viewshark.com
| E-mail               : support@viewshark.com || viewshark@gmail.com
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the ViewShark End-User License Agreement, available online at:
| https://www.viewshark.com/support/license/
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2013-2024 viewshark.com. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

class VMembership
{
    public static function getRequiredTierForFile($type, $file_key)
    {
        global $db;
        // optional access rule table; if not present or cfg disabled, return null (no gating)
        if (!self::enabled()) return null;
        try {
            $t = $db->execute(sprintf("SELECT `required_tier_id` FROM `db_file_access_rules` WHERE `file_key`='%s' LIMIT 1;", htmlspecialchars($file_key, ENT_QUOTES, 'UTF-8')));
            $tier = (int) $t->fields['required_tier_id'];
            return $tier > 0 ? $tier : null;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function hasAccess($viewerId, $channelId, $requiredTierId = null)
    {
        // no gating
        if (!self::enabled() || !$requiredTierId) return true;
        global $db;
        $viewerId = (int) $viewerId;
        $channelId = (int) $channelId;
        if ($viewerId <= 0) return false; // must sign in for member-only
        try {
            $q = $db->execute(sprintf("SELECT `tier_id`, `status`, `expires_at` FROM `db_user_memberships` WHERE `subscriber_usr_id`='%s' AND `channel_usr_id`='%s' AND `status`='active' ORDER BY `expires_at` DESC LIMIT 1;", $viewerId, $channelId));
            if (!$q->fields['tier_id']) return false;
            // allow if user tier >= required tier in simple numeric order, or exact match
            $userTier = (int) $q->fields['tier_id'];
            $exp = $q->fields['expires_at'];
            if ($exp && strtotime($exp) < time()) return false;
            return $userTier >= (int) $requiredTierId;
        } catch (Exception $e) {
            return true; // fail-open if table is missing
        }
    }

    public static function badgeFor($viewerId, $channelId)
    {
        global $db;
        if (!self::badgesEnabled()) return '';
        try {
            $q = $db->execute(sprintf("SELECT `tier_id` FROM `db_user_memberships` WHERE `subscriber_usr_id`='%s' AND `channel_usr_id`='%s' AND `status`='active' ORDER BY `expires_at` DESC LIMIT 1;", (int) $viewerId, (int) $channelId));
            if ($q->fields['tier_id']) {
                $tier = (int) $q->fields['tier_id'];
                // simple star badge with tier number; matches existing icon classes
                return '<span class="mem-badge" title="Member Tier ' . $tier . '"><i class="icon-star"></i><span class="mem-tier">' . $tier . '</span></span>';
            }
        } catch (Exception $e) {
        }
        return '';
    }

    public static function issueChatToken($viewerId, $channelId)
    {
        global $cfg;
        // Very lightweight signed token for chat membership gating; 5 minute TTL
        $ttl = 300;
        $ts = time();
        $sig = md5($viewerId . '|' . $channelId . '|' . $ts . '|' . $cfg['live_chat_salt']);
        return base64_encode($viewerId . ':' . $channelId . ':' . $ts . ':' . $ttl . ':' . $sig);
    }

    public static function enabled()
    {
        global $cfg; return (int) $cfg['channel_memberships'] === 1;
    }
    public static function chatMembersOnly()
    {
        global $cfg; return (int) $cfg['member_chat_only'] === 1;
    }
    public static function badgesEnabled()
    {
        global $cfg; return (int) $cfg['member_badges'] === 1;
    }
}

