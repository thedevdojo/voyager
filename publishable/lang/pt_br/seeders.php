<?php

return [
    'categories' => [
        [
            'slug' => 'categoria-1',
            'name' => 'Categoria 2'
        ],
        [
            'slug' => 'categoria-2',
            'name' => 'Categoria 2'
        ],
    ],
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
    'pages' => [
        [
            'slug'             => 'ola-mundo',
            'title'            => 'Olá Mundo',
            'excerpt'          => 'Mussum Ipsum, cacilds vidis litro abertis. Vehicula non. Ut sed ex eros. Vivamus sit amet nibh non tellus tristique interdum. Mais vale um bebadis conhecidiss, que um alcoolatra anonimis. Leite de capivaris, leite de mula manquis sem cabeça. Viva Forevis aptent taciti sociosqu ad litora torquent. ',
            'body'             => '<p>Olá mundo. Mussum Ipsum, cacilds vidis litro abertis. Mé faiz elementum girarzis, nisi eros vermeio. Quem num gosta di mim que vai caçá sua turmis! Manduma pindureta quium dia nois paga. Interagi no mé, cursus quis, vehicula ac nisi. </p>
                                   <p>Copo furadis é disculpa de bebadis, arcu quam euismod magna. Delegadis gente finis, bibendum egestas augue arcu ut est. Casamentiss faiz malandris se pirulitá. Paisis, filhis, espiritis santis. </p>',
            'meta_description' => 'Yar Meta Descrição',
            'meta_keywords'    => 'Palavra-chave1, Palavra-chave2',
        ]
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
