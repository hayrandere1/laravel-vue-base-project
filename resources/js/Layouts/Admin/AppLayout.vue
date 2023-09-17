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
            <v-app-bar-title>
                {{ this.VITE_APP_NAME }}
            </v-app-bar-title>
            <template v-slot:append>
                <v-btn icon="mdi-heart"></v-btn>
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
                prepend-avatar="https://randomuser.me/api/portraits/men/78.jpg"
                title="John Leider"
            ></v-list-item>

            <v-divider></v-divider>

            <v-list density="compact" nav>
                <Link :href="route('admin.logout')" class="nav-item" method="POST" as="nav">
                    <v-list-item
                        prepend-icon="mdi-logout"
                        :title="trans('admin.layout.logout')"
                        value="home"
                    >
                    </v-list-item>
                </Link>
                <v-list-item prepend-icon="mdi-forum" title="About" value="about"></v-list-item>
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
                    prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
                    title="John Leider"
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
                    prepend-icon="mdi-Home"
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
        </v-main>
    </v-layout>

</template>

<script>
export default {
    name: "AppLayout",
    data() {
        return {
            alert: false,
            alerts: [],
            model: true,
            drawer: true,
            rightDrawer: false,
            deviceType: '',
            rail: false,
            VITE_APP_NAME: '',
            breadcrumbs: [
                {
                    title: 'Dashboard',
                    disabled: false,
                    href: 'breadcrumbs_dashboard',
                },
                {
                    title: 'Link 1',
                    disabled: false,
                    href: 'breadcrumbs_link_1',
                },
                {
                    title: 'Link 2',
                    disabled: true,
                    href: 'breadcrumbs_link_2',
                },
            ],
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

            this.deviceType = 'desktop'
        },
    },
    mounted() {
        this.setDeviceType();
        this.VITE_APP_NAME = import.meta.env.VITE_APP_NAME;
    }
}
</script>

<style scoped>

</style>
