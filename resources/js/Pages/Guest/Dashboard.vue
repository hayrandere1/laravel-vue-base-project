<template>
    <v-toolbar color="blue-grey-lighten-4">
        <v-app-bar-nav-icon v-on:click="menu=!menu" v-if="deviceType!='desktop'"></v-app-bar-nav-icon>
        <v-toolbar-title>Laravel + Vue</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items class="hidden-sm-and-down">
            <v-btn v-for="(item) in menus">{{ item.name }}</v-btn>
        </v-toolbar-items>
    </v-toolbar>
    <transition name="committee" appear>
        <v-list bg-color="blue-grey-lighten-4" v-if="menu && deviceType!='desktop'">
            <v-list-item
                v-for="(item) in menus"
                :key="item.id"
                :value="item.name"
                variant="plain"

            >
                <template v-slot:prepend>
                    <v-icon :icon="item.icon"></v-icon>
                </template>

                <v-list-item-title v-text="item.name"></v-list-item-title>
            </v-list-item>
        </v-list>
    </transition>

</template>

<script>
import Menu from "../../Components/Widgets/menu.vue"
import Toolbar from "../../Components/Widgets/Toolbar.vue"
import Articles from "../../Components/Widgets/Articles.vue"
import Slider from "../../Components/Widgets/Slider.vue"

export default {
    components: {Menu, Toolbar, Articles, Slider},
    data() {
        return {
            value: 0,
            tab: 0,
            menu: false,
            menus: [
                {'id': 'home', 'name': 'Home'},
                {'id': 'about_us', 'name': 'About Us'},
                {'id': 'references', 'name': 'References'},
                {'id': 'contact_us', 'name': 'Contact Us'},
            ],
            deviceType: 'desktop'
        }
    },
    props: {},
    mounted() {
        this.setDeviceType();
    },
    methods: {
        setDeviceType() {
            const platform = navigator.platform.toLowerCase();
            if (/(android|webos|iphone|ipad|ipod|blackberry|windows phone)/.test(platform)) {
                this.deviceType = 'mobile';
            } else if (/mac|win|linux/i.test(platform)) {
                this.deviceType = 'desktop';
            } else if (/tablet|ipad/i.test(platform)) {
                this.deviceType = 'tablet';
            } else {
                this.deviceType = 'unknown';
            }

            // this.deviceType = 'tablet'
        },
    }
};
</script>
<style>

</style>
