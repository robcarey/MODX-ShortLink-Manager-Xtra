<?php
/**
 * ShortLink Manager - Table resolver
 *
 * Uses raw SQL (not xPDO model classes) so it runs correctly regardless of
 * whether the core component files have been installed yet. This resolver
 * is attached to the plugin vehicle (xPDOObjectVehicle) which means it runs
 * on both install AND uninstall.
 *
 * @var xPDOTransport $transport
 * @var array         $options
 * @package shortlinkmgr
 */
if (!$transport->xpdo) return true;
$modx =& $transport->xpdo;

$prefix = $modx->getOption('table_prefix', null, 'modx_');

switch ($options[xPDOTransport::PACKAGE_ACTION]) {

    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        $modx->exec("CREATE TABLE IF NOT EXISTS `{$prefix}shortlinkmgr_links` (
            `id`                int(10) unsigned    NOT NULL AUTO_INCREMENT,
            `shortcode`         varchar(32)         NOT NULL DEFAULT '',
            `title`             varchar(255)        DEFAULT '',
            `description`       text,
            `published`         tinyint(1)          NOT NULL DEFAULT 1,
            `redirect_id`       int(10)             DEFAULT NULL,
            `redirect_url`      varchar(2048)       DEFAULT NULL,
            `redirect_type`     int(3)              NOT NULL DEFAULT 302,
            `utm_source`        varchar(255)        DEFAULT NULL,
            `utm_medium`        varchar(255)        DEFAULT NULL,
            `utm_campaign`      varchar(255)        DEFAULT NULL,
            `utm_term`          varchar(255)        DEFAULT NULL,
            `utm_content`       varchar(255)        DEFAULT NULL,
            `anchor`            varchar(255)        DEFAULT NULL,
            `additional_params` text,
            `click_count`       int(10)             NOT NULL DEFAULT 0,
            `expires_at`        datetime            DEFAULT NULL,
            `created_by`        int(10)             NOT NULL DEFAULT 0,
            `created_at`        datetime            DEFAULT NULL,
            `updated_at`        datetime            DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `shortcode` (`shortcode`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $modx->exec("CREATE TABLE IF NOT EXISTS `{$prefix}shortlinkmgr_clicks` (
            `id`         int(10) unsigned NOT NULL AUTO_INCREMENT,
            `link_id`    int(10)          NOT NULL DEFAULT 0,
            `clicked_at` datetime         DEFAULT NULL,
            `ip_address` varchar(45)      DEFAULT NULL,
            `referrer`   varchar(2048)    DEFAULT NULL,
            `user_agent` varchar(512)     DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `link_id` (`link_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $modx->log(modX::LOG_LEVEL_INFO, '[ShortLink Manager] Database tables created/verified.');
        break;

    case xPDOTransport::ACTION_UNINSTALL:
        $remove = (bool) $modx->getOption('shortlinkmgr.remove_table_on_uninstall', null, false);
        if ($remove) {
            $modx->exec("DROP TABLE IF EXISTS `{$prefix}shortlinkmgr_clicks`");
            $modx->exec("DROP TABLE IF EXISTS `{$prefix}shortlinkmgr_links`");
            $modx->log(modX::LOG_LEVEL_INFO, '[ShortLink Manager] Database tables dropped.');
        } else {
            $modx->log(modX::LOG_LEVEL_INFO, '[ShortLink Manager] Database tables preserved.');
        }
        break;
}

return true;
