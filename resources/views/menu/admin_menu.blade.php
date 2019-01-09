@section('admin-menu')
<ul class="nav navbar-nav">
    <li v-for="(item, i) in items" class="">Hallo @{{ i }}</li>
</ul>
@endsection
<script>
Vue.component('admin-menu', {
    template: `@yield('admin-menu')`,
    props: {
        items: {
            type: Array,
            default: [],
        }
    }
});
</script>
