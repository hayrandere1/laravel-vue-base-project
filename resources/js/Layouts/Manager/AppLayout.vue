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
    <v-layout class="rounded rounded-md">
        <v-app-bar clipped-left>
            <v-app-bar-nav-icon
                v-if="deviceType=='mobile'"
                @click.stop="drawer = !drawer"
            ></v-app-bar-nav-icon>
            <v-toolbar-title>
                {{ this.$page.props.appName }}
            </v-toolbar-title>
            <template v-slot:append>
                <v-btn icon="mdi-bell"></v-btn>
                <v-btn icon="mdi-magnify"></v-btn>
                <v-btn
                    @click.stop="rightDrawer = !rightDrawer"
                    icon="mdi-dots-vertical">
                </v-btn>
            </template>
        </v-app-bar>
        <v-navigation-drawer
            v-model="rightDrawer"
            location="right"
            temporary
        >
            <v-list-item
                :prepend-avatar="'https://www.gravatar.com/avatar/'+this.$page.props.user_email_md5"
                :title="this.$page.props.loginUser.first_name+' '+this.$page.props.loginUser.last_name"
                :subtitle="this.$page.props.loginUser.username"
            ></v-list-item>

            <v-divider></v-divider>

            <v-list density="compact" nav>
                <Link :href="route('manager.logout')" class="nav-item" method="POST" as="nav">
                    <v-list-item
                        prepend-icon="mdi-logout"
                        title="Logout"
                        value="logout"
                    >
                    </v-list-item>
                </Link>
            </v-list>
        </v-navigation-drawer>
        <v-navigation-drawer
            :permanent="deviceType=='desktop'||deviceType=='tablet'"
            :rail="rail"
            @click="rail = false"
            v-model="drawer"
        >
            <v-list>
                <v-list-item
                    :prepend-avatar="'https://www.gravatar.com/avatar/'+this.$page.props.user_email_md5"
                    :title="this.$page.props.loginUser.first_name+' '+this.$page.props.loginUser.last_name"
                    :subtitle="this.$page.props.loginUser.username"
                    nav
                >
                    <template v-slot:append
                              v-if="!rail && (deviceType=='tablet'||deviceType=='desktop')"
                    >
                        <v-btn
                            variant="text"
                            icon="mdi-chevron-left"
                            @click.stop="rail = !rail"
                        ></v-btn>
                    </template>
                </v-list-item>
                <v-divider></v-divider>
                <v-list-item
                    :href="route('manager.home')"
                    :active="route().current('manager.home')"
                    title="Home"
                    prepend-icon="mdi-home"
                    value="home"
                ></v-list-item>
                <v-list-item
                    v-if="can('manager.manager_role_group.index')"
                    :href="route('manager.manager_role_group.index')"
                    :active="route().current('manager.manager_role_group.*')"
                    title="Manager Role Group"
                    prepend-icon="mdi-shield-check"
                    value="manager_role_group"
                ></v-list-item>
                <v-list-item
                    v-if="can('manager.manager.index')"
                    :href="route('manager.manager.index')"
                    :active="route().current('manager.manager.*')"
                    title="Manager"
                    prepend-icon="mdi-account-supervisor"
                    value="manager"
                ></v-list-item>
                <v-list-item
                    v-if="can('manager.user_role_group.index')"
                    :href="route('manager.user_role_group.index')"
                    :active="route().current('manager.user_role_group.*')"
                    title="User Role Group"
                    prepend-icon="mdi-shield-check"
                    value="user_role_group"
                ></v-list-item>
                <v-list-item
                    v-if="can('manager.user.index')"
                    :href="route('manager.user.index')"
                    :active="route().current('manager.user.*')"
                    title="User"
                    prepend-icon="mdi-account-group"
                    value="user"
                ></v-list-item>
                <v-list-item
                    v-if="can('manager.archive.index')"
                    :href="route('manager.archive.index')"
                    :active="route().current('manager.archive.*')"
                    title="Archive"
                    prepend-icon="mdi-archive"
                    value="archive"
                ></v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-main class="align-center justify-center">
            <v-row justify="end" class="mt-1 mb-1">
                <v-breadcrumbs :items="breadcrumbs" class="pt-3 pe-5 pb-3">
                    <template v-slot:title="{ item }">
                        {{ item.title.toUpperCase() }}
                    </template>
                </v-breadcrumbs>
            </v-row>
            <slot></slot>
            <v-footer>
                <v-col class="text-center mt-4" cols="12">
                    <strong> {{ this.$page.props.appName }} </strong>{{ ' — ' + this.$page.props.appVersion }}
                </v-col>
            </v-footer>
        </v-main>
    </v-layout>

</template>

<script>
export default {
    name: "AppLayout",
    data() {
        return {
            drawer: true,
            rightDrawer: false,
            deviceType: '',
            rail: false,
            alert: false,
            alerts: [],
            breadcrumbs: [],
        }
    },
    watch: {
        '$page.props.breadcrumbs': {
            deep: true,
            handler(newValue, oldValue) {
                if (newValue !== []) {
                    this.breadcrumbs = newValue;
                }
            }
        },
        '$page.props.alert': {
            deep: true,
            handler(newValue, oldValue) {
                if (newValue !== []) {
                    this.alerts = newValue
                    this.alert = true;
                }
            }
        }
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

            // this.deviceType = 'tablet'
        },
        notificationEvent(event) {
            console.log(1,event);
        },
        userInfoEvent(event) {
            console.log(2,event);
        },
        ArchiveEvent(event) {
            console.log(3,event);
        },
    },
    mounted() {
        this.setDeviceType();
        window.Echo.private('manager.' + this.$page.props.loginUser.id)
            .listen('NotificationEvent', this.notificationEvent)
            .listen('UserInfo', this.userInfoEvent)
            .listen('ArchiveEvent', this.ArchiveEvent);
        // if (typeof window.usersChannel === 'undefined') {
        //     window.usersChannel = window.Echo.private('company.' + this.$page.props.loginUser.company_id)
        //         .listen('DeletedEvent', this.deletedEvent)
        //         .listen('UpdatedEvent', this.updatedEvent)
        // }

    }
}
</script>

<style scoped>

</style>
