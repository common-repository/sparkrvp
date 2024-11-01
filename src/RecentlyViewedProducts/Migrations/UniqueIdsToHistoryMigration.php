<?php

namespace Sparkrvp\SparkPlugins\SparkWoo\RecentlyViewedProducts\Migrations;

use Sparkrvp\SparkPlugins\SparkWoo\Common\Migrations\MigrationInterface;
class UniqueIdsToHistoryMigration implements MigrationInterface
{
    public string $cookieName;
    public function __construct(string $cookieName)
    {
        $this->cookieName = $cookieName;
    }
    public function up() : void
    {
        $results = $this->getUserData();
        global $wpdb;
        foreach ($results as $result) {
            $visited = \unserialize($result['historyData']);
            if (\is_array($visited) && \count($visited) > 0 && \is_array($visited[0])) {
                continue;
            }
            $new = \array_map(function ($h) {
                return array('productId' => $h, 'timestamp' => \time());
            }, $visited);
            $wpdb->update($wpdb->usermeta, array('meta_value' => \serialize($new)), array('user_id' => $result['userId'], 'meta_key' => $this->cookieName));
        }
    }
    public function down() : void
    {
        $results = $this->getUserData();
        global $wpdb;
        foreach ($results as $result) {
            $visited = \unserialize($result['historyData']);
            if (\is_array($visited) && \count($visited) > 0 && \is_int($visited[0])) {
                continue;
            }
            $new = \array_unique(\array_map(function ($h) {
                return $h['productId'];
            }, $visited));
            $wpdb->update($wpdb->usermeta, array('meta_value' => \serialize($new)), array('user_id' => $result['userId'], 'meta_key' => $this->cookieName));
        }
    }
    private function getUserData() : array
    {
        global $wpdb;
        $results = $wpdb->get_results($wpdb->prepare("SELECT {$wpdb->usermeta}.meta_value as historyData, {$wpdb->usermeta}.user_id as userId\n        FROM {$wpdb->usermeta} \n        WHERE {$wpdb->usermeta}.meta_key = %s\n        ", array($this->cookieName)), ARRAY_A);
        return $results;
    }
    public function getVersion()
    {
        return "1.2.0";
    }
}
