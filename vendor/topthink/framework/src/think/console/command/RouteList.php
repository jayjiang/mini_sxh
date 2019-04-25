<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace think\console\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\console\Table;

class RouteList extends Command
{
    protected $sortBy = [
        'rule'   => 0,
        'route'  => 1,
        'method' => 2,
        'name'   => 3,
    ];

    protected function configure()
    {
        $this->setName('route:list')
            ->addArgument('app', Argument::OPTIONAL, 'app name .')
            ->addArgument('style', Argument::OPTIONAL, "the style of the table.", 'default')
            ->addOption('sort', 's', Option::VALUE_OPTIONAL, 'order by rule name.', 0)
            ->addOption('more', 'm', Option::VALUE_NONE, 'show route options.')
            ->setDescription('show route list.');
    }

    protected function execute(Input $input, Output $output)
    {
        $app = $input->getArgument('app');

        if ($app) {
            $filename = $this->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . $app . DIRECTORY_SEPARATOR . 'route_list_' . $app . '.php';
        } else {
            $filename = $this->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . 'route_list.php';
        }

        if (is_file($filename)) {
            unlink($filename);
        }

        $content = $this->getRouteList($app);
        file_put_contents($filename, 'Route List' . PHP_EOL . $content);
    }

    protected function getRouteList(string $app = null): string
    {
        $this->app->route->setTestMode(true);
        $this->app->route->clear();

        if ($app) {
            $path = $this->app->getRootPath() . 'route' . DIRECTORY_SEPARATOR . $app . DIRECTORY_SEPARATOR;
        } else {
            $path = $this->app->getRootPath() . 'route' . DIRECTORY_SEPARATOR;
        }

        $files = is_dir($path) ? scandir($path) : [];

        foreach ($files as $file) {
            if (strpos($file, '.php')) {
                include $path . $file;
            }
        }

        if ($this->app->config->get('route.route_annotation')) {
            $this->app->console->call('route:build', [$app ?: '']);
            $filename = $this->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . ($app ? $app . DIRECTORY_SEPARATOR : '') . 'build_route.php';

            if (is_file($filename)) {
                include $filename;
            }
        }

        $table = new Table();

        if ($this->input->hasOption('more')) {
            $header = ['Rule', 'Route', 'Method', 'Name', 'Option', 'Pattern'];
        } else {
            $header = ['Rule', 'Route', 'Method', 'Name'];
        }

        $table->setHeader($header);

        $routeList = $this->app->route->getRuleList();
        $rows      = [];

        foreach ($routeList as $item) {
            $item['route'] = $item['route'] instanceof \Closure ? '<Closure>' : $item['route'];

            if ($this->input->hasOption('more')) {
                $item = [$item['rule'], $item['route'], $item['method'], $item['name'], json_encode($item['option']), json_encode($item['pattern'])];
            } else {
                $item = [$item['rule'], $item['route'], $item['method'], $item['name']];
            }

            $rows[] = $item;
        }

        if ($this->input->getOption('sort')) {
            $sort = $this->input->getOption('sort');

            if (isset($this->sortBy[$sort])) {
                $sort = $this->sortBy[$sort];
            }

            uasort($rows, function ($a, $b) use ($sort) {
                $itemA = $a[$sort] ?? null;
                $itemB = $b[$sort] ?? null;

                return strcasecmp($itemA, $itemB);
            });
        }

        $table->setRows($rows);

        if ($this->input->getArgument('style')) {
            $style = $this->input->getArgument('style');
            $table->setStyle($style);
        }

        return $this->table($table);
    }

}
