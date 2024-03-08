<template>
    <v-snackbar
        v-for="(item,index) in alerts"
        :style="{'margin-top':calcMargin(index)}"
        :key="index"
        v-model="alert"
        :timeout="4000"
        variant="text"
        location="top right"
        multi-line
    >
        <v-alert :title="item.title" :text="item.text" :type="item.type"></v-alert>
    </v-snackbar>
    <v-toolbar color="blue-grey-lighten-4">
        <v-app-bar-nav-icon v-on:click="menu=!menu" v-if="deviceType!='desktop'"></v-app-bar-nav-icon>
        <v-toolbar-title>
            {{ this.$page.props.appName }}
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items class="hidden-sm-and-down">
            <template v-for="(item) in menus">
                <v-btn :class="(item.color)?'bg-'+item.color:''" :href="(item.route)?item.route:''" v-show="item.visible">
                    {{ item.name }}
                </v-btn>
            </template>
        </v-toolbar-items>
    </v-toolbar>
    <transition name="committee" appear>
        <v-list bg-color="blue-grey-lighten-4" v-if="menu && deviceType!='desktop'">

            <template v-for="(item) in menus">
                <v-list-item
                    :key="item.id"
                    :value="item.name"
                    variant="plain"
                    :class="(item.color)?'bg-'+item.color:''"
                    v-if="item.visible"
                >
                    <template v-slot:prepend>
                        <v-icon :icon="item.icon"></v-icon>
                    </template>
                    <v-list-item-title v-text="item.name"></v-list-item-title>
                </v-list-item>
            </template>
        </v-list>
    </transition>
    <slot></slot>
</template>

<script>
import route from "ziggy-js";

export default {
    name: "AppLayout",
    data() {
        return {
            value: 0,
            tab: 0,
            menu: false,
            menus: [
                {'id': 'home', 'name': 'Dashboad', 'visible': true},
                {'id': 'about_us', 'name': 'About Us', 'visible': true},
                {'id': 'references', 'name': 'References', 'visible': true},
                {'id': 'contact_us', 'name': 'Contact Us', 'visible': true},
                {
                    'id': 'login',
                    'name': 'Login',
                    'route': route('user.login'),
                    'color': 'success',
                    'visible': !this.$page.props.loginUser
                },
                {'id': 'register', 'name': 'Register', 'color': 'primary', 'visible': !this.$page.props.loginUser},
                {
                    'id': 'user',
                    'name': 'Account',
                    'route': route('user.home'),
                    'color': 'primary',
                    'visible': this.$page.props.loginUser
                },
            ],
            deviceType: 'desktop',
            alerts: [],
            alert: false,
            flash: {"error": null, "message": null},
        }
    },
    props: {},
    mounted() {
        this.setDeviceType();
        this.flash = this.$page.props.flash;
    },
    watch: {
        'flash': {
            deep: true,
            handler(newValue, oldValue) {
                this.alerts = [];
                if (newValue.message !== null) {
                    this.alert = true;
                    this.alerts = [{
                        'title': newValue.message,
                        'text': '',
                        'type': 'success'
                    }];
                } else if (newValue.error !== null) {
                    this.alert = true;
                    this.alerts = [{
                        'title': newValue.error,
                        'text': '',
                        'type': 'warning'
                    }];
                }
            }
        },
    },
    methods: {
        calcMargin(i) {
            return (i * 100) + 'px'
        },
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

            // this.deviceType = 'mobile'
        },
    }
}
</script>

<style scoped>

</style>
