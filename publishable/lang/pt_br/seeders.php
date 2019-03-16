<?php

return [
    'data_rows'  => [
        'author'           => 'Autor',
        'avatar'           => 'Avatar',
        'body'             => 'Corpo',
        'category'         => 'Categoria',
        'created_at'       => 'Criado em',
        'display_name'     => 'Nome a ser apresentado',
        'email'            => 'E-mail',
        'excerpt'          => 'Excerto',
        'featured'         => 'Destaque',
        'id'               => 'ID',
        'meta_description' => 'Meta Descrição',
        'meta_keywords'    => 'Meta Palavra-chave',
        'name'             => 'Nome',
        'order'            => 'Ordem',
        'page_image'       => 'Imagem da Página',
        'parent'           => 'Parent',
        'password'         => 'Senha',
        'post_image'       => 'Imagem da Publicação',
        'remember_token'   => 'Remember Token', //todo find suitable translation
        'role'             => 'Função',
        'seo_title'        => 'SEO Título',
        'slug'             => 'Slug', //todo find suitable translation
        'status'           => 'Status',
        'title'            => 'Título',
        'updated_at'       => 'Atualizado em',
    ],
    'data_types' => [
        'category' => [
            'singular' => 'Categoria',
            'plural'   => 'Categorias',
        ],
        'menu'     => [
            'singular' => 'Menu',
            'plural'   => 'Menus',
        ],
        'page'     => [
            'singular' => 'Página',
            'plural'   => 'Páginas',
        ],
        'post'     => [
            'singular' => 'Publicação',
            'plural'   => 'Publicações',
        ],
        'role'     => [
            'singular' => 'Função',
            'plural'   => 'Funções',
        ],
        'user'     => [
            'singular' => 'Usuário',
            'plural'   => 'usuários',
        ],
    ],
    'menu_items' => [
        'bread'        => 'BREAD',
        'categories'   => 'Categorias',
        'compass'      => 'Bússola',
        'dashboard'    => 'Painel Administrativo',
        'database'     => 'Base de Dados',
        'media'        => 'Mídia',
        'menu_builder' => 'Construtor de Menu',
        'pages'        => 'Páginas',
        'posts'        => 'Publicações',
        'roles'        => 'Funções',
        'settings'     => 'Configurações',
        'tools'        => 'Ferramentas',
        'users'        => 'Usuários',
    ],
    'roles'      => [
        'admin' => 'Administrador',
        'user'  => 'Usuário comum',
    ],
    'settings'   => [
        'admin' => [
            'background_image'           => 'Imagem de Background do Ambiente Administrativo',
            'description'                => 'Descrição do Ambiente Administrativo',
            'description_value'          => 'Bem-vindo ao Voyager. O Ambiente Administrativo que faltava para Laravel',
            'google_analytics_client_id' => 'Google Analytics Client ID (used for admin dashboard)',
            'icon_image'                 => 'Ícone do Ambiente Administrativo',
            'loader'                     => 'Loader do Ambiente Administrativo',
            'title'                      => 'Título do Ambiente Administrativo',
        ],
        'site'  => [
            'description'                  => 'Descrição do Site',
            'google_analytics_tracking_id' => 'Google Analytics Tracking ID',
            'logo'                         => 'Logo do Site',
            'title'                        => 'Título do Site',
        ],
    ],
];
