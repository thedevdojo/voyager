<div class="container-fluid">
    <div class="alert alert-info">
        <strong>How To Use:</strong>
        <p>You can output {{ !empty($menu) ? 'this' : 'a' }} menu anywhere on your site by calling <code>Menu::display('{{ !empty($menu) ? $menu->name : 'name' }}')</code></p>
    </div>
</div>