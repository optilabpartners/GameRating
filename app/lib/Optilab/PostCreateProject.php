<?php

namespace Optilab;

use Composer\Script\Event;

class PostCreateProject
{

    public static function buildOptions(Event $event)
    {
        $io = $event->getIO();

        if ($io->isInteractive()) {
            $io->write('<info>Configure build settings. Press enter key for default.</info>');

            $browsersync_settings_default = [
                'publicPath'  => '/app/plugins/'.basename(getcwd()),
                'devUrl'      => 'http://example.dev'
            ];

            $browsersync_settings = [
                'publicPath'  => $io->ask('<info>Path to theme directory (eg. /wp-content/plugins/games-rating) [<comment>'.$browsersync_settings_default['publicPath'].'</comment>]:</info> ', $browsersync_settings_default['publicPath']),
                'devUrl'      => $io->ask('<info>Local development URL of WP site [<comment>'.$browsersync_settings_default['devUrl'].'</comment>]:</info> ', $browsersync_settings_default['devUrl'])
            ];

            file_put_contents('resources/assets/config.json', str_replace('/app/plugins/games-rating', $browsersync_settings['publicPath'], file_get_contents('resources/assets/config.json')));
            file_put_contents('resources/assets/config.json', str_replace($browsersync_settings_default['devUrl'], $browsersync_settings['devUrl'], file_get_contents('resources/assets/config.json')));
        }
    }
    // @codingStandardsIgnoreEnd
}
