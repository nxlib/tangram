Tangram develop plan
========================================

version: 0.0.1
-------
1. 识别`tangram.json`文件
2. `tangram.json`支持`modules-path`,`restful-path`,`web-page-path`三个指定目录
3. 根据`tangram.json`文件扫描模块目录
4. 支持`tangram build`命令

version: 0.0.2
-------
1. 根据扫描记录，自动生成`tangram-modules`目录
2. 在`tangram-modules`目录下，自动生成`class`自动加载文件，`路由`文件

version: 0.0.3
-------
1. 模块支持命令行自动创建，命令`tangram create module/name`

version: 0.0.4
-------
1. 配合`nxlib/orm`，支持orm自动化处理

version: 0.0.5
-------
支持更细分的命令