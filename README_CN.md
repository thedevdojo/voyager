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

如果不需要模拟数据可以使用以下命令：

```bash
php artisan voyager:install
```

如果你需要模拟数据，则需要使用--with-dummy参数运行命令：

```bash
php artisan voyager:install --with-dummy
```

> 故障排除: **Specified key was too long error**. 如果看到此错误消息，说明mysql版本已过时，请使用以下解决方案: https://laravel-news.com/laravel-5-4-key-too-long-error

我们现在可以开始了!

使用"php artisan serve"启动本地开发服务器，然后访问 [http://localhost:8000/admin](http://localhost:8000/admin).

## 创建管理员用户

如果继续使用模拟数据，则应使用以下登录凭据:

>**email:** `admin@admin.com`   
>**password:** `password`

注意：请注意，如果数据库中没有当前用户，则只创建一个虚拟用户**.

如果没有使用虚拟用户，则可能希望将管理权限分配给现有用户。

可以通过运行此命令轻松完成此操作:

```bash
php artisan voyager:admin your@email.com
```

如果您没有安装模拟数据，并且希望创建一个新的管理员用户，则可以传参"--create"，如下所示:

```bash
php artisan voyager:admin your@email.com --create
```

系统会提示您输入用户名和密码.
