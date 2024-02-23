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

                <v-menu
                    v-model="notificationMenu"
                    :close-on-content-click="false">
                    <template v-slot:activator="{ props }">
                        <v-btn
                            icon="true"
                            v-bind="props"
                        >
                            <v-badge :content="unRoadNotification" :max="9" overlap color="primary">
                        <v-icon>
                            mdi-bell
                        </v-icon>
                    </v-badge>
                </v-btn>
                    </template>
                    <v-card :flat="true">
                        <v-card-title>
                            Notifications
                        </v-card-title>
                        <v-card-item>
                            <template v-if="notifications.length==0">
                                No notifications
                            </template>
                            <template v-else>
                                <template v-for="(item,key) in notifications">
                                    <template v-if="key<3">
                                        <v-list-item
                                            :active="!item.is_read"
                                            v-on:click="(!item.is_read)?unRoadNotification--:'';item.is_read=true;"
                                            class="mt-3"
                                            variant="flat"
                                            :href="route('user.notification.show',item.id)"
                                        >
                                            <v-list-item-title>
                                                {{ item.title }}
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                {{ item.content }}
                                            </v-list-item-subtitle>
                                        </v-list-item>
                                        <v-divider class="border-opacity-100"></v-divider>
                                    </template>
                                </template>
                            </template>
                        </v-card-item>
                        <v-card-subtitle class="mb-3 mt-3">
                            <v-row>
                                <v-col>
                                    <a :href="route('admin.notification.index')"
                                       class="text-decoration-none">
                                        See all
                                    </a>
                                </v-col>
                                <v-col class="text-end">
                                    <a
                                        href="#"
                                        v-on:click="markAsRead"
                                        class="text-decoration-none">
                                        Mark as read
                                    </a>
                                </v-col>
                            </v-row>
                        </v-card-subtitle>
                    </v-card>
                </v-menu>
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
                <Link :href="route('admin.logout')" class="nav-item" method="POST" as="nav">
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
                    :href="route('admin.home')"
                    :active="route().current('admin.home')"
                    title="Home"
                    prepend-icon="mdi-home"
                    value="home"
                ></v-list-item>
                <v-list-group
                >
                    <template v-slot:activator="{ props }">
                        <v-list-item
                            :active="route().current('admin.admin_role_group.*')"
                            v-bind="props"
                            prepend-icon="mdi-account-circle"
                            title="Admins"
                        ></v-list-item>
                    </template>
                    <v-list-item
                        v-if="can('admin.admin_role_group.index')"
                        :href="route('admin.admin_role_group.index')"
                        :active="route().current('admin.admin_role_group.*')"
                        title="Admin Role Groups"
                        prepend-icon="mdi-shield-check"
                        value="admin_role_group"
                    ></v-list-item>
                    <v-list-item
                        v-if="can('admin.admin.index')"
                        :href="route('admin.admin.index')"
                        :active="route().current('admin.admin.*')"
                        title="Admin"
                        prepend-icon="mdi-badge-account-horizontal"
                        value="admin"
                    ></v-list-item>
                </v-list-group>

                <v-list-item
                    v-if="can('admin.company.index')"
                    :href="route('admin.company.index')"
                    :active="route().current('admin.company.*')"
                    title="Company"
                    prepend-icon="mdi-factory"
                    value="company"
                ></v-list-item>
                <v-list-item
                    v-if="can('admin.manager.index')"
                    :href="route('admin.manager.index')"
                    :active="route().current('admin.manager.*')"
                    title="Manager"
                    prepend-icon="mdi-account-supervisor"
                    value="manager"
                ></v-list-item>
                <v-list-item
                    v-if="can('admin.user.index')"
                    :href="route('admin.user.index')"
                    :active="route().current('admin.user.*')"
                    title="User"
                    prepend-icon="mdi-account-group"
                    value="user"
                ></v-list-item>
                <v-list-item
                    v-if="can('admin.package.index')"
                    :href="route('admin.package.index')"
                    :active="route().current('admin.package.*')"
                    title="Package"
                    prepend-icon="mdi-package"
                    value="package"
                ></v-list-item>
                <v-list-item
                    v-if="can('admin.archive.index')"
                    :href="route('admin.archive.index')"
                    :active="route().current('admin.archive.*')"
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
            <v-footer :app="true">
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
            notificationMenu: false,
            notifications: [],
            unRoadNotification: 0,
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
        markAsRead() {
            axios.get(route('admin.notification.mark_all_read')).then(response => {
                if (response.data.process){
                    this.notifications = response.data.notifications;
                    this.unRoadNotification = response.data.unread_count;
                }
            });
        },
        getNotification() {
            axios.get(route('admin.getNotifications')).then(response => {
                this.notifications = response.data.notifications;
                this.unRoadNotification = response.data.unread_count;
            });
        },
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
            this.notifications.unshift(event.notification);
            this.unRoadNotification++;
        },
        userInfoEvent(event) {
            console.log(2, event);
        },
        ArchiveEvent(event) {
            console.log(3, event);
        },
    },
    mounted() {
        this.setDeviceType();
        this.getNotification();
        window.Echo.private('admin.' + this.$page.props.loginUser.id)
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
