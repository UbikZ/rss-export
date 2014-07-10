<?php

namespace Ubikz\Feed;

class Parser
{
    const TYPE_REDDIT = 'reddit';
    const TYPE_COMMON = 'common';
    const URL_PATTERN = 'http:\/\/[0-9a-zA-Z\-_\./]+';

    private static function _getPatterns($type = self::TYPE_COMMON)
    {
        $return = array(
            self::TYPE_REDDIT => '#<a href="%s">\[link\]</a>#',
            self::TYPE_COMMON => '#<img src="%s"/>#'
        );

        return isset($return[$type]) ? $return[$type] : '%s';
    }

    private static function _parseDescription($description, $type = self::TYPE_COMMON)
    {
        $pattern = sprintf(self::_getPatterns($type), self::URL_PATTERN);
        preg_match_all($pattern, $description, $matches);
        preg_match('#' . self::URL_PATTERN . '#', isset($matches[0][0]) ? $matches[0][0] : '', $descLink);

        return isset($descLink[0]) ? $descLink[0] : '';
    }

    private static function _getDescriptionByFeed($feed, $type = self::TYPE_COMMON)
    {
        $result = array();
        $f = \Zend\Feed\Reader\Reader::import($feed);
        foreach ($f as $entry) {
            if ((new \DateTime())->setTime(0,0) ==
                $entry->getDateModified()->setTimezone(new \DateTimeZone('Europe/Paris'))->setTime(0,0)) {
                $parse = self::_parseDescription($entry->getDescription(), $type);
                if (!empty($parse)){
                    $result[] = $parse;
                }
            }
        }

        return $result;
    }

    private static function _guessTypeForLink($link)
    {
        $return = self::TYPE_COMMON;
        if (preg_match('#[0-9a-zA-Z\-_\./]+reddit.+#', $link) != 0) {
            $return = self::TYPE_REDDIT;
        }

        return $return;
    }

    public static function set($feeds, $src = '')
    {
        $links = array();
        foreach ($feeds as $feed) {
            $links = array_merge_recursive($links, self::_getDescriptionByFeed($feed, self::_guessTypeForLink($feed)));
        }

        $dir = realpath(dirname($src));
        $fileName = $dir . "/parsedLinks-" . (new \DateTime())->format('Y-m-d');
        if (false !== ($content = @file_get_contents($fileName))) {
            $linksFromFile = explode(PHP_EOL, $content);
            $links = array_unique(array_merge($links, $linksFromFile));
        }

        @file_put_contents($fileName, implode(PHP_EOL, array_filter($links)));
    }
}