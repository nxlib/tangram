<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 2017/3/13
 * Time: 20:21
 */

namespace Tangram\Handler\Data;


class UriMap
{
    private $routerMap = [];
    private $permissionMap = [];

    public function __construct(array $uriList)
    {
        if (!empty($uriList)) {
            foreach ($uriList as $item) {
                $key = strtoupper($item['method']) . '#' . $item['uri'];
                if (isset($this->routerMap[$key])) {
                    console($item);
                    console($this->routerMap[$key]);
                    die("ERROR:存在相同的URI:\r\n path:{$item['uri']}\r\n method:{$item['method']}\r\n");
                }
                $this->routerMap[$key] = [
                    'namespace' => $item['namespace'],
                    'class' => $item['class'],
                    'function' => $item['function']
                ];
                if (!empty($item['module']) || !empty($item['nav']) || !empty($item['menu']) || !empty($item['name'])) {
                    $this->permissionMap[$key] = [
                        'module' => $item['module'],
                        'nav' => $item['nav'],
                        'menu' => $item['menu'],
                        'name' => $item['name'],
                        'rest' => $item['rest']
                    ];
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getRouterMap(): array
    {
        return $this->routerMap;
    }

    /**
     * @return array
     */
    public function getPermissionMap(): array
    {
        return $this->permissionMap;
    }
}