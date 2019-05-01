<template>
    <ul class="nav navbar-nav">
        <li v-for="(item, i) in items" :class="classes(item)">
            <a :target="item.target" :href="item.children.length > 0 ? '#'+item.id+'-dropdown-element' : item.href" :style="'color:'+color(item)" v-bind:data-toggle="item.children.length > 0 ? 'collapse' : false" :aria-expanded="item.children.length > 0 ? String(item.active) : false">
                <span :class="'icon '+item.icon_class"></span>
                <span class="title">{{ item.title }}</span>
            </a>
            <div v-if="item.children.length > 0" :id="item.id+'-dropdown-element'" :class="'panel-collapse collapse' + (item.active ? ' in' : ' ')">
                <div class="panel-body">
                    <admin-menu :items="item.children"></admin-menu>
                </div>
            </div>
        </li>
    </ul>
</template>
<script>
export default {
    props: {
        items: {
            type: Array,
            default: [],
        }
    },
    methods: {
        classes: function(item) {
            var classes = [];
            if (item.children.length > 0) {
                classes.push('dropdown');
            }
            if (item.active) {
                classes.push('active');
            }


            return classes.join(' ');
        },
        color: function(item) {
            if (item.color && item.color != '#000000') {
                return item.color;
            }

            return '';
        }
    }
};
</script>
