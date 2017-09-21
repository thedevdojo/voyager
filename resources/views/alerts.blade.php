<div class="alerts">
    @foreach ($alerts as $alert)
        <div class="alert alert-{{ $alert->type }} alert-name-{{ $alert->name }}">
            @foreach($alert->components as $component)
                <?php echo $component->render(); ?>
            @endforeach
        </div>
    @endforeach
</div>
