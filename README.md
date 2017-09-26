<p align="center"><a href="https://the-control-group.github.io/voyager/" target="_blank"><img width="400" src="https://s3.amazonaws.com/thecontrolgroup/voyager.png"></a></p>

<p align="center">
<a href="https://travis-ci.org/the-control-group/voyager"><img src="https://travis-ci.org/the-control-group/voyager.svg?branch=master" alt="Build Status"></a>
<a href="https://styleci.io/repos/72069409/shield?style=flat"><img src="https://styleci.io/repos/72069409/shield?style=flat" alt="Build Status"></a>
<a href="https://packagist.org/packages/tcg/voyager"><img src="https://poser.pugx.org/tcg/voyager/downloads.svg?format=flat" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/tcg/voyager"><img src="https://poser.pugx.org/tcg/voyager/v/stable.svg?format=flat" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/tcg/voyager"><img src="https://poser.pugx.org/tcg/voyager/license.svg?format=flat" alt="License"></a>
<a href="https://github.com/larapack/awesome-voyager"><img src="https://cdn.rawgit.com/sindresorhus/awesome/d7305f38d29fed78fa85652e3a63e154dd8e8829/media/badge.svg" alt="Awesome Voyager"></a>
</p>

# **V**oyager - 基于 Laravel 的后台管理
创建者 [The Control Group](https://www.thecontrolgroup.com)

修改者 [Bigface Song](https://blog.11010.net)。几乎所有代码都来自[The Control Group](https://www.thecontrolgroup.com)，我纠结了很长时间才fork，主要因为slug，
我自己做项目基本用不到，而且 `admin_menu.blade.php` 文件大约 65 行 `<div id="{{ str_slug($transItem->title, '-') }}-dropdown-element" class="panel-collapse collapse {{ (in_array('active', $listItemClass) ? 'in' : '') }}">`，中文项目里面 `title` 几乎都是中文，这样 `str_slug()` 函数就回报错，本来打算提交PR，建议代码别用 `title`，比如改为 `url`，
感觉这样做有点自私。还有就是因为国内无法加载谷歌的一些服务。

![Voyager Screenshot](https://s3.amazonaws.com/thecontrolgroup/voyager-screenshot.png)

网站 & 文档: https://laravelvoyager.com

视频教程: https://laravelvoyager.com/academy/

Slack 聊天室: https://voyager-slack-invitation.herokuapp.com/

Voyager 速查手册: https://voyager-cheatsheet.ulties.com/

<hr>

Laravel Admin & BREAD 系统 (Browse, Read, Edit, Add, & Delete), 支持 Laravel 5.4 和更新的版本!

*特意的说一下，BREAD系统确实是炒鸡好用*

## 安装步骤

### 1. 安装 voyager 包

首选创建 Laravel 应用， 然后运行如下命令安装 voyager 包

```bash
composer require tcg/voyager
```

### 2. 修改 .env 文件里的数据库配置和 APP_URL 配置

接下来，请确保创建一个新的数据库，并将您的数据库修改到 .env 文件中

```
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

修改 .env 文件中的 `APP_URL` 变量

```
APP_URL=http://localhost:8000
```

> 如果你使用的是 Laravel 5.4，你需要添加服务提供者 [（Service Provider）](https://voyager.readme.io/docs/adding-the-service-provider)

### 3. 运行安装

最后，我们可以安装 voyager。 你可以不添加虚拟数据。
虚拟数据包括 1 个 admin 用户（如果 admin 用户不存在），1 条单页数据，4 条文章数据， 2 个分类和 7 个设置

不安装测试数据运行如下

```bash
php artisan voyager:install
```

如果安装测试数据运行如下

```bash
php artisan voyager:install --with-dummy
```

> 常见问题： **Specified key was too long error**。如果你遇到此错误信息，说明你的MySQL版本过时，请使用以下解决方案：https://laravel-news.com/laravel-5-4-key-too-long-error

到此祝您顺利安装完成！

使用 `php artisan serve` 本地启动, 然后访问 [http://localhost:8000/admin](http://localhost:8000/admin).

## 创建管理员用户

If you did go ahead with the dummy data, a user should have been created for you with the following login credentials:
如果你已经添加测试数据，已经创建好如下用户数据供您登录

>**email:** `admin@admin.com`
>**password:** `password`

注意：请您注意，**只有**当数据库中没有此用户的情况下，才能创建测试数据成功

如果您没有成功安装测试数据，您可以通过如下命令为您现有用户分配一个管理员权限

```bash
php artisan voyager:admin your@email.com
```

如果您安装的时候没有安装测试数据，您可以添加 `--create` 参数来创建一个管理员用户
```bash
php artisan voyager:admin your@email.com --create
```

系统将会提示您输入用户名和密码

本项目长期更新，至少每个大版本会合并发布，最后，欢迎PR，思路最好符合原作者的初衷。
