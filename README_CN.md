<p align="center"><a href="https://the-control-group.github.io/voyager/" target="_blank"><img width="400" src="https://s3.amazonaws.com/thecontrolgroup/voyager.png"></a></p>

<p align="center">
<a href="https://travis-ci.org/the-control-group/voyager"><img src="https://travis-ci.org/the-control-group/voyager.svg?branch=master" alt="Build Status"></a>
<a href="https://styleci.io/repos/72069409/shield?style=flat"><img src="https://styleci.io/repos/72069409/shield?style=flat" alt="Build Status"></a>
<a href="https://packagist.org/packages/tcg/voyager"><img src="https://poser.pugx.org/tcg/voyager/downloads.svg?format=flat" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/tcg/voyager"><img src="https://poser.pugx.org/tcg/voyager/v/stable.svg?format=flat" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/tcg/voyager"><img src="https://poser.pugx.org/tcg/voyager/license.svg?format=flat" alt="License"></a>
<a href="https://github.com/larapack/awesome-voyager"><img src="https://cdn.rawgit.com/sindresorhus/awesome/d7305f38d29fed78fa85652e3a63e154dd8e8829/media/badge.svg" alt="Awesome Voyager"></a>
</p>

# **V**oyager - Laravel 后台管理框架
Made with ❤️ by [The Control Group](https://www.thecontrolgroup.com)

![Voyager Screenshot](https://s3.amazonaws.com/thecontrolgroup/voyager-screenshot.png)

官网 & 文档: https://voyager.devdojo.com/

视频教程: https://voyager.devdojo.com/academy/

加入我们 Slack chat: https://voyager-slack-invitation.herokuapp.com/

View the Voyager Cheat Sheet: https://voyager-cheatsheet.ulties.com/

<hr>

Laravel 后台管理与BREAD系统 (Browse(浏览), Read(阅读), Edit(编辑), Add(添加), & Delete(删除)), 支持 Laravel 5.5, 5.6, 5.7 and 5.8!

## 安装步骤

### 1. 安装依赖包

创建新的laravel应用程序后，可以使用以下命令安装Voyager包:

```bash
composer require tcg/voyager
```

### 2. 添加数据库凭据与APP_URL

接下来，确保创建一个新数据库并将数据库凭据添加到.env文件中:

```
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

你还需要更新.env文件中"APP_UR"变量中的网站URL:

```
APP_URL=http://localhost:8000
```

### 3. 设置本地语言

打开应用程序config/app.php文件并更改为本地语言:

```
'locale' => 'zh_CN',
```

### 4. 运行安装命令

最后，我们可以安装voyager。你可以使用或不使用模拟数据来执行此操作.
模拟数据将包括1个管理员帐户（如果没有用户已经存在）、1个演示页面、4个演示帖子、2个类别和7个设置.

如查不需要模拟数据可以使用以下命令：

```bash
php artisan voyager:install
```

如果你需要模拟数据，则需要使用--with-dummy参数运行命令：

```bash
php artisan voyager:install --with-dummy
```

> Troubleshooting: **Specified key was too long error**. If you see this error message you have an outdated version of MySQL, use the following solution: https://laravel-news.com/laravel-5-4-key-too-long-error

And we're all good to go!

Start up a local development server with `php artisan serve` And, visit [http://localhost:8000/admin](http://localhost:8000/admin).

## Creating an Admin User

If you did go ahead with the dummy data, a user should have been created for you with the following login credentials:

>**email:** `admin@admin.com`   
>**password:** `password`

NOTE: Please note that a dummy user is **only** created if there are no current users in your database.

If you did not go with the dummy user, you may wish to assign admin privileges to an existing user.
This can easily be done by running this command:

```bash
php artisan voyager:admin your@email.com
```

If you did not install the dummy data and you wish to create a new admin user you can pass the `--create` flag, like so:

```bash
php artisan voyager:admin your@email.com --create
```

And you will be prompted for the user's name and password.
